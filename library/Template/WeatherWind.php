<?php
/**
 * Die Datei enthält die Klasse "{@link Template_WeatherWind}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Template für die Windgeschwindigkeit
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Template_WeatherWind extends Heimdallr_TemplateAbstract
{

    const VALUE_WIND = 'weather_wind';

    /**
     * Initialisiert die rrdTool-Klasse
     */
    protected function init()
    {
        $this->initRrdtool('wind');
    }

    /**
     * Erstellt die Datenbank
     */
    public function create()
    {
        $this->getRrdtool()->create(array(
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_WIND,
                'min'  => 0,
            ),
        ), array(
            Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            Heimdallr_Rrdtool::RRD_TYPE_MAX,
            Heimdallr_Rrdtool::RRD_TYPE_MIN,
        ));
    }

    /**
     * Updatet die Datenbank
     *
     * @param array $data
     */
    public function update(array $data)
    {
        $this->validData($data, array(
            self::VALUE_WIND
        ));
        $this->getRrdtool()->update($data);
    }

    /**
     * Erstellt die Graphen von der Datenbank
     */
    public function graph()
    {
        $title = $this->getServer()->getGraphTitle('Windgeschwindigkeit in', '');
        $label = 'km/h';
        $name  = $this->adjustText(array(
            self::VALUE_WIND => 'Windgeschwindigkeit',
        ));
        $args = array_merge(
            array(
                'DEF:g1ave='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_WIND.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
                'DEF:g1max='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_WIND.':'.Heimdallr_Rrdtool::RRD_TYPE_MAX,
                'DEF:g1min='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_WIND.':'.Heimdallr_Rrdtool::RRD_TYPE_MIN,
            ),
            $this->getRrdtool()->gradientStart('g1ave', '_', 0, '#000000'),
            $this->getRrdtool()->gradient('g1ave', '_',  0, 20, 0.5, '#000000', Heimdallr_Rrdtool::COLOR_TURQUOISE, true),
            $this->getRrdtool()->gradientEnd('g1ave', '_', 20, Heimdallr_Rrdtool::COLOR_TURQUOISE),
            array(
                'LINE1:g1ave'.Heimdallr_Rrdtool::COLOR_TURQUOISE.':"'.$name[self::VALUE_WIND].'"',
                'GPRINT:g1ave:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
                'GPRINT:g1ave:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
                'GPRINT:g1max:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
                'GPRINT:g1min:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            )
        );
        // Tages-Ansicht
        $this->getRrdtool()->graphDay($title, $label, 1000, $args);
        // Löscht die "aktuellen" Werte
        $args = $this->removeLast($args);
        // Wochen-Ansicht
        $this->getRrdtool()->graphWeek($title, $label, 1000, $args);
        // Monats-Ansicht
        $this->getRrdtool()->graphMonth($title, $label, 1000, $args);
        // Jahr-Ansicht
        $this->getRrdtool()->graphYear($title, $label, 1000, $args);
    }
}
