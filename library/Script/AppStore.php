<?php
/**
 * Die Datei enthält die Klasse "{@link Script_Weather}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Überprüft den Preis im iTunes App Store
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_AppStore extends Heimdallr_StructureAbstract
{
    /**
     * @var Heimdallr_TemplateAbstract
     */
    private $_template = null;

    /**
     * @param \Heimdallr_AppStore $appStore
     * @param Heimdallr_EventInterface|null $event
     */
    public function __construct(Heimdallr_AppStore $appStore, Heimdallr_EventInterface $event = null)
    {
        $server = new Heimdallr_Server();
        $server->setName($appStore->getName());
        $server->setDescription($appStore->getDescription());
        $server->setIp($appStore->getId());

        parent::__construct($server, $event);
    }

    /**
     * Initialisiert die Template-Klassen
     */
    protected function init()
    {
        $this->setMode('appstore');
        $this->_template = new Template_AppStore($this);
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
            $file = Main::getInstance()->getPathTmp($this->getServer()->getName(), $this->getMode());
            // Alter des Datensatzes
            if (time() - filemtime($file) > (60 * 60) - 20) {
                // zu alt (~60 Minuten)
                $string  = file_get_contents('http://itunes.apple.com/de/app/app_name/id'.$this->getServer()->getIp().'?mt=12');
                $pattern = '/<div class="price">([\.,0-9]*)/i';
                $value   = 0;
                if (preg_match($pattern, $string, $matches)) {
                    $value = str_replace(',' , '.', $matches['1']);
                }
                file_put_contents($file, $value);
            } else {
                $value = file_get_contents($file);
            }
            $this->_template->update(array(
                Template_AppStore::VALUE_PRICE => $value
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
