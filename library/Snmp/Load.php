<?php
/**
 * Die Datei enthält die Klasse "{@link Snmp_Load}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SNMP die Average-Load Verteilung
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Snmp_Load extends Heimdallr_StructureAbstract
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
        $this->_template = new Template_Load($this);
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
            $this->_template->update(array(
                Template_Load::VALUE_LOAD_01 => trim(Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.10.1.3.1'), '"'),
                Template_Load::VALUE_LOAD_05 => trim(Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.10.1.3.2'), '"'),
                Template_Load::VALUE_LOAD_15 => trim(Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.10.1.3.3'), '"'),

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
