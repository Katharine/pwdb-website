<?php
require_once 'utils.php';

class Weapon extends Equipment {
    protected $_min_patk, $_max_patk, $_min_matk, $_max_matk, $_range, $_interval;
    protected $_unique_addon_probability, $_left_model, $_right_model, $_dropped_model;

    const SERIALIZED_SIZE = 10;

    public function __construct($record=null) {
        parent::__construct($record);
        if($record == null) {
            return;
        }
        // Our parent's definition of this is unhelpful.
        $this->_subtype = WeaponType::FromID($this->_subtype);

        $this->_min_patk = (int)$record->min_patk;
        $this->_max_patk = (int)$record->max_patk;
        $this->_max_matk = (int)$record->max_matk;
        $this->_min_matk = (int)$record->min_matk;
        $this->_left_model = $record->model_left;
        $this->_right_model = $record->model_right;
        $this->_dropped_model = $record->model_dropped;
        $this->_range = (float)$record->range;
        $this->_interval = (float)$this->_subtype->interval;
        $this->_unique_addon_probability = (float)$record->unique_addon;
        $this->_unique_addons = $record->unique_addons;
    }

    protected function to_array() {
        return array_merge(array(
            $this->_min_patk, $this->_max_patk, $this->_min_matk, $this->_max_matk, $this->_range, $this->_interval,
            $this->_unique_addon_probability, $this->_left_model, $this->_right_model, $this->_dropped_model
        ), parent::to_array());
    }

    protected function from_array($array) {
        list($this->_min_patk, $this->_max_patk, $this->_min_matk, $this->_max_matk, $this->_range, $this->_interval,
            $this->_unique_addon_probability, $this->_left_model, $this->_right_model, $this->_dropped_model) = array_slice($array, 0, self::SERIALIZED_SIZE);
        parent::from_array(array_slice($array, self::SERIALIZED_SIZE));
    }

    protected function render_tooltip_stats() {
        $rate = number_format(1/$this->_interval, 2);
        $range = number_format($this->range, 2);
        $tip = "<p>Attack Rate (Atks/sec) {$rate}</p>\n<p>Range {$range}</p>";
        if($this->_max_patk > 0) {
            $tip .= "<p>Physical Attack {$this->_min_patk}–{$this->_max_patk}</p>";
        }
        if($this->_max_matk > 0) {
            $tip .= "<p>Magic Attack {$this->_min_matk}–{$this->_max_matk}</p>";
        }
        return $tip;
    }

    public function socket_stones($sockets) {
        $stones = array(
            array(null, null, null),
            array(0, 5, 10),
            array(0, 10, 20),
            array(0, 15, 30),
            array(0, 20, 40),
            array(0, 25, 50),
            array(0, 30, 60),
            array(0, 35, 70),
            array(0, 40, 80),
            array(0, 45, 90),
            array(0, 50, 100),
            array(0, 500, 1000),
            array(0, 1000, 2000),
            array(0, 4000, 8000),
            array(0, 6000, 12000),
            array(0, 8000, 16000),
            array(0, null, null)
        );
        return $stones[$this->_grade][$sockets];
    }
}
