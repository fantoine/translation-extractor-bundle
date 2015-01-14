<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Extractor;

use Fantoine\TranslationExtractorBundle\Translation\Builder\PhpBuilder;
use Fantoine\TranslationExtractorBundle\Translation\Builder\TwigBuilder;
use Fantoine\TranslationExtractorBundle\Translation\Visitor\PhpVisitor;
use Fantoine\TranslationExtractorBundle\Translation\Visitor\TwigVisitor;
use Fantoine\TranslationExtractorBundle\Translation\Visitor\VisitorInterface;
use Fantoine\TranslationExtractorBundle\Translation\Visitor\VisitorManager;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;

/**
 * Description of AbstractExtractor
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
abstract class AbstractExtractor implements FileVisitorInterface
{
    /**
     * @var VisitorManager 
     */
    protected $visitorManager;
    
    /**
     * @param VisitorManager $visitorManager
     */
    public function setVisitorManager(VisitorManager $visitorManager)
    {
        $this->visitorManager = $visitorManager;
    }
    
    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     */
    public function visitFile(
        \SplFileInfo $file,
        MessageCatalogue $catalogue)
    {
        // Not yet supported
    }

    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     * @param \PHPParser_Node $ast
     */
    public function visitPhpFile(
        \SplFileInfo $file,
        MessageCatalogue $catalogue,
        array $ast)
    {
        $builder = $this->createPhpBuilder();
        if ($builder instanceof PhpBuilder) {
            $this->visitorManager->executeVisitor(
                $builder, $file, $catalogue, $ast
            );
        }
    }

    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     * @param \Twig_Node $ast
     */
    public function visitTwigFile(
        \SplFileInfo $file,
        MessageCatalogue $catalogue,
        \Twig_Node $ast)
    {
        $builder = $this->createTwigBuilder();
        if ($builder instanceof TwigBuilder) {
            $this->visitorManager->executeVisitor(
                $builder, $file, $catalogue, $ast
            );
        }
    }
    
    /**
     * @return PhpBuilder|null
     */
    abstract public function createPhpBuilder();
    
    /**
     * @return TwigBuilder|null
     */
    abstract public function createTwigBuilder();
}
