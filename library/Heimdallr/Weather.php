<?php
/**
 * Die Datei enthält die Klasse "{@link Heimdallr_Weather}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Einstellungen bzw. Informationen über das Wetter
 *
 * Proxy-Klasse von Heimdallr_Server
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Heimdallr_Weather
{
    private $_name = '';
    private $_code = array();

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setYahooCode($code)
    {
        $this->_code = $code;
    }

    public function getYahooCode()
    {
        return $this->_code;
    }

}
