<?php
/**
 * Die Datei enthält die Klasse "{@link Template_Load}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Template für die Load Average
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Template_Load extends Heimdallr_TemplateAbstract
{

    const VALUE_LOAD_01 = 'load_average_01';
    const VALUE_LOAD_05 = 'load_average_05';
    const VALUE_LOAD_15 = 'load_average_15';

    /**
     * Initialisiert die rrdTool-Klasse
     */
    protected function init()
    {
        $this->initRrdtool('load');
    }

    /**
     * Erstellt die Datenbank
     */
    public function create()
    {
        $this->getRrdtool()->create(array(
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_LOAD_01,
                'min'  => 0,
                //'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_LOAD_05,
                'min'  => 0,
                //'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_LOAD_15,
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
            self::VALUE_LOAD_01,
            self::VALUE_LOAD_05,
            self::VALUE_LOAD_15
        ));
        $this->getRrdtool()->update($data);
    }

    /**
     * Erstellt die Graphen von der Datenbank
     */
    public function graph()
    {
        $title = $this->getServer()->getGraphTitle('Load Average vom', '');
        $label = 'Load Average';
        $name  = $this->adjustText(array(
            self::VALUE_LOAD_01 => 'Load  1',
            self::VALUE_LOAD_05 => 'Load  5',
            self::VALUE_LOAD_15 => 'Load 15',
        ));
        $args = array(
            '--lower-limit 0',
            'DEF:g1='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_LOAD_01.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g2='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_LOAD_05.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g3='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_LOAD_15.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'AREA:g3'.Heimdallr_Rrdtool::COLOR_RED.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA.':"'.$name[self::VALUE_LOAD_15].'"',
            'GPRINT:g3:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'AREA:g2'.Heimdallr_Rrdtool::COLOR_YELLOW.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA.':"'.$name[self::VALUE_LOAD_05].'":STACK',
            'GPRINT:g2:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'AREA:g1'.Heimdallr_Rrdtool::COLOR_GREEN.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA.':"'.$name[self::VALUE_LOAD_01].'":STACK',
            'GPRINT:g1:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'LINE1:g3'.Heimdallr_Rrdtool::COLOR_RED,
            'LINE1:g2'.Heimdallr_Rrdtool::COLOR_YELLOW.'::STACK',
            'LINE1:g1'.Heimdallr_Rrdtool::COLOR_GREEN.'::STACK',
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
