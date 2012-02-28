<?php
/**
 * Die Datei enthält die Klasse "{@link Snmp_Ram}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SNMP die Verteilung des RAM's
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Snmp_Ram extends Heimdallr_StructureAbstract
{
    /**
     * @var Heimdallr_TemplateAbstract
     */
    private $_template = null;

    /**
     * Initialisiert die Template-Klassen
     */
    protected function init()
    {
        $this->setMode('server');
        $this->_template = new Template_Ram($this);
    }

    /**
     * Proxy-Methode für das Erstellen der Datenbank
     */
    public function create()
    {
        $this->_template->create();
    }

    /**
     * Proxy-Methode für das Update der Datenbank
     */
    public function update()
    {
        try {
            $total   = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.4.5.0');
            $free    = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.4.6.0');
            $buffers = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.4.14.0');
            $cached  = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.4.15.0');
            $usage   = $total - ($free + $buffers + $cached);

            $this->_template->update(array(
                Template_Ram::VALUE_FREE   => $free * 1024,
                Template_Ram::VALUE_BUFFER => $buffers * 1024,
                Template_Ram::VALUE_CACHE  => $cached * 1024,
                Template_Ram::VALUE_USAGE  => $usage * 1024,
            ));
        } catch (Exception $e) {
            Main::getInstance()->logException($e);
        }
    }

    /**
     * Proxy-Methode für die Erstellungen der Graphen
     */
    public function graph()
    {
        $this->_template->graph();
    }

}
