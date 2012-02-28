<?php
/**
 * Die Datei enthält die Klasse "{@link Snmp_Swap}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SNMP die Benutzung des SWAP's
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Snmp_Swap extends Heimdallr_StructureAbstract
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
        $this->_template = new Template_Swap($this);
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
            $total = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.4.3.0');
            $free  = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.4.4.0');
            $usage = $total - $free;

            $this->_template->update(array(
                Template_Swap::VALUE_FREE  => $free * 1024,
                Template_Swap::VALUE_USAGE => $usage * 1024,
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
