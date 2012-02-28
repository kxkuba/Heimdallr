<?php
/**
 * Die Datei enthält die Klasse "{@link Main}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */

// Erweitert den Standard Pfade
set_include_path(implode(PATH_SEPARATOR, array(dirname(__FILE__), get_include_path())));
// Autoloader
spl_autoload_register(function($class) {
    require_once str_replace('_', '/', $class).'.php';
});
// Timezone
date_default_timezone_set('Europe/Berlin');
// Pfad vom root-Verzeichnis
define('ROOT', realpath(dirname(__FILE__).'/../'));

/**
 * Zentrale Klasse für das Projekt
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Main
{
    const VERSION = 0.1;

    private static $_instance = null;

    private $_data = array();

    /**
     * Lädt die Config-Daten
     */
    private function __construct()
    {
        $this->_data = require_once ROOT.'/data/config.php';
    }

    /**
     * Verhindert das Clonen
     */
    private function __clone()
    {
    }

    /**
     * @static
     * @return self
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Gibt die entsprechene Einstellung zurück
     *
     * @param  array $params
     * @param  bool  $exception
     * @return mixed
     */
    public function getConfig(array $params, $exception = true)
    {
        return $this->_getConfig($this->_data, $params, $exception);
    }

    /**
     * Such die Einstellung rekursive im config-Array, anhand der Parameter
     *
     * @param  array  $data
     * @param  array  $params
     * @param  bool   $exception
     * @param  string $key
     * @return mixed
     * @throws Heimdallr_Exception
     */
    protected function _getConfig(array $data, array $params, $exception = true, $key = '')
    {
        if (empty($params)) {
            throw new Heimdallr_Exception('Der Array $params ist leer');
        } else {
            // Liest den ersten Parameter aus
            $param = array_shift($params);
            // Prüft, ob der Parameter als key im Array existiert
            if (array_key_exists($param, $data)) {
                // Ob es noch andere Parameter gibt
                if (empty($params)) {
                    // nein; Wert wird zurück gegeben
                    return $data[$param];
                } else {
                    // ja; Nächste Parameter wird ausgelesen
                    return $this->_getConfig($data[$param], $params, $exception, $key.'\''.$param.'\' -> ');
                }
            } else {
                if ($exception) {
                    throw new Heimdallr_Exception('Unbekannter Key ('.$key.'\''.$param.'\')');
                } else {
                    return null;
                }
            }
        }
    }

    /**
     * Führt den Befehl im Terminal aus
     *
     * @param  string $command
     * @param  array  $args
     * @return array|null
     */
    public function exec($command, array $args)
    {
        echo $command.' '.implode(' ', $args).PHP_EOL;
        exec($command.' '.implode(' ', $args), $return);
        return $return;
    }

    /**
     * Gibt den Pfad für die Speicherung von temporärer Dateien zurück
     *
     * @param  string      $filename
     * @param  null|string $mode
     * @return string
     */
    public function getPathTmp($filename, $mode = null)
    {
        $path = $this->getConfig(array('path', 'tmp'));
        if (!file_exists($path)) {
            throw new Heimdallr_Exception('Der Ordner "'.$path.'" existiert nicht');
        }
        $path  = realpath($path);
        $path .= '/'.preg_replace('/[^a-z0-9\.\-]+/i', '_', 'rrdtool_'.strtolower($mode).'_'.strtolower($filename).'.tmp');
        clearstatcache();
        if (!file_exists($path)) {
            touch($path, 0);
            chmod($path, 0600);
        }
        return $path;
    }

    /**
     * Gibt den Pfad für die Speicherung von den rrdtool-Datenbank zurück
     *
     * @param  string $filename
     * @return string
     */
    public function getPathDatabase($filename)
    {
        $path = $this->getConfig(array('path', 'database'));
        if (!file_exists($path)) {
            throw new Heimdallr_Exception('Der Ordner "'.$path.'" existiert nicht');
        }
        return realpath($path).'/'.$filename.'.rrd';
    }

    /**
     * Gibt den Pfad für die Speicherung der Bilder zurück
     *
     * @param  string      $filename
     * @param  null|string $folder
     * @return string
     * @throws Heimdallr_Exception
     */
    public function getPathImage($filename, $folder)
    {
        $path = $this->getConfig(array('path', 'images'));
        if (!file_exists($path)) {
            throw new Heimdallr_Exception('Der Ordner "'.$path.'" existiert nicht');
        }
        if (!empty($folder)) {
            $path .= '/'.$folder;
            if (!file_exists($path)) {
                @mkdir($path);
            }
        }
        return realpath($path).'/'.$filename.'.png';
    }

    /**
     * Führt die Action aus
     *
     * @param string $file
     * @param string $command
     * @param array  $data
     */
    public function action($file, $command, array $data)
    {
        $command = array_key_exists('1', $command) ? $command['1'] : '';
        switch ($command) {
            case 'create':
                foreach ($data as $item) {
                    /* @var StructureAbstract $item */
                    $item->create();
                }
                break;
            case 'update':
                foreach ($data as $item) {
                    /* @var StructureAbstract $item */
                    $item->update();
                }
                break;
            case 'graph':
                foreach ($data as $item) {
                    /* @var StructureAbstract $item */
                    $item->graph();
                }
                break;
            default:
                echo 'Fehlerhafter Aufruf'.PHP_EOL;
                echo 'Struktur: php5 '.basename($file).' [create|update|graph]'.PHP_EOL.PHP_EOL;
                exit -1;
                break;
        }
    }

    /**
     * Gibt das Intervall für das Daten-Input
     *
     * @static
     * @return int
     */
    public function getRrdtoolStep()
    {
        return $this->getConfig(array('rrdtool', 'step'));
    }

    /**
     * Gibt das Intervall für die Wartezeit
     *
     * @return int
     */
    public function getRrdtoolWait()
    {
        return floor($this->getRrdtoolStep() * 2);
    }

    /**
     * Gibt die Information über den Zeitraum der rrdtool-Daten
     *
     * @return array
     */
    public function getRrdtoolRang()
    {
        return array(
            'day'   => array(
                'hour'  => $this->getConfig(array('rrdtool', 'range', 'day')),
                'count' => 1
            ),
            'week'  => array(
                'hour'  => $this->getConfig(array('rrdtool', 'range', 'week')),
                'count' => floor(600 / $this->getRrdtoolStep())
            ),
            'month' => array(
                'hour'  => $this->getConfig(array('rrdtool', 'range', 'month')),
                'count' => floor(3600 / $this->getRrdtoolStep())
            ),
            'year'  => array(
                'hour'  => $this->getConfig(array('rrdtool', 'range', 'year')),
                'count' => floor((3600 * 24) / $this->getRrdtoolStep())
            ),
        );
    }

    /**
     * Holt ein Datensatz per SNMP und gibt genau den Wert zurück
     *
     * @param  Heimdallr_Server $server
     * @param  string        $code
     * @return int|mixed
     * @throws Exception
     */
    public function snmpGet(Heimdallr_Server $server, $code)
    {
        $args = array(
            '-v '.$server->getSnmpVersion(),
            '-c '.$server->getSnmpString(),
            $server->getIp(),
            $code
        );
        $data = self::exec($this->getConfig(array('command', 'snmpGet')), $args);
        if (count($data) != 1) {
            throw new Heimdallr_Exception('Es wurde mehr als ein Datensatz zurück gegeben');
        }
        $data  = explode(' ', reset($data));
        $value = end($data);
        if (count($data) != 4) {
            throw new Heimdallr_Exception('Der Datensatz entspricht nicht der erwarteten Struktur');
        }
        if (strtolower($data['2']) == strtolower('Counter32')) {
            if ($value < 0) {
                $value = -1 * (-4294967296 - $value);
            }
        }
        return $value;
    }

    /**
     * Holt mehrere Datensätze per SNMP
     *
     * @param Heimdallr_Server $server
     * @param string        $code
     * @return array|null
     */
    public function snmpWalk(Heimdallr_Server $server, $code)
    {
        $args = array(
            '-v '.$server->getSnmpVersion(),
            '-c '.$server->getSnmpString(),
            $server->getIp(),
            $code
        );
        return self::exec($this->getConfig(array('command', 'snmpWalk')), $args);
    }

    public function logException(Exception $e)
    {
        echo $e.PHP_EOL;
    }
}