<?php
/**
 * Verlauf des Preises eines Programmes
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

$appstore = new Heimdallr_AppStore();
$appstore->setName('Cut the Rope');
$appstore->setId('501315464');

Main::getInstance()->action(__FILE__, $argv, array(
    new Script_AppStore($appstore, new Event_Events(array(
        new Event_Change(new Transmitter_Transmitters(array(
            new Transmitter_Twitter('') // add E-Mail Adresse
        )), Template_AppStore::VALUE_PRICE),
    ))),
));
