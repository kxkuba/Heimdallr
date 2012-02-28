<?php
/**
 * Überwacht den Server per SSH
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
$server->setDescription('Abfrage per SSH');
$server->setIp('192.168.83.11');
$server->setNetwork(array('eth0'));
$server->setDevice(array('sda1'));
$server->setUser(array('root', 'username'));

Main::getInstance()->action(__FILE__, $argv, array(
    new Script_Ping($server, new Event_Events(array(
        new Event_Equal(new Transmitter_Transmitters(array(
            // Wenn der Wert 0 ist, dann wurde der Server nicht erreicht
            new Transmitter_Twitter(''),  // add Twitter-Benutzername
            new Transmitter_Email(''),    // add E-Mail Adresse
        )), Template_Ping::VALUE_PING, 0)
    ))),
    new Script_SshCpu($server),
    new Script_SshHddSector($server),
    new Script_SshHddSize($server),
    new Script_SshLoad($server),
    new Script_SshNetwork($server),
    new Script_SshProcess($server),
    new Script_SshRam($server),
    new Script_SshSwap($server),
    new Script_SshUserAvailable($server),
    new Script_SshUserCount($server),
    new Script_SshUserOnline($server),
));
