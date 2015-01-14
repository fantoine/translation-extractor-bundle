<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory;

/**
 * Description of FileFactory
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class FileFactory extends AbstractFactory
{
    use ParentFactoryTrait;
    
    /**
     * @var array Allowed filename patterns
     */
    protected $filename = [];
    
    /**
     * @var array Allowed file extension patterns
     */
    protected $extension = [];
    
    /**
     * @param string $name
     * @return FileFactory
     */
    public function filename($name)
    {
        $this->filename = [];
        return $this->orEq($name);
    }
    
    /**
     * @param string $name
     * @return FileFactory
     */
    public function orFilename($name)
    {
        $this->filename[] = '/^' . $this->escapeRegex($name) . '$/';
        return $this;
    }
    
    /**
     * @param string $pattern
     * @return FileFactory
     */
    public function startingWith($pattern)
    {
        $this->filename = [];
        return $this->orStartingWith($pattern);
    }
    
    /**
     * @param string $pattern
     * @return FileFactory
     */
    public function orStartingWith($pattern)
    {
        $this->filename[] = '/^' . $this->escapeRegex($pattern) . '/';
        return $this;
    }
    
    /**
     * @param string $pattern
     * @return FileFactory
     */
    public function endingWith($pattern)
    {
        $this->filename = [];
        return $this->orEndingWith($pattern);
    }
    
    /**
     * @param string $pattern
     * @return FileFactory
     */
    public function orEndingWith($pattern)
    {
        $this->filename[] = '/' . $this->escapeRegex($pattern) . '$/';
        return $this;
    }
    
    /**
     * @param string $name
     * @return FileFactory
     */
    public function extension($name)
    {
        $this->extension = [];
        return $this->orExtension($name);
    }
    
    /**
     * @param string $name
     * @return FileFactory
     */
    public function orExtension($name)
    {
        $this->extension[] = '/^' . $this->escapeRegex(strtolower($name)) . '$/';
        return $this;
    }
    
    /**
     * @return array
     */
    public function getExtensions()
    {
        return $this->extension;
    }
    
    /**
     * @return array
     */
    public function getFilenames()
    {
        return $this->filename;
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
    public function validatedBy()
    {
        return 'file';
    }
}
