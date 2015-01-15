<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory\Handler;

use Fantoine\TranslationExtractorBundle\Translation\Factory\ClassFactory;
use Fantoine\TranslationExtractorBundle\Translation\Factory\FactoryInterface;

/**
 * Description of ClassHandler
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class ClassHandler extends AbstractHandler
{
    /**
     * @param FactoryInterface $factory
     * @return string
     */
    public function getAstClass(FactoryInterface $factory) {
        return '\PHPParser_Node_Stmt_Class';
    }
    
    /**
     * @param FactoryInterface $factory
     * @param string $nodeAlias
     * @return \PHPParser_Node_Stmt|array|null
     */
    public function createExtractionValidation(FactoryInterface $factory, $nodeAlias)
    {
        if (! $factory instanceof ClassFactory) {
            return null;
        }
        return $this->parseCode(
            $this->getCode($factory, $nodeAlias)
        );
    }
    
    /**
     * @param ClassFactory $factory
     * @param string $nodeAlias
     * @return string
     */
    protected function getCode(ClassFactory $factory, $nodeAlias)
    {
        $code = sprintf('
            $className = ($this->context["namespace"] === "" ? "" : $this->context["namespace"] . "\\\\") . %s->name;
            if (!class_exists($className)) {
                return false;
            }
        ', $nodeAlias);
        
        $subclasses = $factory->getSubclasses();
        if (count($subclasses) > 0) {
            $code .= sprintf('
                // Check subclasses
                $ref = new \ReflectionClass($className);
                if (%s) {
                    return false;
                }
                ',
                implode(' && ', array_map(function ($subclass) {
                    return sprintf(
                        '!$ref->isSubclassOf(%s) && $ref->name !== %s',
                        $subclass,
                        $subclass
                    );
                }, $this->escapeStringArray($subclasses)))
            );
        }
        
        $extends = $factory->getExtends();
        if (count($extends) > 0) {
            $code .= sprintf('
                // Check inherited classes
                $parentClasses = class_parents($className);
                if (!in_array(%s, $parentClasses)) {
                    return false;
                }
                ',
                implode(', $parentClasses) && !in_array(', $this->escapeStringArray($extends))
            );
        }
        
        $implements = $factory->getImplements();
        if (count($implements) > 0) {
            $code .= sprintf('
                // Check implemented interfaces
                $parentInterfaces = class_implements($className);
                if (!in_array(%s, $parentInterfaces)) {
                    return false;
                }
                ',
                implode(', $parentInterfaces) && !in_array(', $this->escapeStringArray($implements))
            );
        }
    
        // Final return
        $code .= 'return true;';
        
        return $code;
    }
}
