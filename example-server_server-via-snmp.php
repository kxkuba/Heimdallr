<?php
/**
 * Überwacht den Server per SNMP
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Example
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */

if ($argc == 0) {
    // Wurde nicht per Terminal aufgerufen
    die(-1);
}

require_once dirname(__FILE__) . '/library/Main.php';

$server = new Heimdallr_Server();
$server->setName('Test');
$server->setDescription('Abfrage per SNMP');
$server->setIp('localhost');
$server->setSnmpVersion('2c');
$server->setSnmpString('public');
$server->setNetwork(array('eth0'));
$server->setDevice(array('sda1'));

Main::getInstance()->action(__FILE__, $argv, array(
    new Script_Ping($server, new Event_Events(array(
        new Event_Equal(new Transmitter_Transmitters(array(
            // Wenn der Wert 0 ist, dann wurde der Server nicht erreicht
            new Transmitter_Twitter(''),  // add Twitter-Benutzername
            new Transmitter_Email(''),    // add E-Mail Adresse
        )), Template_Ping::VALUE_PING, 0)
    ))),
    new Snmp_Cpu($server),
    new Snmp_HddSize($server),
    new Snmp_Load($server),
    new Snmp_Network($server),
    new Snmp_Process($server),
    new Snmp_Ram($server),
    new Snmp_Swap($server),
    new Snmp_UserOnline($server),
));
