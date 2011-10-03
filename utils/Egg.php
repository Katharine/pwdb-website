<?php
require_once 'utils.php';

class Egg extends Item {
    public static function FromID($id) {
        $id = (int)$id;
        $result = mysql_fetch_object(MySQL::instance()->query("SELECT * FROM eggs WHERE id = {$id}", true));
        if($result) {
            return new Egg($result);
        } else {
            return null;
        }
    }

    protected $_pet, $_hatch_price, $_unhatch_price, $_loyalty;
    const SERIALIZED_SIZE = 4;

    protected function to_array() {
        return array_merge(array(
            $this->_pet->id, $this->_hatch_price, $this->_unhatch_price, $this->_loyalty
        ), parent::to_array());
    }

    protected function from_array($array) {
        list($pet, $this->_hatch_price, $this->_unhatch_price, $this->_loyalty) = array_slice($array, 0, self::SERIALIZED_SIZE);
        parent::from_array(array_slice($array, self::SERIALIZED_SIZE));
        $this->_pet = Pet::FromID($pet);
    }
    
    public function __construct($record) {
        if(!$record) {
            return;
        }
        parent::__construct($record);
        $this->_pet = Pet::FromID($record->pet);
        $this->_hatch_price = (int)$record->hatch_price;
        $this->_unhatch_price = (int)$record->unhatch_price;
        $this->_loyalty = (int)$record->loyalty;
    }
}
