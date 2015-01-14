<?php

namespace Fantoine\TranslationExtractorBundle\Translation\Visitor;

use JMS\TranslationBundle\Model\MessageCatalogue;

/**
 * Description of VisitorInterface
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
interface VisitorInterface
{
    /**
     * @param \SplFileInfo $file
     * @return boolean
     */
    public function validate(\SplFileInfo $file);
    
    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     * @param mixed $ast
     * @return boolean
     */
    public function visit(\SplFileInfo $file, MessageCatalogue $catalogue, $ast = null);
}
