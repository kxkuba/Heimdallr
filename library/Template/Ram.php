<?php
/**
 * Die Datei enthält die Klasse "{@link Template_Ram}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Template für die Auslastung des Ram's
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Template_Ram extends Heimdallr_TemplateAbstract
{

    const VALUE_FREE   = 'ram_free';
    const VALUE_BUFFER = 'ram_buffer';
    const VALUE_CACHE  = 'ram_cache';
    const VALUE_USAGE  = 'ram_usage';

    /**
     * Initialisiert die rrdTool-Klasse
     */
    protected function init()
    {
        $this->initRrdtool('ram');
    }

    /**
     * Erstellt die Datenbank
     */
    public function create()
    {
        $this->getRrdtool()->create(array(
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_FREE,
                'min'  => 0,
                //'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_BUFFER,
                'min'  => 0,
                //'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_CACHE,
                'min'  => 0,
                //'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_USAGE,
                'min'  => 0,
                //'max'  => 100,
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
            self::VALUE_FREE,
            self::VALUE_BUFFER,
            self::VALUE_CACHE,
            self::VALUE_USAGE
        ));
        $this->getRrdtool()->update($data);
    }

    /**
     * Erstellt die Graphen von der Datenbank
     */
    public function graph()
    {
        $title = $this->getServer()->getGraphTitle('RAM Auslastung vom', '');
        $label = 'Bytes';
        $name  = $this->adjustText(array(
            self::VALUE_FREE   => 'Free RAM',
            self::VALUE_BUFFER => 'Buffer RAM',
            self::VALUE_CACHE  => 'Cached RAM',
            self::VALUE_USAGE  => 'Usage RAM'
        ));
        $args = array(
            '--lower-limit 0',
            'DEF:g1='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_FREE.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g2='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_BUFFER.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g3='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_CACHE.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g4='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_USAGE.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'AREA:g4'.Heimdallr_Rrdtool::COLOR_RED.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA.':"'.$name[self::VALUE_USAGE].'"',
            'GPRINT:g4:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g4:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g4:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g4:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'AREA:g3'.Heimdallr_Rrdtool::COLOR_YELLOW.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA.':"'.$name[self::VALUE_CACHE].'":STACK',
            'GPRINT:g3:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'AREA:g2'.Heimdallr_Rrdtool::COLOR_BLUE.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA.':"'.$name[self::VALUE_BUFFER].'":STACK',
            'GPRINT:g2:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'AREA:g1'.Heimdallr_Rrdtool::COLOR_GREEN.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA.':"'.$name[self::VALUE_FREE].'":STACK',
            'GPRINT:g1:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',

            'LINE1:g4'.Heimdallr_Rrdtool::COLOR_RED,
            'LINE1:g3'.Heimdallr_Rrdtool::COLOR_YELLOW.'::STACK',
            'LINE1:g2'.Heimdallr_Rrdtool::COLOR_BLUE.'::STACK',
            'LINE1:g1'.Heimdallr_Rrdtool::COLOR_GREEN.'::STACK',
        );
        // Tages-Ansicht
        $this->getRrdtool()->graphDay($title, $label, 1024, $args);
        // Löscht die "aktuellen" Werte
        $args = $this->removeLast($args);
        // Wochen-Ansicht
        $this->getRrdtool()->graphWeek($title, $label, 1024, $args);
        // Monats-Ansicht
        $this->getRrdtool()->graphMonth($title, $label, 1024, $args);
        // Jahr-Ansicht
        $this->getRrdtool()->graphYear($title, $label, 1024, $args);
    }
}
