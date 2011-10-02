<?php
require_once 'utils.php';

class Ornament extends Equipment {
    protected $_phys_def, $_fire_def, $_water_def, $_metal_def, $_earth_def, $_wood_def;
    protected $_phys_attack, $_mag_attack, $_evasion;

    const SERIALIZED_SIZE = 9;

    public static function FromID($id) {
        $id = (int)$id;
        return new Ornament(mysql_fetch_object(MySQL::instance()->query("SELECT * FROM ornaments WHERE id = {$id}", true)));
    }

    protected function render_tooltip_stats() {
        $tip = '';
        if($this->_evasion != 0) {
            $tip .= sprintf("<p>Evasion %+d</p>", $this->_evasion);
        }
        if($this->_phys_def != 0) {
            $tip .= sprintf("<p>Phys. Res.: %+d</p>", $this->_phys_def);
        }
        if($this->_phys_attack != 0) {
            $tip .= sprintf("<p>Physical Attack: %+d</p>", $this->_phys_attack);
        }
        if($this->_mag_attack != 0) {
            $tip .= sprintf("<p>Magic Attack: %+d</p>", $this->_mag_attack);
        }
        if($this->_metal_def != 0) {
            $tip .= sprintf("<p>Metal Resistance: %+d</p>", $this->_metal_def);
        }
        if($this->_wood_def != 0) {
            $tip .= sprintf("<p>Wood Resistance: %+d</p>", $this->_wood_def);
        }
        if($this->_water_def != 0) {
            $tip .= sprintf("<p>Water Resistance: %+d</p>", $this->_water_def);
        }
        if($this->_fire_def != 0) {
            $tip .= sprintf("<p>Fire Resistance: %+d</p>", $this->_fire_def);
        }
        if($this->_earth_def != 0) {
            $tip .= sprintf("<p>Earth Resistance: %+d</p>", $this->_earth_def);
        }
        return $tip;
    }

    protected function to_array() {
        return array_merge(array(
            $this->_phys_def, $this->_fire_def, $this->_water_def, $this->_metal_def, $this->_earth_def, $this->_wood_def,
            $this->_phys_attack, $this->_mag_attack, $this->_evasion
        ), parent::to_array());
    }

    protected function from_array($array) {
        list($this->_phys_def, $this->_fire_def, $this->_water_def, $this->_metal_def, $this->_earth_def, $this->_wood_def,
            $this->_phys_attack, $this->_mag_attack, $this->_evasion) = $array;
        parent::from_array(array_slice($array, self::SERIALIZED_SIZE));
    }

    public function __construct($record) {
        parent::__construct($record);
        if($record == null) {
            return;
        }
        $this->_subtype = new OrnamentType($this->_subtype);
        $this->_phys_def = (int)$record->phys_def;
        $this->_fire_def = (int)$record->fire_def;
        $this->_water_def = (int)$record->water_def;
        $this->_metal_def = (int)$record->metal_def;
        $this->_earth_def = (int)$record->earth_def;
        $this->_wood_def = (int)$record->wood_def;
        $this->_phys_attack = (int)$record->phys_atk;
        $this->_mag_attack = (int)$record->mag_atk;
        $this->_evasion = (int)$record->evasion;
    }

    public function socket_stones($sockets) {
        $stones = array(
            array(null, null, null, null, null),
            array(0, 1, 2, 3, 10),
            array(0, 2, 4, 6, 20),
            array(0, 3, 6, 9, 30),
            array(0, 4, 8, 12, 40),
            array(0, 5, 10, 15, 50),
            array(0, 6, 12, 18, 60),
            array(0, 7, 14, 21, 70),
            array(0, 8, 16, 24, 80),
            array(0, 9, 18, 27, 90),
            array(0, 10, 20, 30, 100),
            array(0, 100, 200, 300, 1000),
            array(0, 200, 400, 600, 2000),
            array(0, 800, 1600, 2400, 8000),
            array(0, 1200, 2400, 3600, 12000),
            array(0, 1600, 3200, 4800, 16000),
            array(null, null, null, null, null)
        );
        return $stones[$this->_grade][$sockets];
    }
}
