<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Visitor;

use Fantoine\TranslationExtractorBundle\Translation\Builder\BuilderInterface;
use Fantoine\TranslationExtractorBundle\Translation\Factory\FactoryInterface;
use Fantoine\TranslationExtractorBundle\Translation\Factory\Handler\HandlerInterface;

/**
 * Description of VisitorGenerator
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class VisitorGenerator
{
    /**
     * @var array
     */
    protected $handlers;
    
    /**
     * 
     */
    public function __construct()
    {    
        $this->handlers = [];
    }
    
    /**
     * @param string $name
     * @param HandlerInterface $validator
     * @return VisitorManager
     */
    public function addHandler($name, HandlerInterface $validator)
    {
        $this->handlers[$name] = $validator;
        return $this;
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function hasHandler($name)
    {
        return array_key_exists($name, $this->handlers);
    }
    
    /**
     * @param string $name
     * @return HandlerInterface|null
     */
    public function getHandler($name)
    {
        return $this->handlers[$name];
    }
    
    /**
     * @param BuilderInterface $builder
     * @param string $className
     * @return string
     */
    public function generate(BuilderInterface $builder, $className)
    {
        // Prepare class factory
        $factory      = new \PHPParser_BuilderFactory();
        $classFactory = $factory
            ->class($className)
            ->extend($builder->getVisitorClass())
        ;
        
        // Get pre-validation code
        $classFactory->addStmts(
            $this->createValidation($builder, $factory)
        );
        
        // Get validation code
        $classFactory->addStmts(
            $this->createExtraction($builder, $factory)
        );
        
        // Generate PHP code
        return sprintf("<?php\n%s\n",
            (new \PHPParser_PrettyPrinter_Zend)->prettyPrint([ $classFactory->getNode() ])
        );
    }
    
    /**
     * @param BuilderInterface $builder
     * @param \PHPParser_BuilderFactory $factory
     * @return array
     */
    protected function createValidation(
        BuilderInterface $builder,
        \PHPParser_BuilderFactory $factory)
    {
        $stmts = [];
        
        // List all required pre-validations services
        $services = [];
        $this->extractServices($builder, true, $services);
        
        // Prepare conditions
        $lastExpression = null;
        foreach ($services as $service) {
            if (!$this->hasHandler($service['name'])) {
                throw new \LogicException(sprintf('The handler service "%s" does not exists.', $service['name']));
            }
            
            // Call service to get validation method content
            $handler    = $this->getHandler($service['name']);
            $statements = $handler->createValidation(
                $service['factory'], '$file'
            );
            
            // Prepare method
            $methodName = 'validate_' . sha1($service['name'] . '-' . $service['factory']->getHash());
            $method     = $factory
                ->method($methodName)
                ->addParam($factory->param('file')->setTypeHint('\SplFileInfo'))
            ;
            if (null === $statements) {
                continue;
            }
            
            $stmts[] = $method;

            // Fill method
            $method->addStmts(is_array($statements) ? $statements : [$statements]);

            // Prepare condition expression: $this->validate_<...>($this->getFile())
            $expression = new \PHPParser_Node_Expr_MethodCall(
                new \PHPParser_Node_Expr_Variable('this'),
                $methodName,
                [
                    new \PHPParser_Node_Arg(new \PHPParser_Node_Expr_MethodCall(
                        new \PHPParser_Node_Expr_Variable('this'),
                        'getFile'
                    ))
                ]
            );
            if (null === $lastExpression) {
                $lastExpression = $expression;
            } else {
                $lastExpression = new \PHPParser_Node_Expr_LogicalAnd(
                    $lastExpression,
                    $expression
                );
            }
        }
        
        // If there is validation to do, add method
        if (count($stmts) > 0) {
            $stmts[] = $factory
                ->method('validate')
                ->addParam($factory->param('file')->setTypeHint('\SplFileInfo'))
                ->addStmt(new \PHPParser_Node_Stmt_Return($lastExpression))
            ;
        }
        
        return $stmts;
    }
    
    /**
     * @param BuilderInterface $builder
     * @param \PHPParser_BuilderFactory $factory
     * @return array
     */
    protected function createExtraction(
        BuilderInterface $builder,
        \PHPParser_BuilderFactory $factory)
    {
        $stmts      = [];
        $enterStmts = [];
        $leaveStmts = [];
        
        foreach ($builder->children() as $child) {
            $service  = $child->extractedBy();
            if (null === $service) {
                continue;
            }
            
            // Get handler service
            if (!$this->hasHandler($service)) {
                throw new \LogicException(sprintf('The handler service "%s" does not exists.', $service));
            }
            $handler  = $this->getHandler($service);
            
            // Get validation condition
            $validationCondition = null;
            $statements = $handler->createExtractionValidation($child);
            if (null !== $statements) {    
                // Prepare method
                $methodName = 'enterNode_' . $child->getHash();
                $method     = $factory
                    ->method($methodName)
                    ->addParam($factory->param('node')->setTypeHint($builder->getAstClass()))
                    ->addStmts(is_array($statements) ? $statements : [$statements])
                ;

                // Prepare condition expression: $this->enterNode_<...>($node)
                $validationCondition = new \PHPParser_Node_Expr_MethodCall(
                    new \PHPParser_Node_Expr_Variable('this'),
                    $methodName,
                    [ new \PHPParser_Node_Arg(new \PHPParser_Node_Expr_Variable('node')) ]
                );
            
                $stmts[] = $method;
            }
            
            // Get AST condition
            $astClass = $handler->getAstClass($child);
            $astCondition = null;
            if (null !== $astClass) {
                $astCondition = new \PHPParser_Node_Expr_Instanceof(
                    new \PHPParser_Node_Expr_Variable('node'),
                    new \PHPParser_Node_Name($astClass)
                );
            }
            
            // Prepare final condition
            $enterCondition = (null !== $astCondition && null !== $validationCondition ?
                new \PHPParser_Node_Expr_LogicalAnd($astCondition, $validationCondition) :
                $astCondition ?: $validationCondition
            );
            
            // Prepare addState statement
            $addState = new \PHPParser_Node_Expr_MethodCall(
                new \PHPParser_Node_Expr_Variable('this'),
                'addState',
                [ new \PHPParser_Node_Arg(new \PHPParser_Node_Scalar_String($child->getHash())) ]
            );
            
            // Prepare removeState statement
            $removeState = new \PHPParser_Node_Expr_MethodCall(
                new \PHPParser_Node_Expr_Variable('this'),
                'removeState',
                [ new \PHPParser_Node_Arg(new \PHPParser_Node_Scalar_String($child->getHash())) ]
            );
            
            // Add enter condition
            $enterStmts[] = (null !== $enterCondition ?
                new \PHPParser_Node_Stmt_If($enterCondition, [ 'stmts' => [$addState] ]) :
                $addState
            );
            
            // Add remove condition
            if (null !== $astCondition) {
                $leaveStmts[] = new \PHPParser_Node_Stmt_If($astCondition, [ 'stmts' => [$removeState] ]);
            }
        }
        
        // If there is extraction to do, add methods
        if (count($enterStmts) >= 0) {
            $stmts[] = $factory
                ->method('enterNode')
                ->addParam($factory->param('node')->setTypeHint($builder->getAstClass()))
                ->addStmt(new \PHPParser_Node_Expr_StaticCall(
                    new \PHPParser_Node_Name('parent'),
                    'enterNode',
                    [ new \PHPParser_Node_Arg(new \PHPParser_Node_Expr_Variable('node')) ]
                ))
                ->addStmts($enterStmts)
                ->addStmt(new \PHPParser_Node_Expr_MethodCall(
                    new \PHPParser_Node_Expr_Variable('this'),
                    'extract'
                ))
            ;
        }
        if (count($leaveStmts) >= 0) {
            $stmts[] = $factory
                ->method('leaveNode')
                ->addParam($factory->param('node')->setTypeHint($builder->getAstClass()))
                ->addStmt(new \PHPParser_Node_Expr_StaticCall(
                    new \PHPParser_Node_Name('parent'),
                    'leaveNode',
                    [ new \PHPParser_Node_Arg(new \PHPParser_Node_Expr_Variable('node')) ]
                ))
                ->addStmts($leaveStmts)
            ;
        }
        
        return $stmts;
    }
    
    /**
     * @param FactoryInterface $factory
     * @param boolean $validation
     * @param array $services
     */
    protected function extractServices(
        FactoryInterface $factory,
        $validation,
        array &$services)
    {
        // Extract service
        $service = ($validation ? $factory->validatedBy() : $factory->extractedBy());
        if (null !== $service) {
            $services[] = [
                'name'    => $service,
                'factory' => $factory,
            ];
        }
        
        // Go to children
        foreach ($factory->children() as $child) {
            $this->extractServices($child, $validation, $services);
        }
    }
}
