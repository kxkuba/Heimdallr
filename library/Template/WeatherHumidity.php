<?php
/**
 * Die Datei enthält die Klasse "{@link Template_WeatherHumidity}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Template für die Luftfeuchtigkeit
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Template_WeatherHumidity extends Heimdallr_TemplateAbstract
{

    const VALUE_HUMIDITY = 'weather_humidity';

    /**
     * Initialisiert die rrdTool-Klasse
     */
    protected function init()
    {
        $this->initRrdtool('humidity');
    }

    /**
     * Erstellt die Datenbank
     */
    public function create()
    {
        $this->getRrdtool()->create(array(
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_HUMIDITY,
                'min'  => 0,
                'max'  => 100,
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
            self::VALUE_HUMIDITY
        ));
        $this->getRrdtool()->update($data);
    }

    /**
     * Erstellt die Graphen von der Datenbank
     */
    public function graph()
    {
        $title = $this->getServer()->getGraphTitle('Luftfeuchtigkeit in', '');
        $label = 'Prozent';
        $name  = $this->adjustText(array(
            self::VALUE_HUMIDITY => 'Luftfeuchtigkeit',
        ));
        $args = array_merge(
            array(
                'DEF:g1ave='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_HUMIDITY.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
                'DEF:g1max='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_HUMIDITY.':'.Heimdallr_Rrdtool::RRD_TYPE_MAX,
                'DEF:g1min='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_HUMIDITY.':'.Heimdallr_Rrdtool::RRD_TYPE_MIN,
            ),
            $this->getRrdtool()->gradientStart('g1ave', '_', 0, '#000000'),
            $this->getRrdtool()->gradient('g1ave', '_',  0, 100, 1, '#000000', Heimdallr_Rrdtool::COLOR_YELLOW, true),
            $this->getRrdtool()->gradientEnd('g1ave', '_', 100, Heimdallr_Rrdtool::COLOR_YELLOW),
            array(
                'LINE1:g1ave'.Heimdallr_Rrdtool::COLOR_YELLOW.':"'.$name[self::VALUE_HUMIDITY].'"',
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
