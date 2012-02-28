<?php
/**
 * Die Datei enthält die Klasse "{@link Snmp_Cpu}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SNMP die CPU Last-Verteilung
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Snmp_Cpu extends Heimdallr_StructureAbstract
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
        $this->_template = new Template_Cpu($this);
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
            $user   = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.11.50.0');
            $nice   = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.11.51.0');
            $system = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.11.52.0');
            $idle   = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.11.53.0');

            $this->_template->update(array(
                Template_Cpu::VALUE_USER   => $user,
                Template_Cpu::VALUE_NICE   => $nice,
                Template_Cpu::VALUE_SYSTEM => $system,
                Template_Cpu::VALUE_IDLE   => $idle,

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
