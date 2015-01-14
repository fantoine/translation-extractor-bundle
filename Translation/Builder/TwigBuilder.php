<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Builder;

/**
 * Description of TwigBuilder
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class TwigBuilder extends AbstractBuilder
{
    /**
     * @return PhpFileFactory
     */
    public function createFactory()
    {
        // TODO
        return null;
    }
    
    /**
     * @return string
     */
    public function getCacheDir()
    {
        return 'twig';
    }
    
    /**
     * @return string
     */
    public function getVisitorClass()
    {
        return '\Fantoine\TranslationExtractorBundle\Translation\Visitor\TwigVisitor';
    }
    
    /**
     * @return string
     */
    public function getAstClass()
    {
        return '\Twig_Node';
    }
}
