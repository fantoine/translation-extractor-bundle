<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Visitor;

use JMS\TranslationBundle\Model\MessageCatalogue;

/**
 * Description of AbstractVisitor
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
abstract class AbstractVisitor implements VisitorInterface
{
    /**
     * @var boolean
     */
    protected $visiting;
    
    /**
     * @var \SplFileInfo 
     */
    protected $file;
    
    /**
     * @var MessageCatalogue
     */
    protected $catalogue;
    
    /**
     * @var mixed
     */
    protected $ast;
    
    /**
     * @var array
     */
    protected $context;
    
    /**
     * @var array
     */
    protected $states;
    
    /**
     * 
     */
    public function __construct()
    {
        $this->visiting  = false;
    }
    
    /**
     * @return \SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * @return MessageCatalogue
     */
    public function getCatalogue()
    {
        return $this->catalogue;
    }
    
    /**
     * @return mixed
     */
    public function getAst()
    {
        return $this->ast;
    }
    
    /**
     * @return boolean
     */
    public function isVisiting()
    {
        return $this->visiting;
    }
    
    /**
     * @param \SplFileInfo $file
     * @return boolean
     */
    public function validate(\SplFileInfo $file)
    {
        return true;
    }
    
    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     * @param mixed $ast
     * @return boolean
     */
    public function visit(
        \SplFileInfo $file,
        MessageCatalogue $catalogue,
        $ast = null)
    {
        if ($this->visiting) {
            return;
        }
        
        $this->visiting = true;
        
        // Store items
        $this->file      = $file;
        $this->catalogue = $catalogue;
        $this->ast       = $ast;
        
        // Reset context
        $this->states = [];
        $this->resetContext();
        
        // Get result
        $result = $this->doVisit();
        
        $this->visiting = false;
        
        return $result;
    }
    
    /**
     * 
     */
    protected function resetContext()
    {
        $this->context = [];
    }
    
    /**
     * @return boolean
     */
    abstract protected function doVisit();
    
    /**
     * 
     */
    protected function extract()
    {
        
    }
    
    /**
     * @param string $state
     * @return AbstractVisitor
     */
    protected function addState($state)
    {
        $this->states[$state] = true;
        return $this;
    }
    
    /**
     * @param string $state
     * @return AbstractVisitor
     */
    protected function removeState($state)
    {
        unset($this->states[$state]);
        return $this;
    }
    
    /**
     * @param string $state
     * @return boolean
     */
    protected function hasState($state)
    {
        return array_key_exists($state, $this->states);
    }
    
    /**
     * @param array $states
     * @return boolean
     */
    protected function hasStates(array $states)
    {
        foreach ($states as $state) {
            if ($this->hasState($state)) {
                return false;
            }
        }
        return true;
    }
}
