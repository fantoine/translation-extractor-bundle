<?php

namespace Fantoine\TranslationExtractorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of FileVisitorPass
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class FileVisitorPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fantoine_translation_extractor.manager.visitor')) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds(
            'fantoine_translation_extractor.file_visitor'
        );
        foreach ($taggedServices as $id => $attributes) {
            $container
                ->findDefinition($id)
                ->addMethodCall(
                    'setVisitorManager',
                    [new Reference('fantoine_translation_extractor.manager.visitor')]
                )
            ;
        }
    }
}
