<?php
/**
 * Die Datei enthält die Klasse "{@link Heimdallr_TemplateAbstract}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 *
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
abstract class Heimdallr_TemplateAbstract
{
    /**
     * @var Heimdallr_StructureAbstract
     */
    private $_structure = null;

    /**
     * @var Heimdallr_Rrdtool
     */
    private $_rrdtool = null;

    /**
     * @var string
     */
    private $_device = '';

    /**
     * @param Heimdallr_StructureAbstract $structure
     * @param string $device
     */
    final public function __construct(Heimdallr_StructureAbstract $structure, $device = '')
    {
        $this->_structure = $structure;
        $this->_device    = $device;
        $this->init();
    }

    /**
     * @return Heimdallr_StructureAbstract
     */
    public function getStrucutre()
    {
        return $this->_structure;
    }

    /**
     * @return string
     */
    public function getDevice()
    {
        return $this->_device;
    }

    /**
     * @return Heimdallr_Server
     */
    public function getServer()
    {
        return $this->getStrucutre()->getServer();
    }

    /**
     * @return Heimdallr_EventInterface|null
     */
    public function getEvent()
    {
        return $this->getStrucutre()->getEvent();
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->getStrucutre()->getMode();
    }

    /**
     * @return \Heimdallr_Rrdtool
     */
    public function getRrdtool()
    {
        if ($this->_rrdtool === null) {
            throw new Heimdallr_TemplateException('Die rrdTool Klasse muss erst initialisiert werden');
        }
        return $this->_rrdtool;
    }

    /**
     * Initialisiert die rrdTool Klasse
     *
     * @param string $type
     */
    public function initRrdtool($type)
    {
        $this->_rrdtool = new Heimdallr_Rrdtool($this, $type);
    }

    /**
     * Prüft, ob der Datensatz korrekt ist
     *
     * @param  array $data
     * @param  array $keys
     * @throws Heimdallr_TemplateException
     */
    public function validData(array $data, array $keys)
    {
        // Prüft, ob die Array gleich lang
        if (count($keys) != count($data)) {
            throw new Heimdallr_TemplateException('Unvollständiger Datensatz');
        }
        // Prüft, ob die Keys vorhanden sind.
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new Heimdallr_TemplateException('Unvollständiger Datensatz');
            }
        }
    }

    /**
     * Entfernt aus den Array Einträge mit Keyword :LAST:"
     *
     * @param  array $args
     * @return array
     */
    public function removeLast(array $args)
    {
        return array_filter($args, function($string) {
            return strpos($string, ':LAST:"') === false;
        });
    }

    /**
     * Sucht den Array mit den Längsten String und füllt die restlichen Strings auf
     *
     * @param  array $array
     * @return array
     */
    public function adjustText(array $array)
    {
        $count = 0;
        foreach ($array as $string) {
            $count = max($count, strlen($string));
        }
        $return = array();
        foreach ($array as $key => $string) {
            $return[$key] = str_pad($string, $count, ' ', STR_PAD_RIGHT);
        }
        return $return;
    }

    /**
     * Initialisiert die rrdTool-Klasse
     */
    abstract protected function init();

    /**
     * Erstellt die Datenbank
     */
    abstract public function create();

    /**
     * Updatet die Datenbank
     *
     * @param array $data
     */
    abstract public function update(array $data);

    /**
     * Erstellt die Graphen von der Datenbank
     */
    abstract public function graph();

}
