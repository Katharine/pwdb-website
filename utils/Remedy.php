<?php
require_once 'utils.php';

class Remedy extends Item {

    protected $_level, $_hp, $_mp, $_cooldown, $_recovery_time;
    const SERIALIZED_SIZE = 5;

    protected function render_tooltip_requisites() {
        if($this->_level > 0) {
            return "<p>Requisite Lv. {$this->_level}</p>";
        } else {
            return '';
        }
    }

    protected function to_array() {
        return array_merge(array(
            $this->_level, $this->_hp, $this->_mp, $this->_cooldown, $this->_recovery_time
        ), parent::to_array());
    }

    protected function from_array($array) {
        list($this->_level, $this->_hp, $this->_mp, $this->_cooldown, $this->_recovery_time) = array_slice($array, 0, self::SERIALIZED_SIZE);
        parent::from_array(array_slice($array, self::SERIALIZED_SIZE));
    }

    public function __construct($record) {
        parent::__construct($record);
        $this->_level = (int)$record->level;
        $this->_hp = (int)$record->hp;
        $this->_mp = (int)$record->mp;
        $this->_cooldown = (int)$record->cooldown;
        $this->_recovery_time = (int)$record->recovery_time;
    }

    protected function render_effect() {
        $tip = "Effect: ";
        if($this->_recovery_time > 0) {
            $tip .= "Over the next {$this->_recovery_time} seconds, %s will be restored";
        } else {
            $tip .= "Recovers %s";
        }

        $effect = '';
        if($this->_hp > 0) {
            $effect .= number_format($this->_hp) . " HP ";
            if($this->_mp > 0) {
                $effect .= " and ";
            }
        }
        if($this->_mp > 0) {
            $effect .= number_format($this->_mp) . " MP ";
        }
        return "<p>" . sprintf($tip, $effect) . "</p>";
    }

    public function render_tooltip() {
        $tip = $this->render_tooltip_header();
        $tip .= $this->render_tooltip_requisites();
        $tip .= $this->render_effect();
        $tip .= $this->render_tooltip_footer();
        return $tip . "</div>";
    }
}
