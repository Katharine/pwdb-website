<?php
require_once 'utils.php';

class Armour extends Equipment {
    protected $_phys_def, $_fire_def, $_water_def, $_metal_def, $_earth_def, $_wood_def;
    protected $_mana, $_hp, $_evasion;

    const SERIALIZED_SIZE = 9;

    public static function FromID($id) {
        $id = (int)$id;
        return new Armour(mysql_fetch_object(MySQL::instance()->query("SELECT * FROM armor WHERE id = {$id}", true)));
    }

    protected function render_tooltip_stats() {
        $tip = '';
        if($this->_evasion != 0) {
            $tip .= sprintf("<p>Evasion %+d</p>", $this->_evasion);
        }
        if($this->_phys_def != 0) {
            $tip .= sprintf("<p>Phys. Res.: %+d</p>", $this->_phys_def);
        }
        if($this->_hp != 0) {
            $tip .= sprintf("<p>HP: %+d</p>", $this->_hp);
        }
        if($this->_mana != 0) {
            $tip .= sprintf("<p>MP %+d</p>", $this->_mana);
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
            $this->_mana, $this->_hp, $this->_evasion
        ), parent::to_array());
    }

    protected function from_array($array) {
        list($this->_phys_def, $this->_fire_def, $this->_water_def, $this->_metal_def, $this->_earth_def, $this->_wood_def,
            $this->_mana, $this->_hp, $this->_evasion) = array_slice($array, 0, self::SERIALIZED_SIZE);
        parent::from_array(array_slice($array, self::SERIALIZED_SIZE));
    }

    public function __construct($record) {
        parent::__construct($record);
        if($record == null) {
            return;
        }
        $this->_subtype = ArmourType::FromID($this->_subtype);
        $this->_phys_def = (int)$record->phys_def;
        $this->_fire_def = (int)$record->fire_def;
        $this->_water_def = (int)$record->water_def;
        $this->_metal_def = (int)$record->metal_def;
        $this->_earth_def = (int)$record->earth_def;
        $this->_wood_def = (int)$record->wood_def;
        $this->_mana = (int)$record->mana;
        $this->_hp = (int)$record->hp;
        $this->_evasion = (int)$record->evasion;

        // Our parent expects us to fill these in.
        $this->_gender_icons = true;
        $this->_craft_sockets = array($record->craft_0_socket, $record->craft_1_socket, $record->craft_2_socket, $record->craft_3_socket, $record->craft_4_socket);
        $this->_drop_sockets = array($record->drop_0_socket, $record->drop_1_socket, $record->drop_2_socket, $record->drop_3_socket, $record->drop_4_socket);
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
