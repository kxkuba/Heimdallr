<?php
/**
 * Die Datei enthält die Klasse "{@link Heimdallr_Market}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Einstellungen bzw. Informationen von der überwachten Aktie
 *
 * Proxy-Klasse von Heimdallr_Server
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Heimdallr_Market
{
    private $_name         = '';
    private $_description  = '';
    private $_symbols      = array();

    public function setDescription($description)
    {
        $this->_description = $description;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function addSymbol($symbol)
    {
        $this->_symbols[] = $symbol;
    }

    public function getSymbol()
    {
        return implode('+', $this->_symbols);
    }

    public function clearSymbols()
    {
        $this->_symbols = array();
    }
}
