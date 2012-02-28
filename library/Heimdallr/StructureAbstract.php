<?php
/**
 * Die Datei enthält die Klasse "{@link Heimdallr_StructureAbstract}"
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
abstract class Heimdallr_StructureAbstract
{
    /**
     * @var Heimdallr_Server
     */
    private $_server = null;

    /**
     * @var Heimdallr_EventInterface|null
     */
    private $_event = null;

    /**
     * @var string
     */
    private $_mode = '';

    /**
     * @param Heimdallr_Server $server
     * @param Heimdallr_EventInterface|null $event
     */
    public function __construct(Heimdallr_Server $server, Heimdallr_EventInterface $event = null)
    {
        $this->_server = $server;
        $this->_event  = $event;
        $this->init();
    }

    /**
     * @return Heimdallr_Server
     */
    public function getServer()
    {
        return $this->_server;
    }

    /**
     * @return Heimdallr_EventInterface|null
     */
    public function getEvent()
    {
        return $this->_event;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;
    }

    /**
     * Initialisiert die Template-Klassen
     */
    abstract protected function init();

    /**
     * Proxy-Methode für das Erstellen der Datenbank
     */
    abstract public function create();

    /**
     * Proxy-Methode für das Update der Datenbank
     */
    abstract public function update();

    /**
     * Proxy-Methode für die Erstellungen der Graphen
     */
    abstract public function graph();
}
