<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory;

/**
 * Description of AbstractFactory
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class AbstractFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $factories = [];
    
    /**
     * @var string
     */
    protected $hash;
    
    /**
     * @param FactoryInterface $factory
     * @return FactoryInterface
     */
    public function add(FactoryInterface $factory)
    {
        $this->factories[] = $factory;
        return $this;
    }
    
    /**
     * @return array
     */
    public function children()
    {
        return $this->factories;
    }
    
    /**
     * @return string|null
     */
    public function validatedBy()
    {
        return null;
    }
    
    /**
     * @return string|null
     */
    public function extractedBy()
    {
        return null;
    }
    
    /**
     * @return string
     */
    public function getHash()
    {
        if (null === $this->hash) {
            $this->hash = sha1(get_class($this) . '@' . serialize($this));
        }
        return $this->hash;
    }
}
