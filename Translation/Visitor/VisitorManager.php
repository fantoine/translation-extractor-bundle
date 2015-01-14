<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Visitor;

use Fantoine\TranslationExtractorBundle\Translation\Builder\BuilderInterface;
use Fantoine\TranslationExtractorBundle\Translation\Factory\FactoryInterface;
use Fantoine\TranslationExtractorBundle\Translation\Factory\Handler\HandlerInterface;
use JMS\TranslationBundle\Model\MessageCatalogue;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Description of VisitorManager
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class VisitorManager
{
    /**
     * @var Generator
     */
    protected $generator;
    
    /**
     * @var string
     */
    protected $cacheDir;
    
    /**
     * @var Filesystem
     */
    protected $filesystem;
    
    /**
     * @param $cacheDir
     */
    public function __construct(VisitorGenerator $generator, $cacheDir)
    {
        $this->generator  = $generator;
        $this->cacheDir   = $cacheDir;
        $this->filesystem = new Filesystem();
    }
    
    /**
     * @param PhpBuilder $builder
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     * @param mixed $ast
     * @return boolean
     */
    public function executeVisitor(
        BuilderInterface $builder,
        \SplFileInfo $file,
        MessageCatalogue $catalogue,
        $ast = null)
    {
        // Get visitor
        $visitor = $this->findVisitor($builder);
        if (! $visitor instanceof VisitorInterface) {
            return false;
        }
        
        // Execute visitor
        return $visitor->visit($file, $catalogue, $ast);
    }
    
    /**
     * @param BuilderInterface $builder
     * @return VisitorInterface|null
     */
    public function findVisitor(BuilderInterface $builder)
    {
        // Create cache filename
        $classname = $this->getBuilderCacheClass($builder); 
        $filename  = $this->getBuilderCacheFile($builder);
        
        // If cache file doesn't exist, create it
        if (!$this->hasVisitor($builder)) {
            $this->createVisitor($builder);
        }
        
        // Require file
        require_once $filename;
        
        // Return visitor
        $visitor = new $classname;
        return ($visitor instanceof VisitorInterface ? $visitor : null);
    }
    
    /**
     * @param BuilderInterface $builder
     */
    public function createVisitor(BuilderInterface $builder)
    {
        // Make sure the cache directory exists
        $directory = $this->getBuilderCacheDirectory($builder);
        $this->filesystem->mkdir($directory);
        
        // Get visitor code
        $code = $this->generator->generate(
            $builder, $this->getBuilderCacheClass($builder)
        );
        
        // Dump code in file
        $this->filesystem->dumpFile(
            $this->getBuilderCacheFile($builder),
            $code
        );
    }
    
    /**
     * @param BuilderInterface $builder
     * @return boolean
     */
    public function hasVisitor(BuilderInterface $builder)
    {
        $filename = $this->getBuilderCacheFile($builder);
        return $this->filesystem->exists($filename);
    }
    
    /**
     * @param BuilderInterface $builder
     * @return string
     */
    public function getBuilderCacheClass(BuilderInterface $builder)
    {
        return 'Visitor_' . $builder->getHash();
    }
    
    /**
     * @param BuilderInterface $builder
     * @return string
     */
    public function getBuilderCacheDirectory(BuilderInterface $builder)
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . $builder->getCacheDir();
    }
    
    /**
     * @param BuilderInterface $builder
     * @return string
     */
    public function getBuilderCacheFile(BuilderInterface $builder)
    {
        return sprintf('%s%s%s.php',
            $this->getBuilderCacheDirectory($builder),
            DIRECTORY_SEPARATOR,
            $this->getBuilderCacheClass($builder)
        );
    }
}
