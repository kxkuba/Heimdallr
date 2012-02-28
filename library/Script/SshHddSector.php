<?php
/**
 * Die Datei enthält die Klasse "{@link Script_SshHddSector}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SSH die Schreib-/Lese-Anzahl der HDD Sektoren
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_SshHddSector extends Heimdallr_SshAbstract
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
            $device = rtrim($device, '1234567890');
            if (!array_key_exists($device, $this->_template)) {
                $this->_template[$device] = new Template_HddSector($this, $device);
            }
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
            foreach ($this->getData('system', 'hdd-sector') as $device => $data) {
                // Prüft, ob für das Device ein Template existiert
                if (array_key_exists($device, $this->_template)) {
                    // Prüft, ob die benötigten Werte im Array vorhanden sind
                    if ($this->existArrayKeys($data, array('read', 'write'))) {
                        // Die rrdtool-Datenbank wird geupdatet
                        $this->_template[$device]->update(array(
                            Template_HddSector::VALUE_READ  => $data['read'],
                            Template_HddSector::VALUE_WRITE => $data['write'],
                        ));
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
