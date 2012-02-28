<?php
/**
 * Die Datei enthält die Klasse "{@link Script_SshHddSize}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SSH die Größe der Festplatten
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_SshHddSize extends Heimdallr_SshAbstract
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
            foreach ($this->getData('system', 'hdd-size') as $device => $data) {
                // Prüft, ob für das Device ein Template existiert
                if (array_key_exists($device, $this->_template)) {
                    // Prüft, ob die benötigten Werte im Array vorhanden sind
                    if ($this->existArrayKeys($data, array('free', 'total'))) {
                        // Aktuelle Benutzung
                        $usage = $data['total'] - $data['free'];
                        // Die rrdtool-Datenbank wird geupdatet
                        $this->_template[$device]->update(array(
                            Template_HddSize::VALUE_FREE  => $data['free'] * 1024,
                            Template_HddSize::VALUE_USAGE => $usage * 1024
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
