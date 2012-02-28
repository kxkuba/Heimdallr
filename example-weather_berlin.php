<?php
/**
 * Verlauf des Wetters
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

$weather = new Heimdallr_Weather();

$weather->setName('Berlin');
$weather->setYahooCode('638242');

Main::getInstance()->action(__FILE__, $argv, array(
    new Script_Weather($weather, new Event_Events(array(
        // Add Events
    ))),
));
