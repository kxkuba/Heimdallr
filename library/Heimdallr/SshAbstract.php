<?php
/**
 * Die Datei enthält die Klasse "{@link Heimdallr_SshAbstract}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 *  Holt die Daten per SSH
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
abstract class Heimdallr_SshAbstract extends Heimdallr_StructureAbstract
{
    private static $_data = array();
    private static $_exception = array();

    /**
     * Der Datensatz wird zurück gegeben (Wenn der Daten-Type noch nicht vorhanden ist, wird er per SSH geladen)
     *
     * @param  string $type
     * @param  string $mode
     * @return array
     */
    public function getData($type, $mode)
    {
        // Prüft, ob ein Exception-Fall vorliegt
        if (array_key_exists($type, self::$_exception)) {
            // wenn ja, wird ein Exception geworfen
            throw self::$_exception[$type];
        }
        if (!array_key_exists($type, self::$_data)) {
            // Daten werden geladen und die String-Ränder bereinigt

            // temporär ohne SSH und nur lokal !!!!
            $return   = trim(trim(implode('', Main::getInstance()->exec(ROOT.'/data.sh', array($type))), ';'));
            $pattern1 = '/^[^:]+:[^;]+(?:;[^:]+:[^;]+)*$/';
            $pattern2 = '/(?:[^:;]+:[^;:]+)+/';
            // Die Datenstruktur wird geprüft
            if (preg_match($pattern1, $return) && preg_match_all($pattern2, $return, $matches)) {
                // Datenstruktur ist korrekt
                $data = array();
                // Array wird aufgebaut
                foreach (reset($matches) as $match) {
                    $split = explode(':', $match);
                    $data  = $this->makeArray($data, $split['0'], $split['1']);
                }
                self::$_data[$type] = $data;
            } else {
                // Datenstruktur ist fehlerhaft
                self::$_exception[$type] = new Heimdallr_StructureException('Fehlerhafte Pattern-Struktur: '.$return);
                throw self::$_exception[$type];
            }
        }
        if (isset(self::$_data[$type][$mode])) {
            return self::$_data[$type][$mode];
        }
        return array();
    }

    /**
     * Baut aus dem $key-String ein mehrdimensionalen Array
     *
     * @param  array  $data
     * @param  string $key
     * @param  string $value
     * @return array
     */
    public function makeArray(array $data, $key, $value)
    {
        $position = strpos($key, '_');
        if ($position === false) {
            $data[$key] = (float) $value;
        } else {
            $firstKey = substr($key, 0, $position);
            $restKey  = substr($key, $position + 1);
            if (!isset($data[$firstKey])) {
                $data[$firstKey] = array();
            }
            $data[$firstKey] = $this->makeArray($data[$firstKey], $restKey, $value);
        }
        return $data;
    }

    /**
     * Prüft, ob die $keys im array vorhanden sind
     *
     * @param  array  $data
     * @param  array  $keys
     * @return bool
     */
    public function existArrayKeys(array $data, array $keys = array())
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                return false;
            }
        }
        return true;
    }
}
