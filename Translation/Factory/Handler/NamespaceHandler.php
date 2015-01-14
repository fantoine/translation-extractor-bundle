<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory\Handler;

use Fantoine\TranslationExtractorBundle\Translation\Factory\FactoryInterface;
use Fantoine\TranslationExtractorBundle\Translation\Factory\NamespaceFactory;

/**
 * Description of NamespaceHandler
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class NamespaceHandler extends AbstractHandler
{
    /**
     * @param FactoryInterface $factory
     * @return string
     */
    public function getAstClass(FactoryInterface $factory) {
        return '\PHPParser_Node_Stmt_Namespace';
    }
    
    /**
     * @param FactoryInterface $factory
     * @return \PHPParser_Node_Stmt|array|null
     */
    public function createExtractionValidation(FactoryInterface $factory)
    {
        if (! $factory instanceof NamespaceFactory) {
            return null;
        }
        return $this->parseCode($this->getCode($factory));
    }
    
    /**
     * @param FileFactory $factory
     * @return string
     */
    protected function getCode(NamespaceFactory $factory)
    {
        $code = '';
        
        $namespaces = $factory->getNamespaces();
        if (count($namespaces) > 0) {
            $code .= sprintf('
                // Check namespace
                $namespace = $this->context["namespace"];
                if (!preg_match(%s, $namespace)) {
                    return false;
                }
                ',
                implode(', $namespace) && !preg_match(', $this->escapeStringArray($namespaces))
            );
        }
    
        // Final return
        $code .= 'return true;';
        
        return $code;
    }
}
