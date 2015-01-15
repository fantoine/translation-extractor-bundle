<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory;

/**
 * Description of ClassFactory
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class ClassFactory extends AbstractFactory
{
    use ParentFactoryTrait;
    
    /**
     * @var array Array of possible parent classes/interfaces
     */
    protected $subclass = [];
    
    /**
     * @var array Array of possible parent classes
     */
    protected $extend = [];
    
    /**
     * @var array Array of possible parent interfaces
     */
    protected $implement = [];
    
    /**
     * @param string $class
     * @return ClassFactory
     */
    public function subclass($class)
    {
        $this->subclass = [];
        return $this->orSubclass($class);
    }
    
    /**
     * @param string $class
     * @return ClassFactory
     */
    public function orSubclass($class)
    {
        $this->subclass[] = $class;
        return $this;
    }
    
    /**
     * @param string $class
     * @return ClassFactory
     */
    public function extend($class)
    {
        $this->extend = [];
        return $this->orExtend($class);
    }
    
    /**
     * @param string $class
     * @return ClassFactory
     */
    public function orExtend($class)
    {
        $this->extend[] = $class;
        return $this;
    }
    
    /**
     * @param string $class
     * @return ClassFactory
     */
    public function implement($class)
    {
        $this->implement = [];
        return $this->orImplement($class);
    }
    
    /**
     * @param string $class
     * @return ClassFactory
     */
    public function orImplement($class)
    {
        $this->implement[] = $class;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getSubclasses()
    {
        return $this->subclass;
    }
    
    /**
     * @return array
     */
    public function getExtends()
    {
        return $this->extend;
    }
    
    /**
     * @return array
     */
    public function getImplements()
    {
        return $this->implement;
    }
    
    /**
     * @return string
     */
    public function extractedBy()
    {
        return 'class';
    }
}
