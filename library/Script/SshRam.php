<?php
/**
 * Die Datei enthält die Klasse "{@link Script_SshRam}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt per SSH die Verteilung des RAM's
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_SshRam extends Heimdallr_SshAbstract
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
        $this->_template = new Template_Ram($this);
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
            $data = $this->getData('system', 'ram');
            // Prüft, ob die benötigten Werte im Array vorhanden sind
            if ($this->existArrayKeys($data, array('total', 'free', 'buffers', 'cached'))) {
                // Aktuelle Benutzung
                $usage = $data['total'] - ($data['free'] + $data['buffers'] + $data['cached']);
                // Die rrdtool-Datenbank wird geupdatet
                $this->_template->update(array(
                    Template_Ram::VALUE_FREE   => $data['free'],
                    Template_Ram::VALUE_BUFFER => $data['buffers'],
                    Template_Ram::VALUE_CACHE  => $data['cached'],
                    Template_Ram::VALUE_USAGE  => $usage,
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
