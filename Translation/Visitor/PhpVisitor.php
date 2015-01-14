<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Visitor;

/**
 * Description of PhpVisitor
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class PhpVisitor extends AbstractVisitor implements \PHPParser_NodeVisitor
{
    /**
     * 
     */
    protected function resetContext()
    {
        $this->context = [
            'namespace' => '',
            'use'       => [],
        ];
    }
    
    /**
     * @return boolean
     */
    public function doVisit()
    {
        // Traverse nodes
        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor($this);
        $traverser->traverse($this->ast);
        
        return true;
    }
    
    /**
     * @param array $nodes
     */
    public function afterTraverse(array $nodes)
    {
        // Nothing to do...
    }

    /**
     * @param array $nodes
     */
    public function beforeTraverse(array $nodes)
    {
        // Nothing to do...
    }

    /**
     * @param \PHPParser_Node $node
     */
    public function leaveNode(\PHPParser_Node $node)
    {
        // namespace ... { }
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->context['namespace'] = '';
            $this->context['use']       = [];
        }
    }

    /**
     * @param \PHPParser_Node $node
     */
    public function enterNode(\PHPParser_Node $node)
    {
        // namespace ... { }
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->context['namespace'] = implode('\\', $node->name->parts);
        }
        
        if ($node instanceof \PHPParser_Node_Stmt_Use) {
            foreach ($node->uses as $useNode) {
                $this->context['use'][$useNode->alias] = implode('\\', $useNode->name->parts);
            }
        }
        
        if ($node instanceof \PHPParser_Node_Stmt_UseUse) {
            $this->context['use'][$node->alias] = implode('\\', $node->name->parts);
        }
    }
}
