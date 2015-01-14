<?php

namespace Fantoine\TranslationExtractorBundle;

use Fantoine\TranslationExtractorBundle\DependencyInjection\Compiler\FactoryHandlerPass;
use Fantoine\TranslationExtractorBundle\DependencyInjection\Compiler\FileVisitorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * 
 */
class FantoineTranslationExtractorBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FileVisitorPass());
        $container->addCompilerPass(new FactoryHandlerPass());
    }
}
