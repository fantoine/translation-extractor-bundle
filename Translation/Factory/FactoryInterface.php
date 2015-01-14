<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory;

/**
 * Description of FactoryInterface
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
interface FactoryInterface
{
    /**
     * @param FactoryInterface $factory
     * @return FactoryInterface
     */
    public function add(FactoryInterface $factory);
    /**
     * @return array
     */
    public function children();
    
    /**
     * @return string|null
     */
    public function validatedBy();
    
    /**
     * @return string|null
     */
    public function extractedBy();
    
    /**
     * @return string
     */
    public function getHash();
}
