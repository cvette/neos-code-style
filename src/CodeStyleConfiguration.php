<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\VariableNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class CodeStyleConfiguration
 *
 * @package Vette\Neos\CodeStyle
 */
class CodeStyleConfiguration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     *
     * @psalm-suppress PossiblyUndefinedMethod
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('codeStyle');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('defaultRuleSet')->end()
                ->scalarNode('defaultReport')->end()
                ->scalarNode('neosRoot')->end()
                ->arrayNode('files')->prototype('scalar')->end()->end()
                ->arrayNode('reports')
                    ->useAttributeAsKey('name')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('class')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('rules')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('class')->end()
                            ->enumNode('severity')->values(['info', 'warning', 'error'])->defaultValue('info')->end()
                            ->append($this->addOptionsNode())
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('ruleSets')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('include')->performNoDeepMerging()->prototype('scalar')->end()->end()
                            ->arrayNode('rules')->performNoDeepMerging()->prototype('scalar')->end()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    public function addOptionsNode(): ArrayNodeDefinition|VariableNodeDefinition|NodeDefinition|NodeBuilder|NodeParentInterface|null
    {
        $treeBuilder = new TreeBuilder('options');
        return $treeBuilder->getRootNode()
            ->useAttributeAsKey('name')->variablePrototype()
            ->end();
    }

}