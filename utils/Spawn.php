<?php
require_once 'utils.php';

class Spawn {
    protected $_kind;

    public function __get($var) {
        $real_name = "_{$var}";
        if(property_exists($this, $real_name)) {
            return $this->$real_name;
        }
    }

    public function __isset($var) {
        $real_name = "_{$var}";
        return property_exists($this, $real_name);
    }

    public function link() {
        return "<a class='pw_color_0 {$this->_kind}-link' href='/{$this->_kind}/{$this->_id}'>【".$this->_name."】</a>";
    }

    protected function render_tooltip_location($m=null, $x=null, $y=null) {
        $location = $this->location_summary($m, $x, $y);
        if(count($location) > 0) {
            return '<p class="spawn-location">' . implode('; ', $location) . '</p>';
        } else {
            return "<p class='spawn-location'>Doesn't exist</p>";
        }
    }

    public function location_summary($m=null, $x=null, $y=null) {
        $link = MySQL::instance();
        if($m !== null) {
            $map = new Map($m);
            if($m != 'wor' || $x === null || $y === null) {
                return array($map->get_name());
            } else {
                return array($map->get_place_name($x, $y));
            }
        }
        $result = $link->query(
           "SELECT map, AVG(x) AS x, AVG(y) AS y, COUNT(spawn) AS spawn_count, (SQRT(POW(MAX(X) - MIN(X), 2) + POW(MAX(Y) - MIN(Y), 2))) AS spread 
            FROM spawn_points 
            WHERE spawn = {$this->_id} 
            GROUP BY map", true);
        $places = array();
        if(mysql_num_rows($result)) {
            while($row = mysql_fetch_object($result)) {
                $map = new Map($row->map);
                if((float)$row->spread < 100) {
                    $places[] = $map->get_place_name($row->x, $row->y);
                } else {
                    $places[] = $map->get_name();
                }
            }
        }
        return $places;
    }

    public function spawn_points() {
        if($this->_spawns) {
            return $this->_spawns;
        }
        $link = MySQL::instance();
        $results = $link->query("SELECT map, x, y, z FROM spawn_points WHERE spawn = {$this->_id}", true);
        $points = array();
        while($point = mysql_fetch_object($results)) {
            if(!isset($points[$point->map])) {
                $points[$point->map] = new Map($point->map);
            }
            $points[$point->map]->add_point($point->x, $point->y, $point->z, $this->_kind, $this->_id);
        }
        $this->_spawns = $points;
        return $points;
    }

    public function exists() {
        return (bool)mysql_num_rows(MySQL::instance()->query("SELECT spawn FROM spawn_points WHERE spawn = {$this->_id} LIMIT 1",true));
    }
}
