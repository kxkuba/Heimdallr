<?php
/**
 * Die Datei enthält die Klasse "{@link Transmitter_Transmitters}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Transmitter
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Dies ist ein Transmitter der andere Transmitter aufruft
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Transmitter
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Transmitter_Transmitters extends Heimdallr_TransmitterAbstract
{
    /**
     * Liste der Transmitter
     *
     * @var array
     */
    private $_transmitters = array();

    /**
     * Optional: Hinzufügen der Transmitter
     *
     * @param array $transmitters
     */
    public function __construct(array $transmitters)
    {
        foreach ($transmitters as $transmitter) {
            $this->addTransmitter($transmitter);
        }
    }

    /**
     * Fügt ein Transmitter hinzu
     *
     * @param Heimdallr_TransmitterInterface $transmitter
     */
    public function addTransmitter(Heimdallr_TransmitterInterface $transmitter)
    {
        $this->_transmitters[] = $transmitter;
    }

    /**
     * Löscht alle vorhandene Transmitter
     */
    public function clearTransmitter()
    {
        $this->_transmitters = array();
    }

    /**
     * Wird durch ein Event ausgelöst
     * Gibt die Nachricht an die Transmitter weiter
     *
     * @param Heimdallr_TemplateAbstract $template
     * @param string                  $message
     * @param string                  $key
     * @param float                   $value
     * @param float                   $limit
     */
    public function send(Heimdallr_TemplateAbstract $template, $message, $key, $value, $limit)
    {
        foreach ($this->_transmitters as $transmitter) {
            /* @var Heimdallr_TransmitterInterface $transmitter */
            try {
                $transmitter->send($template, $message, $key, $value, $limit);
            } catch (Exception $e) {
                Main::getInstance()->logException($e);
            }
        }
    }
}
