<?php
/**
 * Die Datei enthält die Klasse "{@link Script_Weather}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 *
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Script
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Script_Weather extends Heimdallr_StructureAbstract
{
    /**
     * @var Heimdallr_TemplateAbstract
     */
    private $_template = array();

    /**
     * @param Heimdallr_Weather $weather
     * @param Heimdallr_EventInterface|null $event
     */
    public function __construct(Heimdallr_Weather $weather, Heimdallr_EventInterface $event = null)
    {
        $server = new Heimdallr_Server();
        $server->setName($weather->getName());
        $server->setIp($weather->getYahooCode());

        parent::__construct($server, $event);
    }

    /**
     * Initialisiert die Template-Klassen
     */
    protected function init()
    {
        $this->setMode('weather');
        $this->_template = array(
            Template_WeatherHumidity::VALUE_HUMIDITY       => new Template_WeatherHumidity($this),
            Template_WeatherPressure::VALUE_PRESSURE       => new Template_WeatherPressure($this),
            Template_WeatherTemperature::VALUE_TEMPERATURE => new Template_WeatherTemperature($this),
            Template_WeatherWind::VALUE_WIND               => new Template_WeatherWind($this),
        );
    }

    /**
     * Proxy-Methode für das Erstellen der Datenbank
     */
    public function create()
    {
        foreach ($this->_template as $template) {
            /* @var Heimdallr_TemplateAbstract $template */
            $template->create();
        }
    }

    /**
     * Proxy-Methode für das Update der Datenbank
     */
    public function update()
    {
        try {
            $data = array();
            $file = Main::getInstance()->getPathTmp($this->getServer()->getName(), $this->getMode());
            // Alter des Datensatzes
            if (time() - filemtime($file) > (6 * 60) - 20) {
                // zu alt (~6 Minuten)
                $string = file_get_contents('http://weather.yahooapis.com/forecastrss?u=c&w='.$this->getServer()->getIp());
                $patternWind       = '/<yweather:wind[^>]*speed="([0-9\.]*)"[^>]*>/i';
                $patternAtmosphere = '/<yweather:atmosphere[^>]*humidity="([0-9\.]*)"[^>]*pressure="([0-9\.]*)"[^>]*>/i';
                $patternCondition  = '/<yweather:condition[^>]*temp="([0-9\.\-]*)"[^>]*>/i';
                $pregWind       = preg_match ($patternWind, $string, $matchesWind);
                $pregAtmosphere = preg_match ($patternAtmosphere, $string, $matchesAtmosphere);
                $pregCondition  = preg_match ($patternCondition, $string, $matchesCondition);
                if (!($pregWind && $pregAtmosphere && $pregCondition)) {
                    throw new Heimdallr_StructureException('Fehlerhafter XML-Datensatz geladen');
                }
                $data[Template_WeatherWind::VALUE_WIND] = $matchesWind['1'];
                $data[Template_WeatherHumidity::VALUE_HUMIDITY] = $matchesAtmosphere['1'];
                $data[Template_WeatherPressure::VALUE_PRESSURE] = $matchesAtmosphere['2'];
                $data[Template_WeatherTemperature::VALUE_TEMPERATURE] = $matchesCondition['1'];
                file_put_contents($file, json_encode($data));
            } else {
                $data = json_decode(file_get_contents($file), true);
            }
            foreach ($this->_template as $key => $template) {
                /* @var Heimdallr_TemplateAbstract $template */
                if (array_key_exists($key, $data)) {
                    $template->update(array(
                        $key => $data[$key]
                    ));
                }
            }
        } catch (Exception $e) {
            Main::getInstance()->logException($e);
        }
    }

    /**
     * Proxy-Methode für die Erstellungen der Graphen
     */
    public function graph()
    {
        foreach ($this->_template as $template) {
            /* @var Heimdallr_TemplateAbstract $template */
            $template->graph();
        }
    }
}
