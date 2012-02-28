<?php
/**
 * Die Datei enthält die Klasse "{@link Transmitter_Email}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Transmitter
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 *
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Transmitter
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Transmitter_Email extends Heimdallr_TransmitterAbstract
{
    /**
     * Die E-Mail Adresse des Empfängers
     *
     * @var string
     */
    private $_receiver = '';

    public function __construct($receiver)
    {
        if (!preg_match('/^(.+)@([^@]+)$/', $receiver)) {
            $receiver = $this->getConfig(array('receiver'));
            if (!preg_match('/^(.+)@([^@]+)$/', $receiver)) {
                throw new Heimdallr_TransmitterException('Die Struktur der E-Mail Adressen ist fehlerhaft');
            }
        }
        $this->_receiver = $receiver;
    }

    /**
     * Wird durch ein Event ausgelöst
     *
     * @param Heimdallr_TemplateAbstract $template
     * @param string                  $message
     * @param string                  $key
     * @param float                   $value
     * @param float                   $limit
     */
    public function send(Heimdallr_TemplateAbstract $template, $message, $key, $value, $limit)
    {
        if (!$this->getConfig(array('active'))) {
            // E-Mail-Funktion ist deaktiviert
            return;
        }
        // Verhindert, dass die Nachricht beim jeden aufruf gesendet wird
        if ($this->canSend($template, $key)) {
            //
            // Body
            //
            $message = 'Hello'.PHP_EOL.PHP_EOL.trim($this->getMessage($message, $value, $limit)).PHP_EOL.PHP_EOL;
            // Zusammenfassung der Daten
            $message .= 'Overview'.PHP_EOL;
            // Art
            // Beispiel: Server
            $message .= 'Mode:        '.trim(ucfirst($template->getMode())).PHP_EOL;
            // Servername
            // Beispiel: www.example.com
            $message .= 'Name:        '.trim($template->getServer()->getName()).PHP_EOL;
            // Beschreibung
            // Beispiel: Web-Server
            $message .= 'Description: '.trim($template->getServer()->getDescription()).PHP_EOL;
            // IP-Adresse
            // Beispiel: 127.0.0.1
            $message .= 'Address:     '.trim($template->getServer()->getIp()).PHP_EOL;
            // Type
            // Beispiel: Network
            $message .= 'Type:        '.str_replace(' ', '', ucwords(str_replace('-', ' ', trim($template->getRrdtool()->getType())))).PHP_EOL;
            // Device (optional)
            // Beispiel: eth0
            $message .= 'Device:      '.trim($template->getDevice()).PHP_EOL;
            // Key
            $message .= 'Key:         '.trim($key).PHP_EOL;
            // Abspann
            $message .= PHP_EOL.'Sincerely yours'.PHP_EOL.'Heimdallr-Project';
            // Header
            $header  = 'From: '.$this->getConfig(array('from'))."\r\n";
            $header .= 'Reply-To: '.$this->getConfig(array('from'))."\r\n";
            $header .= 'X-Mailer: Heimdallr V'.Main::VERSION;

            if (!@mail($this->getReceiver(), '[Heimdallr] Triggering an event: '.trim($template->getServer()->getName()), $message, $header)) {
                throw new Heimdallr_TransmitterException('Die Mail wurde nicht versendet');
            }
            // Update der Zeit der letzten E-Mail
            $this->updateTime($template, $key);
        }
    }

    /**
     * Gibt den Empfänger zurück
     *
     * @return string
     */
    public function getReceiver()
    {
        return $this->_receiver;
    }

}
