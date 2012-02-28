<?php
/**
 * Die Datei enthält die Klasse "{@link Event_Log}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Event
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Loggt den Wert, sollte nur für den Debug-Modus benutzt werden
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Event
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Event_Log extends Heimdallr_EventAbstract
{

    /**
     * Prüft, ob das definierte Ereigniss eintritt (debug only !!)
     *
     * @param Heimdallr_TemplateAbstract $template
     * @param string $key
     * @param float  $value
     */
    public function verify(Heimdallr_TemplateAbstract $template, $key, $value)
    {
        // Ob es der passende Schlüssel ist
        if ($key == $this->getKey()) {
            $this->send($template, $this->getMessage(), $key, $value);
        }
    }

}
