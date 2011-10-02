<?php
require_once 'utils.php';

class Remedy extends Item {
    public static function FromID($id) {
        $id = (int)$id;
        return new Remedy(mysql_fetch_object(MySQL::instance()->query("SELECT * FROM remedies WHERE id = {$id}", true)));
    }

    protected function render_tooltip_requisites() {
        if($this->_level > 0) {
            return "<p>Requisite Lv. {$this->_level}</p>";
        } else {
            return '';
        }
    }

    protected $_level, $_hp, $_mp, $_cooldown, $_recovery_time;

    public function __construct($record) {
        parent::__construct($record);
        $this->_level = $record->level;
        $this->_hp = $record->hp;
        $this->_mp = $record->mp;
        $this->_cooldown = $record->cooldown;
        $this->_recovery_time = $record->recovery_time;
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
