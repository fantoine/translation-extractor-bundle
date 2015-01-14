<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory;

/**
 * Description of ParentFactoryTrait
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
trait ParentFactoryTrait
{
    /**
     * @var mixed
     */
    protected $parent;
    
    /**
     * @param mixed $parent
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
    }
    
    /**
     * @return mixed
     */
    public function end()
    {
        return $this->parent;
    }
}
