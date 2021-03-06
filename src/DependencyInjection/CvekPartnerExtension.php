<?php
/*
 * This file is part of the Aqua Delivery package.
 *
 * (c) Sergey Logachev <svlogachev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cvek\PartnerBundle\DependencyInjection;

use Cvek\PartnerBundle\Messenger\Message\ParseErrorMessage;
use Cvek\PartnerBundle\Messenger\Message\PartnerAlreadyExistsErrorMessage;
use Cvek\PartnerBundle\Messenger\Message\PartnerCreatedMessage;
use Cvek\PartnerBundle\Messenger\Message\PartnerRemovedMessage;
use Cvek\PartnerBundle\Messenger\Serializer\PartnerCreatedSerializer;
use Cvek\PartnerBundle\Messenger\Serializer\PartnerRemovedSerializer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class CvekPartnerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        /** @var Configuration $configuration */
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('cvek_partner_created_handler');
        $definition->setArgument(2, $config[Configuration::PARTNER_CLASS_FIELD]);
        $definition->setArgument(3, $config[Configuration::USE_BUILTIN_CREATOR_FIELD]);

        $definition = $container->getDefinition('cvek_partner_removed_handler');
        $definition->setArgument(1, $config[Configuration::PARTNER_CLASS_FIELD]);
        $definition->setArgument(2, $config[Configuration::USE_BUILTIN_REMOVER_FIELD]);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $frameworkBundle = $container->getParameter('kernel.bundles')['FrameworkBundle'] ?? null;
        if (!isset($frameworkBundle)) {
            throw new \RuntimeException('Partner bundle: Framework bundle is not available!');
        }

        $configs = $container->getExtensionConfig($this->getAlias());
        /** @var Configuration $configuration */
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $partnerCreatedConfig = $config[Configuration::TRANSPORTS_FIELD][Configuration::PARTNER_CREATED_TRANSPORT];
        $partnerRemovedConfig = $config[Configuration::TRANSPORTS_FIELD][Configuration::PARTNER_REMOVED_TRANSPORT];

        $messengerConfig = [
            'messenger' => [
                'transports' => [
                    Configuration::PARTNER_CREATED_TRANSPORT => [
                        'dsn' => $config[Configuration::DSN_FIELD],
                        'serializer' => PartnerCreatedSerializer::class,
                        'retry_strategy' => [
                            'max_retries' => 0,
                        ],
                        'options' => [
                            'exchange' => [
                                'name' => $partnerCreatedConfig[Configuration::EXCHANGE_NAME_FIELD],
                                'type' => 'direct',
                            ],
                            'queues' => [
                                $partnerCreatedConfig[Configuration::QUEUE_FIELD] => [
                                    'binding_keys' => [$partnerCreatedConfig[Configuration::ROUTING_KEY_FIELD]],
                                ]
                            ],
                        ],
                    ],
                    Configuration::PARTNER_REMOVED_TRANSPORT => [
                        'dsn' => $config[Configuration::DSN_FIELD],
                        'serializer' => PartnerRemovedSerializer::class,
                        'retry_strategy' => [
                            'max_retries' => 0,
                        ],
                        'options' => [
                            'exchange' => [
                                'name' => $partnerRemovedConfig[Configuration::EXCHANGE_NAME_FIELD],
                                'type' => 'direct',
                            ],
                            'queues' => [
                                $partnerRemovedConfig[Configuration::QUEUE_FIELD] => [
                                    'binding_keys' => [$partnerRemovedConfig[Configuration::ROUTING_KEY_FIELD]],
                                ]
                            ],
                        ],
                    ],
                ],
                'routing' => [
                    PartnerCreatedMessage::class => Configuration::PARTNER_CREATED_TRANSPORT,
                    PartnerRemovedMessage::class => Configuration::PARTNER_REMOVED_TRANSPORT,
                    ParseErrorMessage::class => $config[Configuration::FAILURE_TRANSPORT_FIELD],
                    PartnerAlreadyExistsErrorMessage::class => $config[Configuration::FAILURE_TRANSPORT_FIELD],
                ],
            ],
        ];

        $container->prependExtensionConfig('framework', $messengerConfig);
    }
}
