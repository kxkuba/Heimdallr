<?php
/**
 * Die Datei enthält die Klasse "{@link Script_SshCpu}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SSH die CPU Last-Verteilung
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_SshCpu extends Heimdallr_SshAbstract
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
        $this->_template = new Template_Cpu($this);
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
            $data = $this->getData('system', 'cpu');
            // Prüft, ob die benötigten Werte im Array vorhanden sind
            if ($this->existArrayKeys($data, array('user', 'nice', 'system', 'idle'))) {
                // Die rrdtool-Datenbank wird geupdatet
                $this->_template->update(array(
                    Template_Cpu::VALUE_USER   => $data['user'],
                    Template_Cpu::VALUE_NICE   => $data['nice'],
                    Template_Cpu::VALUE_SYSTEM => $data['system'],
                    Template_Cpu::VALUE_IDLE   => $data['idle'],
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
