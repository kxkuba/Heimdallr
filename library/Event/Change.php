<?php
/**
 * Die Datei enthält die Klasse "{@link Event_Change}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Event
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Prüft, ob der Wert sich geändert hat
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Event
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Event_Change extends Heimdallr_EventAbstract
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
            $file = $this->getPathTmp($template, $key);
            $content = file_get_contents($file);
            if (empty($content)) {
                // Datei ist leer und wird erstmal mit Inhalt gefüllt
                file_put_contents($file, $value);
            } else {
                // Es wird geprüft, ob sie die Zahl geändert hat
                if ($value != $content) {
                    $this->send($template, $this->getMessage(), $key, $value, $content);
                }
            }
        }
    }

}
