<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Builder;

use Fantoine\TranslationExtractorBundle\Translation\Factory\AbstractFactory;

/**
 * Description of AbstractBuilder
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
abstract class AbstractBuilder extends AbstractFactory implements BuilderInterface
{
    /**
     * @return string
     */
    abstract public function getCacheDir();
    
    /**
     * @return string
     */
    abstract public function getVisitorClass();
}
