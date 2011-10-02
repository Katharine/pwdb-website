<?php
require_once 'utils.php';

class Soulgem extends Item {
    public static function FromID($id) {
        $id = (int)$id;
        return new Soulgem(mysql_fetch_object(MySQL::instance()->query("SELECT * FROM shards WHERE id = {$id}", true)));
    }

    protected $_imbue_price, $_purge_price, $_weapon_addon, $_armour_addon, $_weapon_string, $_armour_string;

    const SERIALIZED_SIZE = 6;

    protected function to_array() {
        return array_merge(array(
            $this->_imbue_price, $this->_purge_price, $this->_weapon_addon, $this->_armour_addon, $this->_weapon_string, $this->_armour_string
            ), parent::to_array());
    }

    protected function from_array($array) {
        list($this->_imbue_price, $this->_purge_price, $this->_weapon_addon, $this->_armour_addon, $this->_weapon_string, $this->_armour_string) = array_slice($array, 0, self::SERIALIZED_SIZE);
        parent::from_array(array_slice($array, self::SERIALIZED_SIZE));
    }
    
    public function __construct($record) {
        parent::__construct($record);
        $this->_imbue_price = (int)$record->imbue_price;
        $this->_purge_price = (int)$record->purge_price;
        $this->_weapon_addon = (int)$record->weapon_addon;
        $this->_armour_addon = (int)$record->armour_addon;
        $this->_weapon_string = $record->weapon_string;
        $this->_armour_string = $record->armour_string;
    }
}
