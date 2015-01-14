<?php

namespace Fantoine\TranslationExtractorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of FactoryHandlerPass
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class FactoryHandlerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fantoine_translation_extractor.manager.visitor')) {
            return;
        }

        $definition = $container->getDefinition(
            'fantoine_translation_extractor.manager.visitor'
        );
        
        $taggedServices = $container->findTaggedServiceIds(
            'fantoine_translation_extractor.factory_handler'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addHandler',
                    [$attributes['alias'], new Reference($id)]
                );
            }
        }
    }
}
