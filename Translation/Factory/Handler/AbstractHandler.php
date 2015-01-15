<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory\Handler;

use Fantoine\TranslationExtractorBundle\Translation\Factory\FactoryInterface;

/**
 * Description of AbstractHandler
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class AbstractHandler implements HandlerInterface
{
    /**
     * @param string $fileAlias
     * @return \PHPParser_Node_Stmt|array|null
     */
    public function createValidation(FactoryInterface $factory, $fileAlias)
    {
        return null;
    }
    
    /**
     * @param FactoryInterface $factory
     * @return string|null
     */
    public function getAstClass(FactoryInterface $factory)
    {
        return null;
    }
    
    /**
     * @param FactoryInterface $factory
     * @param string $nodeAlias
     * @return \PHPParser_Node_Stmt|array|null
     */
    public function createExtractionValidation(FactoryInterface $factory, $nodeAlias)
    {
        return null;
    }
    
    /**
     * @param string $expression
     * @return \PHPParser_Node_Stmt
     */
    protected function parseExpression($expression)
    {
        // Get statements
        $stmts = $this->parseCode($expression);
        
        // Returns first statement
        return (count($stmts) > 0 ? $stmts[0] : null);
    }
    
    /**
     * @staticvar \PHPParser_Parser $parser
     * @param string $code
     * @return array
     */
    protected function parseCode($code)
    {
        static $parser = null;
        
        if (null === $parser) {
            $parser = new \PHPParser_Parser();
        }
        
        // Get statements
        try {
            return $parser->parse(new \PHPParser_Lexer(sprintf('<?php %s', $code)));
        } catch(\PHPParser_Error $e) {
            throw new \Exception(
                sprintf("%s :\n%s", $e->getMessage(), $code),
                0,
                null
            );
        }
    }
    
    /**
     * @param array $values
     * @return array
     */
    protected function escapeStringArray(array $values)
    {
        return array_map(
            function ($value) { return $this->escapeString($value); },
            $values
        );
    }
    
    /**
     * @param string $value
     * @return string
     */
    protected function escapeString($value)
    {
        return sprintf('"%s"', str_replace('"', '\\"', (string) $value));
    }
}
