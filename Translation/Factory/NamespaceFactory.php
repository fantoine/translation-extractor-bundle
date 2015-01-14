<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory;

/**
 * Description of NamespaceFactory
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class NamespaceFactory extends AbstractFactory
{
    use ParentFactoryTrait;
    
    /**
     * @var array Allowed namespaces
     */
    protected $namespace = [];
    
    /**
     * @param string $namespace
     * @param boolean $andChildren
     * @return NamespaceFactory
     */
    public function in($namespace, $andChildren = true)
    {
        $this->namespace = [];
        
        return $this->orIn($namespace, $andChildren);
    }
    
    /**
     * @param string $namespace
     * @param boolean $andChildren
     * @return NamespaceFactory
     */
    public function orIn($namespace, $andChildren = true)
    {
        $this->namespace[] = '/^' . $this->escapeRegex($namespace) . '$/';
        if ($andChildren) {
            $this->namespace[] = '/^' . $this->escapeRegex($namespace) . '\\\\\\\\/';
        }
        return $this;
    }
    
    /**
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespace;
    }
    
    /**
     * @param string $pattern
     * @return string
     */
    protected function escapeRegex($pattern)
    {
        return preg_quote($pattern);
    }
    
    /**
     * @return string
     */
    public function extractedBy()
    {
        return 'namespace';
    }
}
