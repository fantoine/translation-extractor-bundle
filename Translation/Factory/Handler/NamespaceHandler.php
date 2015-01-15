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
     * @param string $nodeAlias
     * @return \PHPParser_Node_Stmt|array|null
     */
    public function createExtractionValidation(FactoryInterface $factory, $nodeAlias)
    {
        if (! $factory instanceof NamespaceFactory) {
            return null;
        }
        return $this->parseCode(
            $this->getCode($factory, $nodeAlias)
        );
    }
    
    /**
     * @param NamespaceFactory $factory
     * @param string $nodeAlias
     * @return string
     */
    protected function getCode(NamespaceFactory $factory, $nodeAlias)
    {
        $code = '';
        
        $namespaces = $factory->getNamespaces();
        if (count($namespaces) > 0) {
            $code .= sprintf('
                // Check namespace
                $namespace = implode("\\\\", %s->name->parts);
                if (!preg_match(%s, $namespace)) {
                    return false;
                }
                ',
                $nodeAlias,
                implode(', $namespace) && !preg_match(', $this->escapeStringArray($namespaces))
            );
        }
    
        // Final return
        $code .= 'return true;';
        
        return $code;
    }
}
