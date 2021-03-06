<?php
/**
 * Die Datei enthält die Klasse "{@link Template_UserCount}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Template für die einzelne Benutzer die gerade online sind
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Template_UserCount extends Heimdallr_TemplateAbstract
{

    const VALUE_USER_COUNT = 'user_count';

    /**
     * Initialisiert die rrdTool-Klasse
     */
    protected function init()
    {
        $this->initRrdtool('user-count');
    }

    /**
     * Erstellt die Datenbank
     */
    public function create()
    {
        $this->getRrdtool()->create(array(
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_USER_COUNT,
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
            self::VALUE_USER_COUNT
        ));
        $this->getRrdtool()->update($data);
    }

    /**
     * Erstellt die Graphen von der Datenbank
     */
    public function graph()
    {
        $title = $this->getServer()->getGraphTitle('Online Zeit vom Benutzer '.$this->getMode().' von  ', '');
        $label = 'Online';
        $name  = $this->adjustText(array(
            self::VALUE_USER_COUNT => $this->getMode()
        ));

        $args = array(
            '--lower-limit 0',
            'DEF:g1ave='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_USER_COUNT.':'.Heimdallr_Rrdtool::RRD_TYPE_AVERAGE,
            'DEF:g1max='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_USER_COUNT.':'.Heimdallr_Rrdtool::RRD_TYPE_MAX,
            'DEF:g1min='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_USER_COUNT.':'.Heimdallr_Rrdtool::RRD_TYPE_MIN,
            'AREA:g1max'.Heimdallr_Rrdtool::COLOR_DEFAULT.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA,
            'LINE2:g1max'.Heimdallr_Rrdtool::COLOR_DEFAULT.':"'.$name[self::VALUE_USER_COUNT].'"',
            'GPRINT:g1ave:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1ave:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1max:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1min:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
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
