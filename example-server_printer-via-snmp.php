<?php
/**
 * Überwacht den Drucker per SNMP
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
$server->setName('Test Drucker');
$server->setDescription('Drucker Abfrage per SNMP');
$server->setIp('192.168.83.5');
$server->setSnmpVersion('2c');
$server->setSnmpString('public');
$server->setNetwork(array('eth0'));

Main::getInstance()->action(__FILE__, $argv, array(
    new Script_Ping($server, new Event_Events(array(
        new Event_Equal(new Transmitter_Transmitters(array(
            // Wenn der Wert 0 ist, dann wurde der Server nicht erreicht
            new Transmitter_Twitter(''),  // add Twitter-Benutzername
            new Transmitter_Email(''),    // add E-Mail Adresse
        )), Template_Ping::VALUE_PING, 0)
    ))),
    new Snmp_Network($server),
    new Snmp_PrinterHpColorFour($server),
));
