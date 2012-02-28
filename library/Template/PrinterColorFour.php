<?php
/**
 * Die Datei enthält die Klasse "{@link Template_PrinterColorFour}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Template für den Verbrauch der Farben vom Drucker
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Template_PrinterColorFour extends Heimdallr_TemplateAbstract
{
    const VALUE_BLACK   = 'print-color-black';
    const VALUE_YELLOW  = 'print-color-yellow';
    const VALUE_CYAN    = 'print-color-cyan';
    const VALUE_MAGENTA = 'print-color-magenta';

    /**
     * Initialisiert die rrdTool-Klasse
     */
    protected function init()
    {
        $this->initRrdtool('printer-color');
    }

    /**
     * Erstellt die Datenbank
     */
    public function create()
    {
        $this->getRrdtool()->create(array(
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_BLACK,
                'min'  => 0,
                'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_YELLOW,
                'min'  => 0,
                'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_CYAN,
                'min'  => 0,
                'max'  => 100,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_MAGENTA,
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
            self::VALUE_BLACK,
            self::VALUE_YELLOW,
            self::VALUE_CYAN,
            self::VALUE_MAGENTA,
        ));
        $this->getRrdtool()->update($data);
    }

    /**
     * Erstellt die Graphen von der Datenbank
     */
    public function graph()
    {
        $title = $this->getServer()->getGraphTitle('Farb-Füllstand vom', '');
        $label = 'Prozent';
        $name  = $this->adjustText(array(
            self::VALUE_BLACK   => 'Schwarz',
            self::VALUE_YELLOW  => 'Gelb',
            self::VALUE_CYAN    => 'Cyan',
            self::VALUE_MAGENTA => 'Magenta',
        ));

        // Original Array
        $args = array(
            '--lower-limit 0',
            '--upper-limit 100',
            'DEF:g1='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_BLACK.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g2='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_YELLOW.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g3='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_CYAN.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g4='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_MAGENTA.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'LINE1:g1'.Heimdallr_Rrdtool::COLOR_BLACK.':"'.$name[self::VALUE_BLACK].'"',
            'GPRINT:g1:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'LINE1:g2'.Heimdallr_Rrdtool::COLOR_YELLOW.':"'.$name[self::VALUE_YELLOW].'"',
            'GPRINT:g2:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'LINE1:g3'.Heimdallr_Rrdtool::COLOR_CYAN.':"'.$name[self::VALUE_CYAN].'"',
            'GPRINT:g3:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g3:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'LINE1:g4'.Heimdallr_Rrdtool::COLOR_MAGENTA.':"'.$name[self::VALUE_MAGENTA].'"',
            'GPRINT:g4:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g4:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g4:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g4:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
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
