<?php
/**
 * Die Datei enthält die Klasse "{@link Script_SshNetwork}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SSH die Netzwerk-Traffic Daten
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_SshNetwork extends Heimdallr_SshAbstract
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
        foreach ($this->getServer()->getNetwork() as $device) {
            $this->_template[$device] = new Template_Network($this, $device);
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
            foreach ($this->getData('system', 'network') as $device => $data) {
                // Prüft, ob für das Device ein Template existiert
                if (array_key_exists($device, $this->_template)) {
                    // Prüft, ob die benötigten Werte im Array vorhanden sind
                    if ($this->existArrayKeys($data, array('input', 'output'))) {
                        // Die rrdtool-Datenbank wird geupdatet
                        $this->_template[$device]->update(array(
                            Template_Network::VALUE_INPUT  => $data['input'],
                            Template_Network::VALUE_OUTPUT => $data['output'],
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
