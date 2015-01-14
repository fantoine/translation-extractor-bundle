<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Builder;

use Fantoine\TranslationExtractorBundle\Translation\Factory\FileFactory;
use Fantoine\TranslationExtractorBundle\Translation\Factory\NamespaceFactory;

/**
 * Description of PhpBuilder
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class PhpBuilder extends AbstractBuilder
{   
    /**
     * @return string
     */
    public function getCacheDir()
    {
        return 'php';
    }
    
    /**
     * @return string
     */
    public function getVisitorClass()
    {
        return '\Fantoine\TranslationExtractorBundle\Translation\Visitor\PhpVisitor';
    }
    
    /**
     * @return string
     */
    public function getAstClass()
    {
        return '\PHPParser_Node';
    }
    
    /**
     * @param string $filename
     * @return FileFactory
     */
    public function file($filename = null)
    {
        $factory = new FileFactory($this);
        if (null !== $filename) {
            $factory->filename($filename);
        }
        return $factory;
    }
    
    /**
     * @param string $namespace
     * @return NamespaceFactory
     */
    public function namespace_($namespace = null)
    {
        $factory = new NamespaceFactory($this);
        if (null !== $namespace) {
            $factory->in($namespace);
        }
        return $factory;
    }
    
    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws LogicException
     */
    public function __call($name, array $arguments)
    {
        $escaped = ['namespace'];
        
        if (in_array($name, $escaped)) {
            return call_user_func_array([$this, $name.'_'], $arguments);
        }
        
        throw new \LogicException(sprintf('Method "%s" does not exist on PhpBuilder', $name));
    }
}
