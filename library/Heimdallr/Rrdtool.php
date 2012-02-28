<?php
/**
 * Die Datei enthält die Klasse "{@link Heimdallr_Rrdtool}"
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
class Heimdallr_Rrdtool
{

    const TEXT_FORMAT_NUMBER = '%7.3lf%s';
    const TEXT_FORMAT_ALPHA  = '44';

    const TEXT_DESCRIPTION_ACTUAL  = 'Aktuell';
    const TEXT_DESCRIPTION_AVERAGE = 'Mittelwert';
    const TEXT_DESCRIPTION_MINIMUM = 'Minimum';
    const TEXT_DESCRIPTION_MAXIMUM = 'Maximum';

    const COLOR_DEFAULT   = '#FF3D00';
    const COLOR_ORANGE    = '#FF9900';
    const COLOR_BLACK     = '#FFFFFF';
    const COLOR_RED       = '#FF0000';
    const COLOR_GREEN     = '#00AA00';
    const COLOR_BLUE      = '#3300FF';
    const COLOR_YELLOW    = '#DFFF00';
    const COLOR_TURQUOISE = '#00CCFF';
    const COLOR_CYAN      = '#00B7EB';
    const COLOR_MAGENTA   = '#FF0090';

    const RRD_MODE_COUNTER  = 'COUNTER';
    const RRD_MODE_GAUGE    = 'GAUGE';
    const RRD_MODE_DERIVE   = 'DERIVE';
    const RRD_MODE_ABSOLUTE = 'ABSOLUTE';

    const RRD_TYPE_AVERAGE = 'AVERAGE';
    const RRD_TYPE_MAX     = 'MAX';
    const RRD_TYPE_MIN     = 'MIN';
    const RRD_TYPE_LAST    = 'LAST';

    private $_template = null;
    private $_type     = '';
    private $_time     = 0;

    public function __construct(Heimdallr_TemplateAbstract $template, $type)
    {
        $this->_template = $template;
        $this->_type     = $type;
        $this->_time     = time();
    }

    /**
     * @return Heimdallr_TemplateAbstract|null
     */
    public function getTemplate()
    {
        return$this->_template;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Gibt den vollständigen Dateipfad zu der rrd-Datenbank zurück
     *
     * @return string
     */
    public function getPathDatabase()
    {
        $mode   = $this->getTemplate()->getMode();
        $name   = $this->getTemplate()->getServer()->getName();
        $type   = $this->getType();
        $device = $this->getTemplate()->getDevice();
        $file   = str_replace(' ', '_', strtolower($mode.'_'.$name.'_'.$type.(!empty($device) ? '_'.$device : '')));

        return Main::getInstance()->getPathDatabase($file);
    }

    /**
     * Gibt den vollständigen Dateipfad zu dem Bild zurück
     *
     * @param  string $period
     * @return string
     */
    public function getPathImage($period)
    {
        $mode   = $this->getTemplate()->getMode();
        $name   = $this->getTemplate()->getServer()->getName();
        $type   = $this->getType();
        $device = $this->getTemplate()->getDevice();
        $file   = str_replace(' ', '_', strtolower($name.'_'.$type.(empty($device) ? '' : '_'.$device).'_'.$period));

        return Main::getInstance()->getPathImage($file, $mode);
    }

    /**
     *
     * Struktur:
     * $ds = array(
     *   'mode' => '',
     *   'name' => '',
     *  ['min'  => '',]
     *  ['max'  => '',]
     * );
     *
     * @param  array $ds
     * @param  array $types
     * @return array|null
     */
    public function create(array $ds, array $types)
    {
        if (!file_exists($this->getPathDatabase())) {
            $args = array(
                'create',
                $this->getPathDatabase(),
                '--step '.Main::getInstance()->getRrdtoolStep()
            );
            foreach ($ds as $item) {
                $item['min'] = array_key_exists('min', $item) ? $item['min'] : 'U';
                $item['max'] = array_key_exists('max', $item) ? $item['max'] : 'U';
                $args[] = 'DS:'.$item['name'].':'.$item['mode'].':'.Main::getInstance()->getRrdtoolWait().':'.$item['min'].':'.$item['max'].'';
            }
            foreach (Main::getInstance()->getRrdtoolRang() as $item) {
                $seconds = floor(($item['hour'] * 3600) / ($item['count'] * Main::getInstance()->getRrdtoolStep()));
                foreach ($types as $type) {
                    $args[] = 'RRA:'.$type.':0.5:'.$item['count'].':'.$seconds;
                }
            }

            Main::getInstance()->exec(Main::getInstance()->getConfig(array('command', 'rrdtool')), $args);
        }
    }

    public function update(array $data)
    {
        if (Main::getInstance()->getConfig(array('automatic', 'createDatabase'))) {
            $this->getTemplate()->create();
        }

        if (file_exists($this->getPathDatabase())) {
            // Prüft, ob ein Event vorliegt...
            if ($this->getTemplate()->getEvent() instanceof Heimdallr_EventInterface) {
                // ... und übergibt die Werte zum prüfen.
                foreach ($data as $key => $value) {
                    try {
                        $this->getTemplate()->getEvent()->verify($this->getTemplate(), $key, $value);
                    } catch (Exception $e) {
                        Main::getInstance()->logException($e);
                    }
                }
            }
            $rrdKeys   = array();
            $rrdValues = array();
            foreach ($data as $key => $value) {
                $rrdKeys[]   = $key;
                $rrdValues[] = $value;
            }
            $args = array(
                'update',
                $this->getPathDatabase(),
                '-t '.implode(':', $rrdKeys),
                'N:'.implode(':', $rrdValues),
            );

            Main::getInstance()->exec(Main::getInstance()->getConfig(array('command', 'rrdtool')), $args);
        }
    }

    public function graph($title, $label, $base, $period, array $args)
    {
        if (file_exists($this->getPathDatabase())) {
            $images = Main::getInstance()->getConfig(array('rrdtool', 'images'));
            foreach ($images as $image) {
                if (array_key_exists('w', $image) && array_key_exists('h', $image) && array_key_exists('postfix', $image)) {
                    $this->_graph($title, $label, $base, $period, $args, $image['w'], $image['h'], $image['postfix']);
                }
            }
        }
    }

    /**
     * @param string $title
     * @param string $label
     * @param string $base
     * @param string $period
     * @param array  $args
     * @param int    $width
     * @param int    $height
     * @param string $postfix
     */
    protected function _graph($title, $label, $base, $period, array $args, $width, $height, $postfix)
    {
        $args = array_merge(array(
            '-n 19',
            Main::getInstance()->getConfig(array('command', 'rrdtool')),
            'graph',
            $this->getPathImage($period.(empty($postfix) ? '' : '_'.$postfix)),
            '-a PNG',
            '-w '.$width,
            '-h '.$height,
            '--base '.$base,
            '--color BACK#1e1816',
            '--color FONT#ffffff',
            '--color CANVAS#1e1816',
            '--color GRID#4E403C7F',
            '--color MGRID#4e403c',
            '--color FRAME#EEEEEE',
            '--color ARROW#4E403C',
            '--border 0',
            '--grid-dash 1:0',
            '--dynamic-labels',
            '--vertical-label "'.$label.'"',
            '-t "'.$title.'"',
            '--end -60',
        ), $args, array(
            // date() demaskiert den Doppelpunkt (":"), was zu rrdtool-Fehler führt
            'COMMENT:"'.date('d.m.Y H', $this->_time).'\:'.date('i', $this->_time).'\l"',
            'COMMENT:"\u"',
            'COMMENT:"Honer (www.kettil.de)\r"',
        ));
        Main::getInstance()->exec(Main::getInstance()->getConfig(array('command', 'nice')), $args);
    }

    public function graphDay($title, $label, $base, array $args)
    {
        $rra = Main::getInstance()->getRrdtoolRang();
        $args = array_merge(array(
            '--start '.floor($rra['day']['hour'] * -3600),
            '--x-grid HOUR:3:HOUR:12:HOUR:3:0:"%H:%M"',
        ), $args);
        return $this->graph($title, $label, $base, 'day', $args);
    }

    public function graphWeek($title, $label, $base, array $args)
    {
        $rra = Main::getInstance()->getRrdtoolRang();
        $args = array_merge(array(
            '--start '.floor($rra['week']['hour'] * -3600),
            '--x-grid HOUR:6:DAY:1:DAY:1:'.floor(24 * 3600).':"%A"',
        ), $args);
        return $this->graph($title, $label, $base, 'week', $args);
    }

    public function graphMonth($title, $label, $base, array $args)
    {
        $rra = Main::getInstance()->getRrdtoolRang();
        $args = array_merge(array(
            '--start '.floor($rra['month']['hour'] * -3600),
            '--x-grid DAY:1:WEEK:1:WEEK:1:'.floor(168 * 3600).':"%W. Woche"',
        ), $args);
        return $this->graph($title, $label, $base, 'month', $args);
    }

    public function graphYear($title, $label, $base, array $args)
    {
        $rra = Main::getInstance()->getRrdtoolRang();
        $args = array_merge(array(
            '--start '.floor($rra['year']['hour'] * -3600),
            '--x-grid MONTH:1:MONTH:1:MONTH:1:'.floor(720 * 3600).':"%b"',
        ), $args);
        return $this->graph($title, $label, $base, 'year', $args);
    }

    /**
     * Entfernt aus den Array Einträge mit Keyword :LAST:"
     *
     * @param  array $args
     * @return array
     */
    public function removeLast(array $args)
    {
        return array_filter($args, function($string) {
            return strpos($string, ':LAST:"') === false;
        });
    }

    public function gradientStart($varName, $varPostfix, $start, $color)
    {
        return array(
            'CDEF:'.$varName.$varPostfix.'start='.$varName.','.$start.',LT,'.$varName.',UNKN,IF',
            'AREA:'.$varName.$varPostfix.'start#'.ltrim($color, '#'),
        );
    }

    public function gradientEnd($varName, $varPostfix, $end, $color)
    {
        return array(
            'CDEF:'.$varName.$varPostfix.'end='.$varName.','.$end.',GT,'.$varName.','.$end.',-,UNKN,IF',
            'AREA:'.$varName.$varPostfix.'end#'.ltrim($color, '#').'::STACK',
        );
    }

    public function gradient($varName, $varPostfix, $start, $end, $step, $color1, $color2, $firstStack = false)
    {
        if ($step == 0 || $start < 0 || $end < 0) { return array(); }
        $color1 = ltrim($color1, '#');
        $color2 = ltrim($color2, '#');
        $color  = array(
            'r1' => base_convert(substr($color1, 0, 2), 16, 10),
            'g1' => base_convert(substr($color1, 2, 2), 16, 10),
            'b1' => base_convert(substr($color1, 4, 2), 16, 10),
            'r2' => base_convert(substr($color2, 0, 2), 16, 10),
            'g2' => base_convert(substr($color2, 2, 2), 16, 10),
            'b2' => base_convert(substr($color2, 4, 2), 16, 10),
        );
        $varNew = $varName.$varPostfix;
        $return = array();
        for ($i = $start; $i < $end; $i += $step) {
            $ii = $i + $step;
            if ($i == 0) {
                $return[] = 'CDEF:'.$varNew.str_replace('.', '_', $i).'='.$varName.','.$i.',GE,'.$varName.','.$ii.',GT,'.$step.','.$varName.',IF,UNKN,IF';
                // if ($varName >= $i) {
                //     if ($varName > $ii) {
                //         $step
                //     } else {
                //         $varName
                //     }
                // } else {
                //     UNKN
                // }
            } else {
                $return[] = 'CDEF:'.$varNew.str_replace('.', '_', $i).'='.$varName.','.$i.',GE,'.$varName.','.$ii.',GT,'.$step.','.$varName.','.$i.',-,IF,UNKN,IF';
                // if ($varName >= $i) {
                //     if ($varName > $ii) {
                //         $step
                //     } else {
                //         $varName - $i
                //     }
                // } else {
                //     UNKN
                // }
            }
        }
        for ($i = $start; $i < $end; $i += $step) {
            $position = ($i - $start + $step) / ($end - $start);
            $r = str_pad(base_convert(floor($color['r1'] + (($color['r2'] - $color['r1']) * $position)), 10, 16), 2, '0', STR_PAD_LEFT);
            $g = str_pad(base_convert(floor($color['g1'] + (($color['g2'] - $color['g1']) * $position)), 10, 16), 2, '0', STR_PAD_LEFT);
            $b = str_pad(base_convert(floor($color['b1'] + (($color['b2'] - $color['b1']) * $position)), 10, 16), 2, '0', STR_PAD_LEFT);
            if ($i == $start && !$firstStack) {
                $return[] = 'AREA:'.$varNew.str_replace('.', '_', $i).'#'.strtoupper($r.$g.$b);
            } else {
                $return[] = 'AREA:'.$varNew.str_replace('.', '_', $i).'#'.strtoupper($r.$g.$b).'::STACK';
            }
        }
        return $return;
    }

}
