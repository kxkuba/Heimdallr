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
 * Es wird von der Webseite finance.yahoo.com alle 30 Minuten der aktuelle Kurs abgefragt
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_Market extends Heimdallr_StructureAbstract
{
    /**
     * @var Heimdallr_TemplateAbstract
     */
    private $_template = null;

    /**
     * @param \Heimdallr_Market $market
     * @param Heimdallr_EventInterface|null $event
     */
    public function __construct(Heimdallr_Market $market, Heimdallr_EventInterface $event = null)
    {
        $server = new Heimdallr_Server();
        $server->setName($market->getName());
        $server->setDescription($market->getDescription());
        $server->setIp($market->getSymbol());

        parent::__construct($server, $event);
    }

    /**
     * Initialisiert die Template-Klassen
     */
    protected function init()
    {
        $this->setMode('market');
        $this->_template = new Template_Market($this);
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
            $data = array();
            $file = Main::getInstance()->getPathTmp($this->getServer()->getName(), $this->getMode());
            // Alter des Datensatzes
            if (time() - filemtime($file) > (30 * 60) - 20) {
                // zu alt (~30 Minuten)
                $string = file_get_contents('http://download.finance.yahoo.com/d/quotes.csv?f=l1&s='.$this->getServer()->getIp());
                foreach (explode(PHP_EOL, $string) as $item) {
                    if (0 < (float) $item) {
                        $data[] = (float)  $item;
                    }
                }
                if (count($data) == 0) {
                    throw new Heimdallr_StructureException('Für die Aktie wurde kein Preis gefunden');
                }
                file_put_contents($file, json_encode($data));
            } else {
                $data = json_decode(file_get_contents($file), true);
            }
            foreach ($data as $item) {
                $this->_template->update(array(
                    Template_Market::VALUE_MARKET => $item
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
