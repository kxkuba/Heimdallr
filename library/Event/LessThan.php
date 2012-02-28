<?php
/**
 * Die Datei enthält die Klasse "{@link Event_LessThan}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Event
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Prüft, ob der Wert kleiner X ist
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Event
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Event_LessThan extends Event_Equal
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
            // Ob der Wert kleiner dem definierten Wert ist
            if ($value < $this->getLimit()) {
                $this->send($template, $this->getMessage(), $key, $value);
            }
        }
    }
}
