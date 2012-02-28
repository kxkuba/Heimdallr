<?php
/**
 * Die Datei enthält das Interface "{@link Heimdallr_EventInterface}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 *
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
interface Heimdallr_EventInterface
{
    /**
     * Prüft, ob das definierte Ereigniss eintritt
     *
     * @param Heimdallr_TemplateAbstract $template
     * @param string $key
     * @param float  $value
     */
    public function verify(Heimdallr_TemplateAbstract $template, $key, $value);
}
