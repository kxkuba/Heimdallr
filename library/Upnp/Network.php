<?php
/**
 * Die Datei enthält die Klasse "{@link Upnp_Network}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Upnp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Holt die Netzwerk-Traffic Daten über UPNP
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Upnp
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Upnp_Network extends Heimdallr_StructureAbstract
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
        foreach ($this->getServer()->getNetwork() as $network) {
            $this->_template[$network] = new Template_Network($this, $network);
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
            $xml = array(
                '<?xml version=\'1.0\' encoding=\'utf-8\'?>',
                '<s:Envelope s:encodingStyle=\'http://schemas.xmlsoap.org/soap/encoding/\' xmlns:s=\'http://schemas.xmlsoap.org/soap/envelope/\'>',
                '<s:Body>',
                '<u:GetAddonInfos xmlns:u=\'urn:schemas-upnp-org:service:WANCommonInterfaceConfig:1\' />',
                '</s:Body>',
                '</s:Envelope>',
            );
            $data = Main::getInstance()->exec(Main::getInstance()->getConfig(array('command', 'curl')), array(
                'http://'.$this->getServer()->getIp().':'.$this->getServer()->getUpnpPort().'/upnp/control/WANCommonIFC1',
                '-H "Content-Type: text/xml; charset=\'utf-8\'"',
                '-H "SoapAction:urn:schemas-upnp-org:service:WANCommonInterfaceConfig:1#GetAddonInfos"',
                '-s',
                '-d "'.implode('', $xml).'"'
            ));
            foreach ($this->_template as $template) {
                /* @var Heimdallr_TemplateAbstract $template */
                $input  = 0;
                $output = 0;
                foreach ($data as $item) {
                    if (strpos($item, 'NewTotalBytesReceived') !== false) {
                        $input = (float) substr($item, strpos($item, '>') + 1, strrpos($item, '<') - strpos($item, '>') - 1);
                    }
                    if (strpos($item, 'NewTotalBytesSent') !== false) {
                        $output = (float) substr($item, strpos($item, '>') + 1, strrpos($item, '<') - strpos($item, '>') - 1);
                    }
                }
                if ($input < 0) {
                    $input = (-4294967296 - $input) * -1;
                }
                if ($output < 0) {
                    $output = (-4294967296 - $output) * -1;
                }
                $template->update(array(
                    Template_Network::VALUE_INPUT  => $input,
                    Template_Network::VALUE_OUTPUT => $output,
                ));
            }
        } catch (Exception $e) {
            Main::getInstance()->logException($e);
        }
    }

    /**
     * Proxy-Methode für die Erstellung der Graphen
     */
    public function graph()
    {
        foreach ($this->_template as $template) {
            /* @var Heimdallr_TemplateAbstract $template */
            $template->graph();
        }
    }

}
