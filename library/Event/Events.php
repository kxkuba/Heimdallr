<?php
/**
 * Die Datei enthält die Klasse "{@link Event_Events}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Event
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Dies ist ein Event das andere Events aufruft
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Event
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Event_Events extends Heimdallr_EventAbstract
{
    private $_events = array();

    /**
     * Definiert den $key und den Transmitter
     *
     * @param array $events
     */
    public function __construct(array $events = array())
    {
        foreach ($events as $event) {
            $this->addEvents($event);
        }
    }

    /**
     * Fügt ein Event der Liste hinzu
     *
     * @param Heimdallr_EventInterface $event
     */
    public function addEvents(Heimdallr_EventInterface $event)
    {
        $this->_events[] = $event;
    }

    /**
     * Löscht vorhandene Events
     */
    public function clearEvents()
    {
        $this->_events = array();
    }

    /**
     * Leitet die Werte an die Events weiter
     *
     * @param \Heimdallr_TemplateAbstract $template
     * @param string $key
     * @param float  $value
     */
    public function verify(Heimdallr_TemplateAbstract $template, $key, $value)
    {
        foreach ($this->_events as $event) {
            /* @var Heimdallr_EventInterface $event */
            try {
                $event->verify($template, $key, $value);
            } catch (Exception $e) {
                Main::getInstance()->logException($e);
            }
        }
    }
}
