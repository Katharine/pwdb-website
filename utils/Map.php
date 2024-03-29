<?php
require_once 'utils.php';

class Map {
    private $_name, $_id, $_points;

    public static function FromID($id) {
        if($id == 1) {
            return new Map('wor');
        } else if($id >= 100 && $id < 200) {
            return new Map('a' . ($id - 100));
        } else if($id >= 200 && $id < 300) {
            return new Map('b' . ($id - 200));
        } else {
            return null;
        }
    }

    public function __construct($id) {
        $this->_id = $id;
        $this->_points = array();
    }

    public function get_name() {
        $MAP_NAMES = array(
            'wor' => 'World',
            'a01' => 'City of Abominations',
            'a02' => 'Secret Passage',
            'a05' => 'Firecrag Grotto',
            'a06' => 'Den of Rabid Wolves',
            'a07' => 'Cave of the Vicious',
            'a08' => 'Hall of Deception',
            'a09' => 'The Gate of Delirium',
            'a10' => 'Secret Frostcover Grounds',
            'a11' => 'Valley of Disaster',
            'a12' => 'Jungle Ruins',
            'a13' => 'Cave of Sadistic Glee',
            'a14' => 'Wraithgate',
            'a15' => 'Hallucinatory Trench',
            'a16' => 'Eden',
            'a17' => 'Brimstone Pit',
            'a18' => 'Dragon Temple',
            'a19' => 'Nightscream Isle',
            'a20' => 'Snake Isle',
            'a21' => 'Lothranis',
            'a22' => 'Momagnon',
            'a23' => 'Seat of Torment',
            'a24' => 'Abaddon',
            'a25' => 'Warsong City',
            'a26' => 'Palace of Nirvana',
            'a27' => 'The Lunar Glade',
            'a28' => 'Valley of Reciprocity',
            'a29' => 'Frostcovered City',
            'a31' => 'Twilight Temple',
            'a32' => 'Cube of Fate',
            'a33' => "Old Heaven's Tear",
            'a34' => 'Chapel',
            'a35' => 'Faction Base',
            'a38' => 'Phoenix Valley',
            'b01' => 'Theatre of Blood',
            'b02' => 'Lost City Arena',
            'b04' => 'Archosaur Arena',
            'b30' => 'Territory War'
        );
        return $MAP_NAMES[$this->_id];
    }

    private static function pnpoly($points, $x, $y) {
        $nvert = count($points);
        $inside = false;
        for($i = 0, $j = $nvert - 1; $i < $nvert; $j = $i++) {
            if((($points[$i][1] > $y) != ($points[$j][1] > $y)) && ($x < ($points[$j][0] - $points[$i][0]) * ($y - $points[$i][1]) / ($points[$j][1]-$points[$i][1]) + $points[$i][0])) {
                    $inside = !$inside;
                }
        }
        return $inside;
    }

    public function point_to_pixels($x, $y, $size='small') {
        $map_origins = array(
            'wor' => array(1515, 16, 0.68, 0.68, 0),
            'a01' => array(-330, -420, 8, 8,4),
            'a02' => array(-318, -512.5, 15.75, 15.72, 0),
            'a05' => array(-315, -528, 18, 18.55, 0),
            'a06' => array(-222, -508.5, 8.77, 8.8, 0),
            'a07' => array(-304, -526, 15.9, 16, 0),
            'a08' => array(-353.7, -539.2, 33.2, 33.1, 0),
            'a09' => array(-178, -492, 6.71, 6.59, 0),
            'a10' => array(-269.3, -517.5, 11.175, 11.16, 0),
            'a11' => array(-145.2, -487, 6.02, 6.05, 0),
            'a12' => array(-206, -496, 7.95, 7.9, 0),
            'a13' => array(-358, -530, 15.715, 16, 2),
            'a14' => array(-280.5, -518, 12.395, 12.335, 0),
            'a15' => array(-251, -523, 10.13, 10.095, 0),
            'a16' => array(-327, -521, 8.885, 8.8, 2),
            'a17' => array(-327, -521, 8.845, 8.8, 2),
            'a18' => array(-132, -480, 5.855, 5.5, 0),
            'a19' => array(-172, -497, 6.755, 6.86, 0),
            'a20' => array(-230, -460, 10, 10, 3),
            'a21' => array(215, -399, 2.5, 2.5, 0),
            'a22' => array(206, -400, 2.53, 2.53, 0),
            'a23' => array(-177, -495, 6.83, 6.86, 0),
            'a24' => array(-160, -491, 6.49, 6.48, 0),
            'a25' => array(-221, -507, 8.5, 8.5, 0),
            'a26' => array(-242.8, -518.2, 9.6, 9.6, 0),
            'a27' => array(-320, -487, 6.4, 6.3, 1),
            'a28' => array(-155, -365, 6.3, 6.3, 3),
            'a29' => array(-230.5, -426, 9, 9.05, 3),
            'a31' => array(-166, -489.5, 6.6, 6.6, 0),
            'a32' => array(-168, -375.5, 2.21, 2.21, 1)
        );
        $w = 1024;
        $h = 768;
        if($size == 'small') {
            $w /= 2;
            $h /= 2;
        }
        list($ox, $oy, $px, $py, $k) = $map_origins[$this->_id];
        $x = abs(($x + $ox) * $px - 1024);
        if($this->_id == 'wor') {
            $x += 234;
        }
        $y = abs(($y + $oy) * $py - 768);
        if($k == 2) {
            $x1 = $x;
            $x = $y;
            $y = $x1;
        }
        if($k == 1) {
            $x = 1024 - $x;
        }
        if($k == 3) {
            $y = 768 - $y;
        }
        if($k == 4) {
            $x = 1024 - $x;
            $y = 768 - $y;
        }
        if($size == 'small') {
            $x /= 2;
            $y /= 2;
        }
        $x = (int)round($x);
        $y = (int)round($y);
        return array($x, $y);
    }

    public function get_place_name($x, $y) {
        // For now we only have precinct data for the main world ("wor")
        if($this->_id != 'wor') {
            return $this->get_name();
        }
        $x = (int)$x;
        $y = (int)$y;

        // Get all possible areas, restricting by bounding box.
        $result = MySQL::instance()->query("SELECT name, AsText(region) AS poly FROM precincts WHERE CONTAINS(region, POINT({$x},{$y})) ORDER BY id ASC", true);

        // Optimisation: in the (likely) event there's only one choice, we must be in it, so just return that.
        if(mysql_num_rows($result) === 1) {
            return mysql_result($result, 0, 0);
        }

        // Clean up the Well Known Text into something that is actually of value to us.
        $regions = array();
        while($row = mysql_fetch_object($result)) {
            $regions[$row->name] = explode(',', substr($row->poly, 9, -2));
        }
        foreach($regions as &$region) {
            foreach($region as &$points) {
                $points = explode(' ', $points);
                $points[0] = (float)$points[0];
                $points[1] = (float)$points[1];
            }
        }
        unset($region);

        // Check if we're actually in each of the regions; jump out as soon as we find one.
        foreach ($regions as $name => $region) {
            if(self::pnpoly($region, $x, $y)) {
                return $name;
            }
        }

        // We apparently don't exist. Fall back on the map name.
        return $this->get_name();
    }

    public function add_point($x, $y, $z, $kind=null, $id=null) {
        $this->_points[] = array('x' => $x, 'y' => $y, 'z' => $z, 'kind' => $kind, 'id' => $id);
    }

    public function point_count() {
        return count($this->_points);
    }

    public function points() {
        return $this->_points;
    }

    public function render($size = 'small') {
        $w = 1024;
        $h = 768;
        if($size == 'small') {
            $w = 512;
            $h = 384;
        }
        $map = "<div class='pw-map map-{$size} map-{$this->_id}' style='height: {$h}px; width: {$w}px; background: url(/images/pwi/maps/{$size}/{$this->_id}); position: relative;'>\n";
        foreach($this->_points as $point) {
            list($x, $y) = $this->point_to_pixels($point['x'], $point['y'], $size);
            $x -= 5;
            $y -= 5;
            $class = "map-point map-{$this->_id} x-" . (int)round($point['x']) . " y-" . (int)round($point['y']) . " z-" . (int)round($point['z']);
            $map .= "<a href='/{$point['kind']}/{$point['id']}' class='{$point['kind']}-link'><img src='/images/point' class='{$class}' alt='x' style='position: absolute; left: {$x}px; top: {$y}px;'></a>\n";
        }
        $map .= "</div>";
        return $map;
    }
}
