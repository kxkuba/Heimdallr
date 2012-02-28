<?php
/**
 * Überwachung einer FritzBox
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

require_once dirname(__FILE__).'/library/Main.php';

$server = new Heimdallr_Server();
$server->setName('FritzBox');
$server->setDescription('FritzBox 7390');
$server->setIp('fritz.box');
$server->setUpnpPort('49000');
$server->setNetwork(array('wan0'));

Main::getInstance()->action(__FILE__, $argv, array(
    new Script_Ping($server),
    new Upnp_Network($server),
));