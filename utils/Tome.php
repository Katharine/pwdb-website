<?php
require_once 'utils.php';

class Tome extends Item {
    public static function FromID($id) {
        $id = (int)$id;
        return new Tome(mysql_fetch_object(MySQL::instance()->query("SELECT * FROM tomes WHERE id = {$id}", true)));
    }

    protected $_decompose_price, $_decompose_to, $_decompose_time, $_decompose_amount;

    const SERIALIZED_SIZE = 4;

    protected function to_array() {
        $decompose_to = null;
        if($this->_decompose_to) {
            $decompose_to = $this->_decompose_to->id;
        }
        return array_merge(array(
            $this->_decompose_price, $decompose_to, $this->_decompose_time, $this->_decompose_amount
            ), parent::to_array());
    }

    protected function from_array($array) {
        list($this->_decompose_price, $decompose_to, $this->_decompose_time, $this->_decompose_amount) = array_slice($array, 0, self::SERIALIZED_SIZE);
        parent::from_array(array_slice($array, self::SERIALIZED_SIZE));
        if($decompose_to)
            $this->_decompose_to = Item::FromID($decompose_to);
    }
    
    public function __construct($record) {
        parent::__construct($record);
        $this->_decompose_price = (int)$record->decompose_price;
        $this->_decompose_to = Item::FromID($record->decompose_to);
        $this->_decompose_time = (int)$record->decompose_time;
        $this->_decompose_amount = (int)$record->decompose_amount;
    }
}
