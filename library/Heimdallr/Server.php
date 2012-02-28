<?php
/**
 * Die Datei enthält die Klasse "{@link Heimdallr_Server}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Einstellungen bzw. Informationen vom überwachten Server
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Heimdallr_Server
{
    private $_name         = '';
    private $_description  = '';
    private $_ip           = '';

    private $_snmp_version = '';
    private $_snmp_string  = '';

    private $_upnp_port    = '';

    private $_network      = array();
    private $_device       = array();
    private $_user         = array();

    public function getGraphTitle($prefix, $postfix)
    {
        $description = $this->getDescription();
        if (empty($description)) {
            return trim(rtrim($prefix).' '.$this->getName().' '.ltrim($postfix));
        }
        return trim(rtrim($prefix).' '.$this->getDescription().' ('.$this->getName().') '.ltrim($postfix));
    }

    /**
     * Beschreibung des Servers
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setDevice(array $device)
    {
        $this->_device = $device;
    }

    public function getDevice()
    {
        return $this->_device;
    }

    public function setIp($ip)
    {
        $this->_ip = $ip;
    }

    public function getIp()
    {
        return $this->_ip;
    }

    /**
     * Ein eindeutigen Namen (eindeutig innerhalb aller Server)
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setNetwork(array $network)
    {
        $this->_network = $network;
    }

    public function getNetwork()
    {
        return $this->_network;
    }

    public function setSnmpString($snmp_string)
    {
        $this->_snmp_string = $snmp_string;
    }

    public function getSnmpString()
    {
        return $this->_snmp_string;
    }

    public function setSnmpVersion($snmp_version)
    {
        $this->_snmp_version = $snmp_version;
    }

    public function getSnmpVersion()
    {
        return $this->_snmp_version;
    }

    public function setUser(array $users)
    {
        $this->_user = $users;
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function setUpnpPort($upnp_port)
    {
        $this->_upnp_port = $upnp_port;
    }

    public function getUpnpPort()
    {
        return $this->_upnp_port;
    }
}
