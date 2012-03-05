<?php
/**
 * Die Datei enthält die Klasse "{@link Heimdallr_TransmitterAbstract}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 *
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Main
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
abstract class Heimdallr_TransmitterAbstract implements Heimdallr_TransmitterInterface
{

    private $_period = 0;

    /**
     * Setzt die Werte $value und $limit in die Nachricht ein
     *
     * @param  string $message
     * @param  float  $value
     * @param  float  $limit
     * @return string
     */
    public function getMessage($message, $value, $limit)
    {
        return sprintf($message, $value, $limit);
    }

    /**
     * Gibt den Pfad und Dateiname der temporäre Datei von diesem Event zurück
     *
     * @param  Heimdallr_TemplateAbstract $template
     * @param  string                  $key
     * @return string
     */
    public function getPathTmp(Heimdallr_TemplateAbstract $template, $key)
    {
        $event = get_class($this);
        $mode  = 'transmitter_'.substr($event, strrpos($event, '_') + 1).'_'.$template->getMode();
        $type  = str_replace(' ', '', ucwords(str_replace('-', ' ', $template->getRrdtool()->getType())));
        $name  = $template->getServer()->getName().'_'.$type;
        if ($template->getDevice() != '') {
            $name .= '_'.$template->getDevice();
        }
        return Main::getInstance()->getPathTmp($name.'_'.$key, $mode);
    }

    /**
     * Gibt den Pfad und Dateiname der Log Datei von diesem Event zurück
     *
     * @param  Heimdallr_TemplateAbstract $template
     * @param  string                  $key
     * @return string
     */
    public function getPathLog(Heimdallr_TemplateAbstract $template, $key)
    {
        $event = get_class($this);
        $mode  = 'transmitter_'.substr($event, strrpos($event, '_') + 1).'_'.$template->getMode();
        $type  = str_replace(' ', '', ucwords(str_replace('-', ' ', $template->getRrdtool()->getType())));
        $name  = $template->getServer()->getName().'_'.$type;
        if ($template->getDevice() != '') {
            $name .= '_'.$template->getDevice();
        }
        return Main::getInstance()->getPathLog($name.'_'.$key, $mode);
    }

    /**
     * Prüft, wann die letzte Nachricht gesendet wurde ist und das die
     * Nachricht ausserhalb des Wartezeitraum ist
     *
     * @param  Heimdallr_TemplateAbstract $template
     * @param  string                  $key
     * @return bool
     */
    public function canSend(Heimdallr_TemplateAbstract $template, $key)
    {
        // Wann die letzte Nachricht geschickt wurde
        $time = (int) file_get_contents($this->getPathTmp($template, $key));
        // Wartezeit
        if ($this->getPeriod() > 0) {
            $period = $this->getPeriod() * 3600;
        } else {
            $period = Main::getInstance()->getConfig(array('transmitter', 'waitingPeriod'));
        }
        // Verhindert, dass die Nachricht in zu kurzen intervallen verschickt wird
        return ($time == 0 || time() - $time > $period);
    }

    /**
     * Updatet die Zeit, wann die letzte Nachricht abgeschickt wurde, auf jetzt
     *
     * @param Heimdallr_TemplateAbstract $template
     * @param string                   $key
     */
    public function updateTime(Heimdallr_TemplateAbstract $template, $key)
    {
        file_put_contents($this->getPathTmp($template, $key), time());
    }

    /**
     * Gibt die Config zurück (Proxy)
     *
     * @param  array $params
     * @return string
     */
    public function getConfig(array $params)
    {
        $class = get_class($this);
        $name  = strtolower(substr($class, strrpos($class, '_') + 1));
        return Main::getInstance()->getConfig(array_merge(array('transmitter', $name), $params));
    }

    /**
     * Definiert die Wartezeit, bis frühstens die nächste
     * Nachricht geschickt werden darf
     *
     * @param $period
     */
    public function setPeriod($period)
    {
        $this->_period = $period;
    }

    /**
     * Gibt die definierte Wartezeit zurück
     *
     * @return int
     */
    public function getPeriod()
    {
        return (int) $this->_period;
    }

}
