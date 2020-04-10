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
    public const TRANSPORTS_FIELD = 'transports';
    public const PARTNER_CREATED_TRANSPORT = 'partner_created';
    public const PARTNER_REMOVED_TRANSPORT = 'partner_removed';
    public const EXCHANGE_NAME_FIELD = 'exchange_name';
    public const ROUTING_KEY_FIELD = 'routing_key';
    public const QUEUE_FIELD = 'queue';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cvek_partner');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('dsn')
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
