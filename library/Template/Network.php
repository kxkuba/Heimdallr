<?php
/**
 * Die Datei enthält die Klasse "{@link Template_Network}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Template für Netzwerk-Traffic
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Template_Network extends Heimdallr_TemplateAbstract
{

    const VALUE_INPUT  = 'network_input';
    const VALUE_OUTPUT = 'network_output';

    /**
     * Initialisiert die rrdTool-Klasse
     */
    protected function init()
    {
        $this->initRrdtool('network');
    }

    /**
     * Erstellt die Datenbank
     */
    public function create()
    {
        $this->getRrdtool()->create(array(
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_COUNTER,
                'name' => self::VALUE_INPUT,
                'min'  => 0,
            ),
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_COUNTER,
                'name' => self::VALUE_OUTPUT,
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
            self::VALUE_INPUT,
            self::VALUE_OUTPUT
        ));
        $this->getRrdtool()->update($data);
    }

    /**
     * Erstellt die Graphen von der Datenbank
     */
    public function graph()
    {
        $title = $this->getServer()->getGraphTitle('Auslastung des Netzwerks '.$this->getMode().' vom', '');
        $label = 'Bytes/s';
        $name  = $this->adjustText(array(
            self::VALUE_INPUT  => 'Input',
            self::VALUE_OUTPUT => 'Output',
        ));
        $args = array(
            'DEF:g1ave='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_INPUT.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g1max='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_INPUT.':'.Heimdallr_Rrdtool::RRD_TYPE_MAX,
            'DEF:g1min='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_INPUT.':'.Heimdallr_Rrdtool::RRD_TYPE_MIN,
            'DEF:g2ave='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_OUTPUT.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g2max='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_OUTPUT.':'.Heimdallr_Rrdtool::RRD_TYPE_MAX,
            'DEF:g2min='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_OUTPUT.':'.Heimdallr_Rrdtool::RRD_TYPE_MIN,
            'CDEF:g2m=g2ave,-1,*',
            'AREA:g2m'.Heimdallr_Rrdtool::COLOR_RED.':"'.$name[self::VALUE_OUTPUT].'"',
            'GPRINT:g2ave:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2ave:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2max:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g2min:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
            'AREA:g1ave'.Heimdallr_Rrdtool::COLOR_GREEN.':"'.$name[self::VALUE_INPUT].'"',
            'GPRINT:g1ave:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1ave:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1max:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1min:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
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
