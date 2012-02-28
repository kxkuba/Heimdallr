<?php
/**
 * Die Datei enthält die Klasse "{@link Template_UserOnline}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Template für die Anzahl der Benutzer die online sind
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Template
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Template_UserOnline extends Heimdallr_TemplateAbstract
{

    const VALUE_USER_ONLINE = 'user_online';

    /**
     * Initialisiert die rrdTool-Klasse
     */
    protected function init()
    {
        $this->initRrdtool('user-online');
    }

    /**
     * Erstellt die Datenbank
     */
    public function create()
    {
        $this->getRrdtool()->create(array(
            array(
                'mode' => Heimdallr_Rrdtool::RRD_MODE_GAUGE,
                'name' => self::VALUE_USER_ONLINE,
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
            self::VALUE_USER_ONLINE
        ));
        $this->getRrdtool()->update($data);
    }

    /**
     * Erstellt die Graphen von der Datenbank
     */
    public function graph()
    {
        $title = $this->getServer()->getGraphTitle('Anzahl der eingeloggten Benutzer vom ', '');
        $label = 'Anzahl der Benutzer';
        $name  = $this->adjustText(array(
            self::VALUE_USER_ONLINE => 'Anzahl'
        ));
        $args = array(
            '--lower-limit 0',
            'DEF:g1='.$this->getRrdtool()->getPathDatabase().':'.self::VALUE_USER_ONLINE.':'.Heimdallr_Rrdtool::RRD_TYPE_MAX,
            'AREA:g1'.Heimdallr_Rrdtool::COLOR_DEFAULT.Heimdallr_Rrdtool::TEXT_FORMAT_ALPHA,
            'LINE2:g1'.Heimdallr_Rrdtool::COLOR_DEFAULT.':"'.$name[self::VALUE_USER_ONLINE].'"',
            'GPRINT:g1:LAST:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_ACTUAL.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:AVERAGE:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_AVERAGE.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MAX:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MAXIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'"',
            'GPRINT:g1:MIN:"'.Heimdallr_Rrdtool::TEXT_DESCRIPTION_MINIMUM.'\: '.Heimdallr_Rrdtool::TEXT_FORMAT_NUMBER.'\j"',
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
