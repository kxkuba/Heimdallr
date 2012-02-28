<?php
/**
 * Die Datei enthält die Klasse "{@link Event_Equal}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Event
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Prüft, ob der Wert gleich X ist
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Event
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Event_Equal extends Heimdallr_EventAbstract
{

    /**
     * Prüft, ob das definierte Ereigniss eintritt
     *
     * @param Heimdallr_TemplateAbstract $template
     * @param string $key
     * @param float  $value
     */
    public function verify(Heimdallr_TemplateAbstract $template, $key, $value)
    {
        // Ob es der passende Schlüssel ist
        if ($key == $this->getKey()) {
            // Ob der Wert gleich dem definierten Wert ist
            if ($value == $this->getLimit()) {
                $this->send($template, $this->getMessage(), $key, $value);
            }
        }
    }
}
