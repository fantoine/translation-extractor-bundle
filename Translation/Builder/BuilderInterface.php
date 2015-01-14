<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Builder;

use Fantoine\TranslationExtractorBundle\Translation\Factory\FactoryInterface;

/**
 * Description of BuilderInterface
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
interface BuilderInterface extends FactoryInterface
{
    /**
     * @return string
     */
    public function getCacheDir();
    
    /**
     * @return string
     */
    public function getVisitorClass();
    
    /**
     * @return string
     */
    public function getAstClass();
}
