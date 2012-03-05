<?php
/**
 * Die Datei enthält die Klasse "{@link Transmitter_Log}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Transmitter
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Logt die Events
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Transmitter
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Transmitter_Log extends Heimdallr_TransmitterAbstract
{
    /**
     * Wird durch ein Event ausgelöst
     *
     * @param Heimdallr_TemplateAbstract $template
     * @param string                  $message
     * @param string                  $key
     * @param float                   $value
     * @param float                   $limit
     */
    public function send(Heimdallr_TemplateAbstract $template, $message, $key, $value, $limit)
    {
        $message = $this->getMessage($message, $value, $limit);
        $file    = $this->getPathLog($template, $key);
        $content = date('Y-m-d_H-iP').' - '.$template->getServer()->getName().': '.$message;
        file_put_contents($file, file_get_contents($file).$content.PHP_EOL);
    }

}
