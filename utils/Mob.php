<?php
require_once 'utils.php';

class Mob extends Spawn implements Serializable {
    public static function DropsItem($id) {
        $id = (int)$id;
        $link = MySQL::instance();
        $result = $link->query("SELECT mobs.*, SUM(mob_drops.rate) as rate FROM mob_drops, mobs WHERE mob_drops.mob = mobs.id AND mobs.drop_0_items < 1 AND mob_drops.item = {$id} GROUP BY mobs.id ORDER BY mob_drops.rate DESC", true);
        $mobs = array();
        while($row = mysql_fetch_object($result)) {
            $mobs[] = array('rate' => $row->rate, 'mob' => new Mob($row));
        }
        return $mobs;
    }

    public static function FromID($id) {
        $id = (int)$id;
        $mob = MemoryCache::instance()->get('mob-id-'.$id);
        if(!$mob) {
            $record = mysql_fetch_object(MySQL::instance()->query("SELECT * FROM mobs WHERE id = {$id}", true));
            if($record) {
                $mob = new Mob($record);
                MemoryCache::instance()->set('mob-id-'.$id, $mob);
            }
        }
        if(!$mob) {
            $mob = null;
        }
        return $mob;
    }

    public function serialize() {
        $egg = null;
        if($this->_egg) {
            $egg = $this->_egg->id;
        }
        return igbinary_serialize(array(
            $this->_id, $this->_type, $this->_name, $this->_strong_element, $this->_weak_element, $this->_model, $this->_gfx, $this->_level, $egg, $this->_hp, $this->_phys_def, $this->_metal_def,
            $this->_wood_def, $this->_water_def, $this->_fire_def, $this->_earth_def, $this->_exp, $this->_spirit, $this->_coins_mean, $this->_coins_variance, $this->_accuracy,
            $this->_evasion, $this->_min_patk, $this->_max_patk, $this->_range, $this->_interval, $this->_min_matk, $this->_max_matk, $this->_aggressive, $this->_aggro_range,
            $this->_aggro_time, $this->_walk_speed, $this->_run_speed, $this->_swim_speed, $this->_drop_distribution, $this->_drop_multiplier, $this->_spawns,
        ));
    }

    public function unserialize($data) {
        list($this->_id, $this->_type, $this->_name, $this->_strong_element, $this->_weak_element, $this->_model, $this->_gfx, $this->_level, $egg, $this->_hp, $this->_phys_def, $this->_metal_def,
            $this->_wood_def, $this->_water_def, $this->_fire_def, $this->_earth_def, $this->_exp, $this->_spirit, $this->_coins_mean, $this->_coins_variance, $this->_accuracy,
            $this->_evasion, $this->_min_patk, $this->_max_patk, $this->_range, $this->_interval, $this->_min_matk, $this->_max_matk, $this->_aggressive, $this->_aggro_range,
            $this->_aggro_time, $this->_walk_speed, $this->_run_speed, $this->_swim_speed, $this->_drop_distribution, $this->_drop_multiplier, $this->_spawns) = igbinary_unserialize($data); 
        $this->_kind = 'mob';
        if($egg) {
            $this->_egg = Egg::FromID($egg);
        } else {
            $this->_egg = null;
        }
    }

    protected $_id, $_type, $_name, $_strong_element, $_weak_element, $_model, $_gfx, $_level, $_egg, $_hp, $_phys_def, $_metal_def;
    protected $_wood_def, $_water_def, $_fire_def, $_earth_def, $_exp, $_spirit, $_coins_mean, $_coins_variance, $_accuracy;
    protected $_evasion, $_min_patk, $_max_patk, $_range, $_interval, $_min_matk, $_max_matk, $_aggressive, $_aggro_range;
    protected $_aggro_time, $_walk_speed, $_run_speed, $_swim_speed, $_drop_distribution, $_drop_multiplier, $_spawns;

    public function __construct($record) {
        $this->_kind = 'mob';
        $this->_id = (int)$record->id;
        $this->_type = (int)$record->type;
        $this->_name = $record->name;
        $this->_strong_element = $record->strong_element;
        $this->_weak_element = $record->weak_element;
        $this->_model = $record->model;
        $this->_gfx = $record->gfx;
        $this->_level = (int)$record->level;
        $this->_egg = Egg::FromID((int)$record->egg);
        $this->_hp = (int)$record->hp;
        $this->_phys_def = (int)$record->phys_def;
        $this->_metal_def = (int)$record->metal_def;
        $this->_wood_def = (int)$record->wood_def;
        $this->_water_def = (int)$record->wood_def;
        $this->_fire_def = (int)$record->fire_def;
        $this->_earth_def = (int)$record->earth_def;
        $this->_exp = (int)$record->exp;
        $this->_spirit = (int)$record->spirit;
        $this->_coins_mean = (int)$record->coins_mean;
        $this->_coins_variance = (int)$record->coins_variance;
        $this->_accuracy = (int)$record->accuracy;
        $this->_evasion = (int)$record->evasion;
        $this->_min_patk = (int)$record->min_patk;
        $this->_max_patk = (int)$record->max_patk;
        $this->_range = (float)$record->range;
        $this->_interval = (float)$record->interval;
        $this->_min_matk = (int)$record->min_matk;
        $this->_max_matk = (int)$record->max_matk;
        $this->_aggressive = (bool)((int)$record->aggressive);
        $this->_aggro_range = (float)$record->aggro_range;
        $this->_aggro_time = (float)$record->aggro_time;
        $this->_walk_speed = (float)$record->walk_speed;
        $this->_run_speed = (float)$record->run_speed;
        $this->_swim_speed = (float)$record->swim_speed;
        $this->_drop_distribution = array(
            (float)$record->drop_0_items,
            (float)$record->drop_1_items,
            (float)$record->drop_2_items,
            (float)$record->drop_3_items
        );
        $this->_drop_multiplier = (int)$record->drop_multiplier;
    }

    public function real_drop_rate($rate) {
        $zero = 0;
        $one = 1 - pow((1 - $rate), 1 * $this->_drop_multiplier);
        $two = 1 - pow((1 - $rate), 2 * $this->_drop_multiplier);
        $three = 1 - pow((1 - $rate), 3 * $this->_drop_multiplier);
        $dist = $this->_drop_distribution;
        $real = $one * $dist[1] + $two * $dist[2] + $three * $dist[3];
        return $real;
    }

    public function get_level() {
        if($this->_level == 150) {
            return '?';
        } else {
            return (string)$this->_level;
        }
    }

    protected function render_tooltip_header() {
        $tip = "<div class='item_tooltip'><p><span class='mob-title pw_color_0'>{$this->_name}</span>";
        if($this->_egg) {
            $tip .= " <img src='/images/pet.png' alt='tamable'>";
        }
        if($this->_strong_element) {
            $tip .= " [<span class='pw-element-{$this->_strong_element}'>{$this->_strong_element}</span>]";
        }
        $tip .= "</p>";
        $translated = Translate::TranslateField($this->_name);
        if($translated) {
            $tip .= "<p class='translation'>{$translated}</p>";
        }
        return $tip;
    }

    public function render_tooltip($map=null, $x=null, $y=null) {
        $tip = $this->render_tooltip_header();
        $tip .= $this->render_tooltip_location($map, $x, $y);
        $tip .= "<p class='mob-level-{$this->_level}'>Level <span class='mob-level-figure'>" . $this->get_level() . "</span></p>";
        $tip .= "<p>HP: " . number_format($this->_hp) . "</p>";
        $tip .= "</div>";
        return $tip;
    }

    public function drops() {
        if($this->_drop_distribution[0] == 1) {
            return array();
        }
        $link = MySQL::instance();
        $result = $link->query("SELECT item, SUM(rate) AS rate FROM mob_drops WHERE mob = {$this->_id} GROUP BY item ORDER BY rate DESC", true);
        $items = array();
        while($row = mysql_fetch_object($result)) {
            $item = Item::FromID($row->item);
            if(!$item) {
                continue;
            }
            $items[] = array('rate' => (float)$row->rate, 'item' => $item);
        }
        return $items;
    }
}
