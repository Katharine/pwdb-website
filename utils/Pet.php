<?php
require_once 'utils.php';

class Pet implements Serializable {
    public static function FromID($id) {
        $id = (int)$id;
        $cache = MemoryCache::instance();
        $ret = $cache->get('pet-id-'.$id);
        if(!$ret) {
            $record = mysql_fetch_object(MySQL::instance()->query("SELECT * FROM pets WHERE id = {$id}", true));
            if($record) {
                $ret = new Pet($record);
                $cache->set('pet-id-'.$id, $ret);
            }
        }
        return $ret;
    }

    protected $_id, $_type, $_name, $_model, $_icon, $_class_mask, $_max_pet_level, $_min_player_level;
    protected $_hp_delta, $_attack_x, $_attack_a, $_attack_b, $_attack_c, $_speed_base, $_speed_delta;
    protected $_accuracy_delta, $_evasion_delta, $_pdef_delta, $_pdef_adjust, $_mdef_delta, $_mdef_adjust;
    protected $_interval, $_move_a, $_move_b;

    const HP_ADJUST = 2.86667;
    const DEF_ADJUST = 20.0;
    const AGILITY_ADJUST = 2.86667;

    const LOYALTY_HATED = 0;
    const LOYALTY_WILD = 1;
    const LOYALTY_TAME = 2;
    const LOYALTY_LOYAL = 3;

    const PET_LAND = 0;
    const PET_WATER = 1;
    const PET_FLYING = 2;
    const PET_ANYWHERE = 3;

    const PET_BATTLE = 8782;
    const PET_SUMMON = 28752;
    const PET_MOUNT = 8781;
    const PET_PRETTY = 8783;

    public function __construct($record) {
        $this->_id = (int)$record->id;
        $this->_type = (int)$record->type;
        $this->_name = $record->name;
        $this->_model = $record->model == '0' ? null : $record->model;
        $this->_icon = $record->icon == '0' ? null : $record->model;
        $this->_class_mask = (int)$record->class_mask;
        $this->_max_pet_level = (int)$record->max_pet_level;
        $this->_min_player_level = (int)$record->min_player_level;
        $this->_hp_delta = (float)$record->hp_delta;
        $this->_attack_x = (float)$record->attack_x;
        $this->_attack_a = (float)$record->attack_a;
        $this->_attack_b = (float)$record->attack_b;
        $this->_attack_c = (float)$record->attack_c;
        $this->_speed_base = (float)$record->speed_base;
        $this->_speed_delta = (float)$record->speed_delta;
        $this->_accuracy_delta = (float)$record->accuracy_delta;
        $this->_evasion_delta = (float)$record->evasion_delta;
        $this->_pdef_delta = (float)$record->pdef_delta;
        $this->_pdef_adjust = (float)$record->pdef_adjust;
        $this->_mdef_delta = (float)$record->mdef_delta;
        $this->_mdef_adjust = (float)$record->mdef_adjust;
        $this->_interval = (float)$record->interval;
        $this->_move_a = (int)$record->move_a;
        $this->_move_b = (int)$record->move_b;
    }

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

    public function get_hp($level=1) {
        $x = $this->_hp_delta;
        $y = $x * self::HP_ADJUST;
        $hp = $y + $x * ($level - 1);
        return $hp;
    }

    public function get_attack($level=1) {
        $a = $this->_attack_x * $this->_attack_a;
        $b = $this->_attack_x * $this->_attack_b;
        $c = $this->_attack_x * $this->_attack_c;
        $attack = $a*$level*$level + $b*$level + $c;
        return $attack;
    }

    public function get_speed($level=1) {
        $speed = $this->_speed_base + $this->_speed_delta * ($level - 1);
        return $speed;
    }

    public function get_accuracy($level=1) {
        $x = $this->_accuracy_delta;
        $y = $x * self::AGILITY_ADJUST;
        $accuracy = $y + $x * ($level - 1);
        return $accuracy;
    }

    public function get_evasion($level=1) {
        $x = $this->_evasion_delta;
        $y = $x * self::AGILITY_ADJUST;
        $evasion = $y + $x * ($level - 1);
        return $evasion;
    }

    public function get_mdef($level=1) {
        $x = $this->_mdef_delta;
        $y = $x * self::DEF_ADJUST + $x - 1;
        $z = $this->_mdef_adjust;
        $mdef = $y + $x * $z * ($level - 1);
        return $mdef;
    }

    public function get_pdef($level=1) {
        $x = $this->_pdef_delta;
        $y = $x * self::DEF_ADJUST + $x - 1;
        $z = $this->_pdef_adjust;
        $pdef = $y + $x * $z * ($level - 1);
        return $pdef;
    }

    public function get_aps() {
        return (1 / $this->_interval);
    }

    public function get_movement_type() {
        if($this->_move_a == 0) {
            return self::PET_LAND;
        }
        if($this->_move_a == 1 && $this->_move_b == 2) {
            return self::PET_WATER;
        }
        if($this->_move_a == 2 && $this->_move_b == 2) {
            return self::PET_FLYING;
        }
        return self::PET_ANYWHERE;
    }

    public function render_tooltip() {
        $tip = '<div class="item_tooltip">';
        $tip .= "<p class='item_name pw_color_0'>{$this->_name}</p>";
        if($this->_class_mask != Element::CLASS_ALL) {
            if($this->_class_mask & Element::CLASS_VENOMANCER) {
                $tip .= "<p>Requisite class Venomancer</p>";
            }
            if($this->_class_mask & Element::CLASS_MYSTIC) {
                $tip .= "<p>Requisite class Mystic</p>";
            }
        }
        if($this->_min_player_level > 1) {
            $tip .= "<p>Requisite Lv. {$this->_min_player_level}</p>";
        }
        switch($this->_type) {
            case self::PET_BATTLE:
                $tip .= "<p>Battle Pet</p>";
                break;
            case self::PET_SUMMON:
                $tip .= "<p>Mystic Summon</p>";
                break;
            case self::PET_MOUNT:
                $tip .= "<p>Ground Mount</p>";
                break;
            case self::PET_PRETTY:
                $tip .= "<p>Non-combat Pet</p>";
                break;
        }
        switch($this->get_movement_type()) {
            case self::PET_LAND:
                $tip .= "<p>Available on land</p>";
                break;
            case self::PET_WATER:
                $tip .= "<p>Available in water</p>";
                break;
            case self::PET_FLYING:
                $tip .= "<p>Available in the air</p>";
                break;
            case self::PET_ANYWHERE:
                $tip .= "<p>Available anywhere</p>";
                break;
        }
        $tip .= "</div>";
        return $tip;
    }

    public function initial_skills() {
        $link = MySQL::instance();
        $result = $link->query("SELECT skill_descriptions.name, egg_skills.level FROM skill_descriptions, eggs, egg_skills WHERE skill_descriptions.id = egg_skills.skill AND egg_skills.egg = eggs.id AND eggs.pet = {$this->_id}", true);
        $skills = array();
        while($row = mysql_fetch_object($result)) {
            if($row->name) {
                $skills[] = array('level' => (int)$row->level, 'name' => $row->name);
            }
        }
        return $skills;
    }

    public function egg() {
        $link = MySQL::instance();
        $record = mysql_fetch_object($link->query("SELECT * FROM eggs WHERE pet = {$this->_id} LIMIT 1", true));
        if($record) {
            return new Egg($record);
        }
        return null;
    }

    public function icon_url() {
        return Element::IconURL($this->_icon);
    }

    public function link() {
        return "<a class='pw_color_0 pet-link' href='/pet/{$this->_id}'>【".$this->_name."】</a>";
    }

    public function serialize() {
        return igbinary_serialize(array($this->_id, $this->_type, $this->_name, $this->_model, $this->_icon, $this->_class_mask, $this->_max_pet_level, $this->_min_player_level,
            $this->_hp_delta, $this->_attack_x, $this->_attack_a, $this->_attack_b, $this->_attack_c, $this->_speed_base, $this->_speed_delta,
            $this->_accuracy_delta, $this->_evasion_delta, $this->_pdef_delta, $this->_pdef_adjust, $this->_mdef_delta, $this->_mdef_adjust,
            $this->_interval, $this->_move_a, $this->_move_b));
    }

    public function unserialize($data) {
        list ($this->_id, $this->_type, $this->_name, $this->_model, $this->_icon, $this->_class_mask, $this->_max_pet_level, $this->_min_player_level,
            $this->_hp_delta, $this->_attack_x, $this->_attack_a, $this->_attack_b, $this->_attack_c, $this->_speed_base, $this->_speed_delta,
            $this->_accuracy_delta, $this->_evasion_delta, $this->_pdef_delta, $this->_pdef_adjust, $this->_mdef_delta, $this->_mdef_adjust,
            $this->_interval, $this->_move_a, $this->_move_b) = igbinary_unserialize($data);
    }
}
