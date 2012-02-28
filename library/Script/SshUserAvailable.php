<?php
/**
 * Die Datei enthält die Klasse "{@link Script_SshUserAvailable}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SSH die Anzahl der vorhandenen Benutzer
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_SshUserAvailable extends Heimdallr_SshAbstract
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
        $this->_template = new Template_UserAvailable($this);
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
            $data = $this->getData('system', 'user-available');
            // Prüft, ob die benötigten Werte im Array vorhanden sind
            if ($this->existArrayKeys($data, array('user', 'system'))) {
                // Die rrdtool-Datenbank wird geupdatet
                $this->_template->update(array(
                    Template_UserAvailable::VALUE_USER_USER   => $data['user'],
                    Template_UserAvailable::VALUE_USER_SYSTEM => $data['system'],
                ));
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
        $this->_template->graph();
    }
}
