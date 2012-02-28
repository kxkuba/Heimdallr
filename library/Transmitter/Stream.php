<?php
/**
 * Die Datei enthält die Klasse "{@link Stream}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Transmitter
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 *
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Transmitter
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Transmitter_Stream extends Heimdallr_TransmitterAbstract
{
    /**
     * @var resource
     */
    private static $_stream = null;

    /**
     * Öffnet ein Verbidnung
     */
    public function __construct()
    {
        self::$_stream = @fopen('php://output', 'a', false);
        if (self::$_stream === false) {
            throw new Heimdallr_TransmitterException('Es konnte kein Verbindung aufgebaut werden.');
        }
    }

    /**
     * Schließt die Verbindung
     */
    public function __destruct()
    {
        @fclose(self::$_stream);
        self::$_stream = null;
    }

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
        // Parameter
        $paramsServer = array();

        // Art
        // Beispiel: Server
        $paramsServer['mode'] = ucfirst($template->getMode());

        // Servername
        // Beispiel: www.example.com
        $paramsServer['name'] = $template->getServer()->getName();

        // Beschreibung
        // Beispiel: Web-Server
        //$paramsServer['description'] = $template->getServer()->getDescription();

        // IP-Adresse
        // Beispiel: 127.0.0.1
        //$paramsServer['ip'] = $template->getServer()->getIp();

        // Type
        // Beispiel: Network
        if ($template->getMode() != $template->getRrdtool()->getType()) {
            $paramsServer['type'] = str_replace(' ', '', ucwords(str_replace('-', ' ', $template->getRrdtool()->getType())));
        }

        // Device (optional)
        // Beispiel: eth0
        if ($template->getDevice() != '') {
            $paramsServer['type'] .= ' ('.$template->getDevice().')';
        }

        // Key
        if ($template->getMode() != $key) {
            $paramsServer['key'] = $key;
        }

        $message = '['.implode(', ', $paramsServer).'] '.$this->getMessage($message, $value, $limit);
        @fwrite(self::$_stream, $message.PHP_EOL);
    }

}
