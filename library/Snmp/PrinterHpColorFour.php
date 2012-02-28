<?php
/**
 * Die Datei enthält die Klasse "{@link Snmp_PrinterHpColorFour}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SNMP den Füllstand der Patronen und die Anzahl der gedruckten Seiten
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Snmp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Snmp_PrinterHpColorFour extends Heimdallr_StructureAbstract
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
        $this->_template = array(
            'color'  => new Template_PrinterColorFour($this),
            'count'  => new Template_PrinterCount($this),
        );

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
            $data = array();

            $totalBlack   = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.43.11.1.1.8.1.1');
            $totalYellow  = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.43.11.1.1.8.1.2');
            $totalCyan    = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.43.11.1.1.8.1.3');
            $totalMagenta = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.43.11.1.1.8.1.4');
            $countBlack   = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.43.11.1.1.9.1.1');
            $countYellow  = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.43.11.1.1.9.1.2');
            $countCyan    = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.43.11.1.1.9.1.3');
            $countMagenta = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.43.11.1.1.9.1.4');

            $data[Template_PrinterColorFour::VALUE_BLACK]   = 100 * $countBlack / $totalBlack;
            $data[Template_PrinterColorFour::VALUE_YELLOW]  = 100 * $countYellow / $totalYellow;
            $data[Template_PrinterColorFour::VALUE_CYAN]    = 100 * $countCyan / $totalCyan;
            $data[Template_PrinterColorFour::VALUE_MAGENTA] = 100 * $countMagenta / $totalMagenta;

            $data[Template_PrinterCount::VALUE_COUNT] = Main::getInstance()->snmpGet($this->getServer(), '.1.3.6.1.2.1.43.10.2.1.4.1.1');

            $this->_template['color']->update(array(
                Template_PrinterColorFour::VALUE_BLACK   => $data[Template_PrinterColorFour::VALUE_BLACK],
                Template_PrinterColorFour::VALUE_YELLOW  => $data[Template_PrinterColorFour::VALUE_YELLOW],
                Template_PrinterColorFour::VALUE_CYAN    => $data[Template_PrinterColorFour::VALUE_CYAN],
                Template_PrinterColorFour::VALUE_MAGENTA => $data[Template_PrinterColorFour::VALUE_MAGENTA],
            ));
            $this->_template['count']->update(array(
                Template_PrinterCount::VALUE_COUNT => $data[Template_PrinterCount::VALUE_COUNT],
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
        foreach ($this->_template as $template) {
            /* @var Heimdallr_TemplateAbstract $template */
            $template->graph();
        }
    }

}
