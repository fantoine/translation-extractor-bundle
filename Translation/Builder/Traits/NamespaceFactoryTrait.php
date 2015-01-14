<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Builder\Traits;

use Fantoine\TranslationExtractorBundle\Translation\Factory\ClassFactory;

/**
 * Description of NamespaceFactoryTrait
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
trait NamespaceFactoryTrait
{
    /**
     * @var array
     */
    protected $class = [];
    
    /**
     * @param array|string|null $instanceOf
     */
    public function forClass($instanceOf = null)
    {
        $this->class = [];
        
        return $this->orClass($instanceOf);
    }
    
    /**
     * @param array|string|null $instanceOf
     */
    public function orClass($instanceOf = null)
    {
        $factory = new ClassFactory($this);
        
        $this->class[] = [
            'factory'    => $factory,
            'instanceOf' => (null === $instanceOf ? null : (
                is_array($instanceOf) ? $instanceOf : [ (string) $instanceOf ]
            )),
        ];
        
        return $factory;
    }
    
    /**
     * @return array
     */
    public function getClass()
    {
        return $this->class;
    }
    
    /**
     * @param array|string|null $instanceOf
     */
    public function forInterface($instanceOf = null)
    {
        
    }
    
    /**
     * 
     */
    public function forTrait()
    {
        
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function matchClass($name)
    {
        $factories = [];
        $classes   = $this->getClass();
        
        foreach ($classes as $details) {
            // No need to check inheritance
            if (null === $details['instanceOf']) {
                $factories[] = $details['factory'];
                continue;
            }
            
            // Check if at least one inheritance is valid
            $ref = new \ReflectionClass($name);
            foreach ($details['instanceOf'] as $parent) {
                if ($ref->isSubclassOf($parent) || $ref->name === $parent) {
                    $factories[] = $details['factory'];
                    break;
                }
            }
        }
        
        return [
            (count($factories) > 0 || count($classes) === 0),
            $factories
        ];
    }
}
