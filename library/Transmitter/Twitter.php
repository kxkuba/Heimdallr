<?php
/**
 * Die Datei enthält die Klasse "{@link Transmitter_Twitter}"
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Transmitter
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */


/**
 * Sendet die Message an Twitter
 *
 * @category Heimdallr
 * @package Heimdallr
 * @subpackage Transmitter
 * @copyright Copyright (c) 2012, Kjell Dießel
 * @author Kjell Dießel
 */
class Transmitter_Twitter extends Heimdallr_TransmitterAbstract
{

    const OAUTH_VERSION          = '1.0';
    const OAUTH_SIGNATURE_METHOD = 'HMAC-SHA1';
    const CURL_METHOD            = 'POST';
    const TWITTER_LENGTH         = 140;

    /**
     * Empfänger
     *
     * @var string
     */
    private $_receiver = '';

    /**
     * Erwartet ein Twitter-Username
     *
     * $waitingPeriod wird in Stunden angegeben
     *
     * @param string $receiver
     * @param int $waitingPeriod
     */
    public function __construct($receiver, $waitingPeriod = 0)
    {
        $receiver = trim($receiver);
        if (empty($receiver)) {
            $receiver = trim($this->getConfig(array('receiver')));
            if (empty($receiver)) {
                throw new Heimdallr_TransmitterException('Es wurde kein Empfänger definiert');
            }
        }
        $this->_receiver = trim($receiver, '@');
        // eigende Wartezeit für die Instance
        if ((int) $waitingPeriod > 0) {
            $this->setPeriod($waitingPeriod);
        }
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
            // Twitter-Funktion ist deaktiviert
            return;
        }
        // Verhindert, dass die Nachricht beim jeden aufruf gesendet wird
        if ($this->canSend($template, $key)) {
            // Parameter
            $paramsServer = array();

            // Art
            // Beispiel: Server
            $paramsServer['mode'] = trim(ucfirst($template->getMode()));

            // Servername
            // Beispiel: www.example.com
            $paramsServer['name'] = trim($template->getServer()->getName());

            // Beschreibung
            // Beispiel: Web-Server
            //$paramsServer['description'] = $template->getServer()->getDescription();

            // IP-Adresse
            // Beispiel: 127.0.0.1
            //$paramsServer['ip'] = $template->getServer()->getIp();

            // Type
            // Beispiel: Network
            if ($template->getMode() != $template->getRrdtool()->getType()) {
                $paramsServer['type'] = str_replace(' ', '', ucwords(str_replace('-', ' ', trim($template->getRrdtool()->getType()))));
            }

            // Device (optional)
            // Beispiel: eth0
            if ($template->getDevice() != '') {
                $paramsServer['type'] .= ' ('.trim($template->getDevice()).')';
            }

            // Key
            if ($template->getMode() != $key) {
                $paramsServer['key'] = trim($key);
            }
            $message  = $this->getReceiver().' '.trim($this->getMessage($message, $value, $limit));
            $message .= PHP_EOL.'['.implode('; ', $paramsServer).']';
            if (strlen($message) > self::TWITTER_LENGTH) {
                $message = substr($message, 0, self::TWITTER_LENGTH - 3).'...';
            }
            $code = $this->sendTweet($message);
            if ($code != 200) {
                throw new Heimdallr_TransmitterException('Fehler bei der Übertragung (Status-Code: '.$code.')');
            }
            // Update der Zeit des letzten tweet
            $this->updateTime($template, $key);
        }
    }

    /**
     * Sendet die Nachricht an Twitter
     *
     * @param  string $message
     * @return int
     * @throws Heimdallr_TransmitterException
     */
    public function sendTweet($message)
    {
        if (strlen($message) == 0 || strlen($message) > self::TWITTER_LENGTH) {
            throw new Heimdallr_TransmitterException('Die Nachricht ist mit '.strlen($message).' zu kurz/lang');
        }
        // Verpackung der Message
        $paramsRequest = array(
            'status' => $message
        );
        // Die Auth-Parameters
        $paramsAuth = array(
            'oauth_version'          => self::OAUTH_VERSION,
            'oauth_signature_method' => self::OAUTH_SIGNATURE_METHOD,
            'oauth_nonce'            => md5(microtime(true).str_pad(mt_rand(0, 1000000), 7, STR_PAD_LEFT).uniqid(time())),
            'oauth_timestamp'        => time(),
            'oauth_consumer_key'     => $this->getConfig(array('consumer', 'key')),
            'oauth_token'            => $this->getConfig(array('access', 'token'))
        );

        $paramsRequest = $this->getSafeEncodeArray($paramsRequest);
        $paramsAuth    = $this->getSafeEncodeArray($paramsAuth);
        // Sort [ Ref: Spec: 9.1.1 (1) ]
        uksort($paramsRequest, 'strcmp');
        uksort($paramsAuth, 'strcmp');
        $paramsMerge = array();
        foreach (array_merge($paramsAuth, $paramsRequest) as $key => $value) {
            $paramsMerge[] = $key.'='.$value;
        }
        // Vorbereitung der Auth-Signature
        $baseString = implode('&', $this->getSafeEncodeArray(array(self::CURL_METHOD, $this->getUrlUpdate(), implode('&', $paramsMerge))));
        $authString = $this->getSafeEncode($this->getConfig(array('consumer', 'secret'))).'&'.$this->getSafeEncode($this->getConfig(array('access', 'secret')));
        // Erweitert die Auth-Parameters um die Auth-Signature
        $paramsAuth['oauth_signature'] = $this->getSafeEncode(base64_encode(hash_hmac('sha1', $baseString, $authString, true)));
        // Sort [ Ref: Spec: 9.1.1 (1) ]
        uksort($paramsAuth, 'strcmp');
        // Baut den Header zusammen
        $header     = array();
        $headerAuth = array();
        foreach ($paramsAuth as $key => $value) {
            $headerAuth[] = $key.'="'.$value.'"';
        }
        $header['Authorization'] = 'OAuth ' . implode(', ', $headerAuth);
        $header['Expect'] = '';
        // Initialisiere CURL
        $c = curl_init();
        curl_setopt_array($c, array(
            CURLOPT_USERAGENT      => 'Honer - Simple rrdTool-Monitoring',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROXY          => false,
            CURLOPT_ENCODING       => '',
            CURLOPT_URL            => $this->getUrlUpdate(),
            CURLOPT_HEADER         => false,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_POST           => true,
        ));
        // Übergibt die POST-Variablen
        $paramsPost = array();
        foreach ($paramsRequest as $key => $value) {
            $paramsPost[] = $key.'='.$value;
        }
        curl_setopt($c, CURLOPT_POSTFIELDS, implode('&', $paramsPost));
        // Übergibt den HTTP Header
        $httpHeader = array();
        foreach ($header as $key => $value) {
            $httpHeader[] = trim($key.': '.$value);
        }
        curl_setopt($c, CURLOPT_HTTPHEADER, $httpHeader);
        // Sendet die Nachricht ab
        curl_exec($c);
        // Status-Code
        $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        return $code;
    }

    /**
     * Wandelt alle nicht-alphanumerischen Zeichen außer -_. vom Key/Value vom Aray um
     *
     * @param  array $params
     * @return array
     */
    public function getSafeEncodeArray(array $params)
    {
        $return = array();
        foreach ($params as $key => $value) {
            $return[$this->getSafeEncode($key)] = $this->getSafeEncode($value);
        }
        return $return;
    }

    /**
     * Wandelt alle nicht-alphanumerischen Zeichen außer -_. vom String um
     *
     * @param  string $param
     * @return mixed|string
     */
    public function getSafeEncode($param)
    {
        if (is_scalar($param)) {
            return str_ireplace(array('+', '%7E'), array(' ', '~'), rawurlencode($param));
        } else {
            return '';
        }
    }

    /**
     * Gibt die komplette URL für ein Tweet-Update
     *
     * @return string
     */
    public function getUrlUpdate()
    {
        return $this->getHttpTwitterApi().'/1/statuses/update.json';
    }

    /**
     * Gibt die URL von der Twitter API zurück
     *
     * @return string
     */
    public function getHttpTwitterApi()
    {
        return $this->getHttpProtocol().trim($this->getConfig(array('api')), '/');
    }

    /**
     * Gibt die Protokollart zurück (http oder https)
     *
     * @return string
     */
    public function getHttpProtocol()
    {
        return $this->getConfig(array('ssl')) ? 'https://' : 'http://';
    }

    /**
     * Gibt den Empfänger zurück
     *
     * @return string
     */
    public function getReceiver()
    {
        return '@'.$this->_receiver;
    }

}
