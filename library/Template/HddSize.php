<?php
/**
 * Die Datei enthält die Klasse "{@link Template_HddSize}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Template für die Größe der Festplatten
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Template_HddSize extends Heimdallr_TemplateAbstract
{

    const VALUE_FREE  = 'hdd_size_free';
    const VALUE_USAGE = 'hdd_size_usage';

    /**
     * Initialisiert die rrdTool-Klasse
     */
    protected function init()
    {
        $this->initRrdtool('hdd-size');
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
            self::VALUE_USAGE,
        ));
        $this->getRrdtool()->update($data);
    }

    /**
     * Erstellt die Graphen von der Datenbank
     */
    public function graph()
    {
        $title = $this->getServer()->getGraphTitle('Auslastung der HDD '.$this->getMode().' vom', '');
        $label = 'Bytes';
        $name  = $this->adjustText(array(
            self::VALUE_FREE  => 'Frei',
            self::VALUE_USAGE => 'Belegt',
        ));
        $args = array(
            '--lower-limit 0',
            'DEF:g1='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_FREE.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g2='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_USAGE.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'AREA:g2'.Heimdallr_Rrdtool::COLOR_RED.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA.':"'.$name[self::VALUE_USAGE].'"',
            'GPRINT:g2:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'AREA:g1'.Heimdallr_Rrdtool::COLOR_GREEN.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA.':"'.$name[self::VALUE_FREE].'":STACK',
            'GPRINT:g1:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'LINE1:g2'.Heimdallr_Rrdtool::COLOR_RED,
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
