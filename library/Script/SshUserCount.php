<?php
/**
 * Die Datei enthält die Klasse "{@link Script_SshUserCount}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SSH die einzelne Benutzer die gerade online sind
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_SshUserCount extends Heimdallr_SshAbstract
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
        foreach ($this->getServer()->getUser() as $device) {
            $this->_template[$device] = new Template_UserCount($this, $device);
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
            foreach ($this->getData('system', 'user-count') as $device => $data) {
                // Prüft, ob für das Device ein Template existiert
                if (array_key_exists($device, $this->_template)) {
                    // Prüft, ob die benötigten Werte im Array vorhanden sind
                    if ($this->existArrayKeys($data, array('count'))) {
                        // Die rrdtool-Datenbank wird geupdatet
                        $this->_template[$device]->update(array(
                            Template_UserCount::VALUE_USER_COUNT => $data['count'],
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
