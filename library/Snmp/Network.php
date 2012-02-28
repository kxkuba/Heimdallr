<?php
/**
 * Die Datei enthält die Klasse "{@link Snmp_Network}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SNMP die Netzwerk-Traffic Daten
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Snmp_Network extends Heimdallr_StructureAbstract
{
    /**
     * @var Heimdallr_TemplateAbstract
     */
    private $_template = array();

    /**
     * Initialisiert die Template-Klassen
     */
    protected function init()
    {
        $this->setMode('server');
        foreach ($this->getServer()->getNetwork() as $network) {
            $this->_template[] = new Template_Network($this, $network);
        }
    }

    /**
     * Proxy-Methode für das Erstellen der Datenbank
     */
    public function create()
    {
        foreach ($this->_template as $template) {
            /* @var Heimdallr_TemplateAbstract $template */
            $template->create();
        }
    }

    /**
     * Proxy-Methode für das Update der Datenbank
     */
    public function update()
    {
        try {
            $interface = array();
            foreach(Main::getInstance()->snmpWalk($this->getServer(), '.1.3.6.1.2.1.2.2.1.2') as $row) {
                foreach ($this->getServer()->getNetwork() as $network) {
                    if (strpos(strtolower($row), strtolower($network)) !== false) {
                        $code = reset(explode(' ', $row));
                        $interface[$network] = substr($code, strrpos($code, '.') + 1);
                    }
                }
            }
            foreach ($this->_template as $template) {
                /* @var Heimdallr_TemplateAbstract $template */
                if (array_key_exists($template->getDevice(), $interface)) {
                    try {
                        $input  = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.2.2.1.10.'.$interface[$template->getDevice()]);
                        $output = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.2.2.1.16.'.$interface[$template->getDevice()]);

                        $template->update(array(
                            Template_Network::VALUE_INPUT  => $input,
                            Template_Network::VALUE_OUTPUT => $output,
                        ));
                    } catch (Exception $e) {
                        Main::getInstance()->logException($e);
                    }
                }
            }
        } catch (Exception $e) {
            Main::getInstance()->logException($e);
        }
    }

    /**
     * Proxy-Methode für die Erstellungen der Graphen
     */
    public function graph()
    {
        foreach ($this->_template as $template) {
            /* @var Heimdallr_TemplateAbstract $template */
            $template->graph();
        }
    }

}
