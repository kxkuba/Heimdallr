<?php
/**
 * Die Datei enthält die Klasse "{@link Script_SshLoad}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SSH die Average-Load Verteilung
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_SshLoad extends Heimdallr_SshAbstract
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
            $data = $this->getData('system', 'load');
            // Prüft, ob die benötigten Werte im Array vorhanden sind
            if ($this->existArrayKeys($data, array('load1', 'load5', 'load15'))) {
                // Die rrdtool-Datenbank wird geupdatet
                $this->_template->update(array(
                    Template_Load::VALUE_LOAD_01 => $data['load1'],
                    Template_Load::VALUE_LOAD_05 => $data['load5'],
                    Template_Load::VALUE_LOAD_15 => $data['load15'],
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
