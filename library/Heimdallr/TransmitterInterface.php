<?php
/**
 * Die Datei enthält das Interface "{@link Heimdallr_TransmitterInterface}"
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
interface Heimdallr_TransmitterInterface
{
    /**
     * Wird durch ein Event ausgelöst
     *
     * @abstract
     * @param Heimdallr_TemplateAbstract $template
     * @param string                  $message
     * @param string                  $key
     * @param float                   $value
     * @param float                   $limit
     */
    public function send(Heimdallr_TemplateAbstract $template, $message, $key, $value, $limit);
}
