<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory\Handler;

use Fantoine\TranslationExtractorBundle\Translation\Factory\FactoryInterface;

/**
 * Description of HandlerInterface
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
interface HandlerInterface
{
    /**
     * @param FactoryInterface $factory
     * @param string $fileAlias
     * @return \PHPParser_Node_Stmt|array|null
     */
    public function createValidation(FactoryInterface $factory, $fileAlias);
    
    /**
     * @param FactoryInterface $factory
     * @return string|null
     */
    public function getAstClass(FactoryInterface $factory);
    
    /**
     * @param FactoryInterface $factory
     * @return \PHPParser_Node_Stmt|array|null
     */
    public function createExtractionValidation(FactoryInterface $factory);
}
