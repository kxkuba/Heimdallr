<?php
/**
 * Die Datei enthält die Klasse "{@link Heimdallr_EventAbstract}"
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
abstract class Heimdallr_EventAbstract implements Heimdallr_EventInterface
{
    /**
     * @var string
     */
    private $_key = '';

    /**
     * @var mixed
     */
    private $_limit = null;

    /**
     * @var Heimdallr_TransmitterInterface
     */
    private $_transmitter = null;

    /**
     * Definiert den $key und den Transmitter
     *
     * @param Heimdallr_TransmitterInterface $transmitter
     * @param string   $key
     * @param null|int $limit
     */
    public function __construct(Heimdallr_TransmitterInterface $transmitter, $key, $limit = null)
    {
        $this->_key         = $key;
        $this->_limit       = $limit;
        $this->_transmitter = $transmitter;
    }

    /**
     * Gibt den Schlüssel zurück
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Gibt den definierten Wert zurück
     *
     * @return mixed|null
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Gibt den Transmitter zurück
     *
     * @return \Heimdallr_TransmitterInterface
     */
    public function getTransmitter()
    {
        return $this->_transmitter;
    }

    /**
     * Gibt die entsprechende Message zurück
     *
     * @return string
     */
    public function getMessage()
    {
        return Main::getInstance()->getConfig(array(
            'messages',
            'event',
            substr(get_class($this), strrpos(get_class($this), '_') + 1)
        ));
    }

    /**
     * Gibt die Nachricht an die Transmitter weiter
     *
     * @param  Heimdallr_TemplateAbstract $template
     * @param  string                  $message
     * @param  string                  $key
     * @param  float                   $value
     * @param  null|float              $limit
     */
    public function send(Heimdallr_TemplateAbstract $template, $message, $key, $value, $limit = null)
    {
        if ($limit === null) {
            $limit = $this->getLimit();
        }
        $this->getTransmitter()->send($template, $message, $key, $value, $limit);
    }

    /**
     * Gibt den Pfad und Dateiname der temporäre Datei von diesem Event zurück
     *
     * @param  Heimdallr_TemplateAbstract $template
     * @param  string                  $key
     * @return string
     */
    public function getPathTmp(Heimdallr_TemplateAbstract $template, $key)
    {
        $event = get_class($this);
        $mode  = 'event_'.substr($event, strrpos($event, '_') + 1).'_'.$template->getMode();
        $type  = str_replace(' ', '', ucwords(str_replace('-', ' ', $template->getRrdtool()->getType())));
        $name  = $template->getServer()->getName().'_'.$type;
        if ($template->getDevice() != '') {
            $name .= '_'.$template->getDevice();
        }
        return Main::getInstance()->getPathTmp($name.'_'.$key, $mode);
    }

}
