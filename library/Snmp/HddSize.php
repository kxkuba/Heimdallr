<?php
/**
 * Die Datei enthält die Klasse "{@link Snmp_HddSize}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SNMP die Größe der Festplatten
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Snmp_HddSize extends Heimdallr_StructureAbstract
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
        foreach ($this->getServer()->getDevice() as $device) {
            $this->_template[$device] = new Template_HddSize($this, $device);
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
            foreach(Main::getInstance()->snmpWalk($this->getServer(), '.1.3.6.1.4.1.2021.9.1.3') as $row) {
                foreach ($this->getServer()->getDevice() as $device) {
                    if (strpos(strtolower($row), strtolower($device)) !== false) {
                        $code = reset(explode(' ', $row));
                        $interface[$device] = substr($code, strrpos($code, '.') + 1);
                    }
                }
            }
            foreach ($this->_template as $template) {
                /* @var Heimdallr_TemplateAbstract $template */
                try {
                    $total = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.9.1.6.'.$interface[$template->getMode()]);
                    $free  = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.4.1.2021.9.1.7.'.$interface[$template->getMode()]);
                    $usage = $total - $free;

                    $template->update(array(
                        Template_HddSize::VALUE_FREE  => $free * 1024,
                        Template_HddSize::VALUE_USAGE => $usage * 1024,
                    ));
                } catch (Exception $e) {
                    Main::getInstance()->logException($e);
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
