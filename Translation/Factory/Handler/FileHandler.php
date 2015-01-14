<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Factory\Handler;

use Fantoine\TranslationExtractorBundle\Translation\Factory\FactoryInterface;
use Fantoine\TranslationExtractorBundle\Translation\Factory\FileFactory;

/**
 * Description of FileHandler
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class FileHandler extends AbstractHandler
{
    /**
     * @param FactoryInterface $factory
     * @param string $fileAlias
     * @return \PHPParser_Node_Stmt|array|null
     */
    public function createValidation(FactoryInterface $factory, $fileAlias)
    {
        if (! $factory instanceof FileFactory) {
            return null;
        }
        return $this->parseCode($this->getCode($factory, $fileAlias));
    }
    
    /**
     * @param FileFactory $factory
     * @param $fileAlias
     * @return string
     */
    protected function getCode(FileFactory $factory, $fileAlias)
    {
        $code = '';
        
        // Add extension matching
        $extensions = $factory->getExtensions();
        if (count($extensions) > 0) {
            $code .= sprintf('
                // Check extension
                $extension = strtolower(%s->getExtension());
                if (!preg_match(%s, $extension)) {
                    return false;
                }
                ',
                $fileAlias,
                implode(', $extension) && !preg_match(', $this->escapeStringArray($extensions))
            );
        }
        
        // Add filename matching
        $filenames = $factory->getFilenames();
        if (count($filenames) > 0) {
            $code .= sprintf('
                // Check filename
                $filename = %s->getBasename(".".%s->getExtension());
                if (!preg_match(%s, $filename)) {
                    return false;
                }
                ',
                $fileAlias, $fileAlias,
                implode(', $filename) && !preg_match(', $this->escapeStringArray($filenames)) 
            );
        }
        
        // Final return
        $code .= 'return true;';
        
        return $code;
    }
}
