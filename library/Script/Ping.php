<?php
/**
 * Die Datei enthält die Klasse "{@link Script_Ping}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Prüft, wie lange der Server auf eine Ping antwort braucht
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_Ping extends Heimdallr_StructureAbstract
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
        $this->_template = new Template_Ping($this);
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
            $data = Main::getInstance()->exec(
                Main::getInstance()->getConfig(array('command', 'ping')),
                array('-c 1', '-w 10', $this->getServer()->getIp())
            );
            $pattern = '/icmp_(?:r|s)eq.*time=([\.0-9]*)/i';
            $value   = 0;
            foreach ($data as $item) {
                if (preg_match($pattern, $item, $matches)) {
                    $value = (float) $matches[1];
                }
            }
            $this->_template->update(array(
                Template_Ping::VALUE_PING => $value
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
        $this->_template->graph();
    }

}
