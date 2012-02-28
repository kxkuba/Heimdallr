<?php
/**
 * Überwachung von Aktienkursen
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

$market = new Heimdallr_Market();
$market->setName('Google');
$market->setDescription('Google Inc.');
$market->addSymbol('GOOG');

Main::getInstance()->action(__FILE__, $argv, array(
    new Script_Market($market, new Event_Events(array(
        new Event_LessEqual(
            new Transmitter_Transmitters(array(
                // add Transmitter
            )),
            Template_Market::VALUE_MARKET,
            600
        ),
        new Event_GreaterEqual(
            new Transmitter_Transmitters(array(
                // add Transmitter
            )),
            Template_Market::VALUE_MARKET,
            650
        ),
    ))),
));


