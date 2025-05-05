<?php

namespace App;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class AppBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return '';
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('observed_icecast_sources')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('scheme')->defaultValue('https')->end()
                            ->scalarNode('host')->end()
                            ->scalarNode('status_path')->defaultValue('/status-json.xsl')->end()
                            ->scalarNode('source_path')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->setParameter('app.observed_icecast_sources', $config['observed_icecast_sources']);
    }
}
