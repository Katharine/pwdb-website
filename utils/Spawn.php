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

    protected function render_tooltip_location() {
        $link = MySQL::instance();
        $result = $link->query("SELECT map FROM spawn_points WHERE spawn = {$this->_id} GROUP BY map", true);
        if(mysql_num_rows($result)) {
            $places = array();
            while($row = mysql_fetch_object($result)) {
                $map = new Map($row->map);
                $places[] = $map->get_name();
            }
            return '<p class="spawn-location">' . implode('; ', $places) . '</p>';
        } else {
            return "<p class='spawn-location'>Doesn't exist</p>";
        }
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
}
