<?php
/**
 * Die Datei enthält die Klasse "{@link Template_Cpu}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Template für die CPU-Benutzung
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Template_Cpu extends Heimdallr_TemplateAbstract
{

    const VALUE_USER   = 'cpu_user';
    const VALUE_NICE   = 'cpu_nice';
    const VALUE_SYSTEM = 'cpu_system';
    const VALUE_IDLE   = 'cpu_idle';

    /**
     * Initialisiert die rrdTool-Klasse
     */
    protected function init()
    {
        $this->initRrdtool('cpu');
    }

    /**
     * Erstellt die Datenbank
     */
    public function create()
    {
        $this->getRrdtool()->create(array(
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_COUNTER,
                'name' => self::VALUE_USER,
                'min'  => 0,
                //'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_COUNTER,
                'name' => self::VALUE_NICE,
                'min'  => 0,
                //'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_COUNTER,
                'name' => self::VALUE_SYSTEM,
                'min'  => 0,
                //'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_COUNTER,
                'name' => self::VALUE_IDLE,
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
            self::VALUE_USER,
            self::VALUE_NICE,
            self::VALUE_SYSTEM,
            self::VALUE_IDLE,
        ));
        $this->getRrdtool()->update($data);
    }

    /**
     * Erstellt die Graphen von der Datenbank
     */
    public function graph()
    {
        $title = $this->getServer()->getGraphTitle('CPU Auslastung vom', '');
        $label = 'Prozent';
        $name  = $this->adjustText(array(
            self::VALUE_USER   => 'User',
            self::VALUE_NICE   => 'Nice',
            self::VALUE_SYSTEM => 'System',
            self::VALUE_IDLE   => 'Idle',
        ));
        $args = array(
            '--lower-limit 0',
            'DEF:g1='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_USER.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g2='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_NICE.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g3='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_SYSTEM.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g4='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_IDLE.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'AREA:g3'.Heimdallr_Rrdtool::COLOR_RED.':"'.$name[self::VALUE_SYSTEM].'"',
            'GPRINT:g3:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'AREA:g2'.Heimdallr_Rrdtool::COLOR_YELLOW.':"'.$name[self::VALUE_NICE].'":STACK',
            'GPRINT:g2:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'AREA:g1'.Heimdallr_Rrdtool::COLOR_BLUE.':"'.$name[self::VALUE_SYSTEM].'":STACK',
            'GPRINT:g1:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'AREA:g4'.Heimdallr_Rrdtool::COLOR_GREEN.':"'.$name[self::VALUE_IDLE].'":STACK',
            'GPRINT:g4:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g4:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g4:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g4:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            /*
            'LINE1:g3'.Heimdallr_Rrdtool::COLOR_RED,
            'LINE1:g2'.Heimdallr_Rrdtool::COLOR_YELLOW.'::STACK',
            'LINE1:g1'.Heimdallr_Rrdtool::COLOR_BLUE.'::STACK',
            'LINE1:g4'.Heimdallr_Rrdtool::COLOR_GREEN.'::STACK',
            //*/
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
