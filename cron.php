<?php
/**
 * Führt alle rrd-Scripte im Hintergrund aus
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */

if ($argc == 0) {
    // Wurde nicht per Terminal aufgerufen
    die(-1);
}

require_once dirname(__FILE__).'/library/Main.php';

$data = array(
    'appstore',
    'market',
    'server',
    'weather',
);

$argv['1'] = array_key_exists('1', $argv) ? $argv['1'] : '';
$pattern   = '/^('.implode('|', $data).')_(.*)\.php$/i';
$iterator  = new DirectoryIterator(dirname(__FILE__));
foreach ($iterator as $file) {
    /* @var DirectoryIterator $file */
    if ($file->isFile() && preg_match($pattern, $file->getFilename())) {
        Main::getInstance()->exec(Main::getInstance()->getConfig(array('command', 'php5')), array(
            $file->getPathname(), $argv['1'], '> /dev/null', '2> /dev/null', '&'
        ));
    }
}