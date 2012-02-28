<?php
return array(
    'messages' => array(
        'event' => array(
            'Change'        => 'Der überwachte Wert hat sich von %1$s auf %2$s geändert',
            'Equal'         => 'Der überwachte Wert ist gleich %2$s',
            'LessThan'      => 'Der überwachte Wert ist kleiner %2$s',
            'LessEqual'     => 'Der überwachte Wert ist kleiner gleich %2$s',
            'GreaterThan'   => 'Der überwachte Wert ist größer %2$s',
            'GreaterEqual'  => 'Der überwachte Wert ist größer gleich %2$s',
            'Log'           => 'Der Wert ist %1$s',
        ),
    ),
    'transmitter' => array(
        // Wartezeit bis die Nachricht nochmal gesendet wird (in Sekunden)
        'waitingPeriod' => 3600,
        // Transmitter: Twitter
        'twitter' => array(
            'active'     => false,
            'ssl'        => true,
            // Default Empfänger, wenn kein spezieller Empfänger Eingetragen
            'receiver'   => '',
            'consumer'   => array(
                'key'    => '',
                'secret' => '',
            ),
            'access'     => array(
                'token'  => '',
                'secret' => '',
            ),
            'api'        => 'api.twitter.com', // ohne http:// oder https://
        ),
        'email' => array(
            'active'     => false,
            'from'       => '',
            // Default Empfänger, wenn kein spezieller Empfänger Eingetragen
            'receiver'   => '',
        ),
    ),
    'rrdtool' => array(
        'step'  => 120,    // in Sekunden
        'range' => array(  // in Stunden
            'day'   => 36,
            'week'  => 168,
            'month' => 720,
            'year'  => 8760,
        ),
        'images' => array(
            array('w' => 600, 'h' => 250, 'postfix' => ''),
            array('w' => 400, 'h' => 200, 'postfix' => 'thumb_400x200'),
        ),
    ),
    'automatic' => array(
        'createDatabase' => true,
    ),
    'path' => array(
        'tmp'       => ROOT.'/data/tmp',
        'database'  => ROOT.'/data/database',
        'images'    => ROOT.'/image',
    ),
    'command' => array(
        'rrdtool'  => '/usr/bin/rrdtool',
        'nice'     => '/usr/bin/nice',
        'snmpGet'  => '/usr/bin/snmpget',
        'snmpWalk' => '/usr/bin/snmpwalk',
        'php5'     => '/usr/bin/php5',
        'ping'     => '/bin/ping',
        'curl'     => '/usr/bin/curl',
    ),
);
