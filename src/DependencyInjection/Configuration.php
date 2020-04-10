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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const DSN_FIELD = 'dsn';
    public const USE_BUILTIN_CREATOR_FIELD = 'use_builtin_creator';
    public const USE_BUILTIN_REMOVER_FIELD = 'use_builtin_remover';
    public const PARTNER_CLASS_FIELD = 'partner_class';
    public const TRANSPORTS_FIELD = 'transports';
    public const FAILURE_TRANSPORT_FIELD = 'failure_transport';
    public const EXCHANGE_NAME_FIELD = 'exchange_name';
    public const ROUTING_KEY_FIELD = 'routing_key';
    public const QUEUE_FIELD = 'queue';
    public const PARTNER_CREATED_TRANSPORT = 'partner_created';
    public const PARTNER_REMOVED_TRANSPORT = 'partner_removed';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cvek_partner');
        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode(self::USE_BUILTIN_CREATOR_FIELD)
                    ->info('Use builtin handler to create new partner')
                    ->defaultTrue()
                ->end()
            ->end()
            ->children()
                ->booleanNode(self::USE_BUILTIN_REMOVER_FIELD)
                    ->info('Use builtin handler to remove partner')
                    ->defaultTrue()
                ->end()
            ->end()
            ->children()
                ->scalarNode(self::PARTNER_CLASS_FIELD)
                    ->info('FQN of partner class to be able to persist and remove it')
                    ->defaultValue('App\Entity\Partner')
                ->end()
            ->end()
            ->children()
                ->scalarNode(self::FAILURE_TRANSPORT_FIELD)
                    ->info('Transport to catch error messages')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
            ->children()
                ->scalarNode(self::DSN_FIELD)
                    ->info('Dsn to connect to channel')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
            ->children()
                ->arrayNode(self::TRANSPORTS_FIELD)
                    ->isRequired()
                    ->children()
                        ->arrayNode(self::PARTNER_CREATED_TRANSPORT)
                            ->isRequired()
                            ->children()
                                ->scalarNode(self::EXCHANGE_NAME_FIELD)
                                    ->info('Name of exchange where partner_created event will be published')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode(self::ROUTING_KEY_FIELD)
                                    ->info('Routing key where partner_created event will be published')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode(self::QUEUE_FIELD)
                                    ->info('Queue to catch an event')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode(self::PARTNER_REMOVED_TRANSPORT)
                            ->isRequired()
                            ->children()
                                ->scalarNode(self::EXCHANGE_NAME_FIELD)
                                    ->info('Name of exchange where partner_removed event will be published')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode(self::ROUTING_KEY_FIELD)
                                    ->info('Routing key where partner_removed event will be published')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode(self::QUEUE_FIELD)
                                    ->info('Queue to catch an event')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
