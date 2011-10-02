<?php
require_once 'utils.php';

class Equipment extends Item {
    protected $_craft_sockets, $_drop_sockets, $_addon_probability, $_durability, $_flags;
    protected $_max_durability, $_decompose_price, $_decompose_time, $_decompose_to, $_decompose_amount;
    protected $_str, $_dex, $_vit, $_mag, $_level, $_repair_price, $_refine_bonus, $_nonrandom_addons;
    protected $_reputation;
    // Not serialised:
    protected $_children, $_decomposes_from, $_parents;

    const SERIALIZED_SIZE = 19;

    public function __construct($record = null) {
        parent::__construct($record);
        $this->_addon_probability = array((float)$record->addons_0, (float)$record->addons_1, (float)$record->addons_2, (float)$record->addons_3);
        $this->_durability = (int)$record->durability;
        $this->_max_durability = (int)$record->max_durability;
        $this->_decompose_price = (int)$record->decompose_price;
        $this->_decompose_to = Item::FromID($record->decompose_to);
        $this->_decompose_time = (int)$record->decompose_time;
        $this->_decompose_amount = (int)$record->decompose_amount;
        $this->_str = (int)$record->str;
        $this->_dex = (int)$record->dex;
        $this->_vit = (int)$record->vit;
        $this->_mag = (int)$record->mag;
        $this->_level = (int)$record->level;
        $this->_repair_price = (int)$record->repair_price;
        $this->_refine_bonus = ItemAddon::FromID($record->level_up_addon);
        $this->_drop_sockets = array();
        $this->_craft_sockets = array();
        $this->_nonrandom_addons = (bool)((int)$record->nonrandom_addons);
        $this->_flags = (int)$record->flags;
        $this->_reputation = (int)$record->reputation;
    }

    protected function to_array() {
        return array_merge(array(
            $this->_craft_sockets, $this->_drop_sockets, $this->_addon_probability, $this->_durability, $this->_flags,
            $this->_max_durability, $this->_decompose_price, $this->_decompose_time, $this->_decompose_to, $this->_decompose_amount,
            $this->_str, $this->_dex, $this->_vit, $this->_mag, $this->_level, $this->_repair_price, $this->_refine_bonus, $this->_nonrandom_addons,
            $this->_reputation
        ), parent::to_array());
    }

    protected function from_array($array) {
        list($this->_craft_sockets, $this->_drop_sockets, $this->_addon_probability, $this->_durability, $this->_flags,
            $this->_max_durability, $this->_decompose_price, $this->_decompose_time, $this->_decompose_to, $this->_decompose_amount,
            $this->_str, $this->_dex, $this->_vit, $this->_mag, $this->_level, $this->_repair_price, $this->_refine_bonus, $this->_nonrandom_addons,
            $this->_reputation) = array_slice($array, 0, self::SERIALIZED_SIZE);
        parent::from_array(array_slice($array, self::SERIALIZED_SIZE));
    }

    protected function render_tooltip_requisites() {
        $tip = '';
        if($this->_max_durability != 0) {
            $tip .= "<p>Durability {$this->_durability}/{$this->_max_durability}</p>";
        }
        if($this->_class_mask != Element::CLASS_NONE && $this->_class_mask != Element::CLASS_ALL) {
            $classes = array();
            if($this->_class_mask & Element::CLASS_BLADEMASTER) $classes[] = 'Blademaster';
            if($this->_class_mask & Element::CLASS_WIZARD) $classes[] = 'Wizard';
            if($this->_class_mask & Element::CLASS_PSYCHIC) $classes[] = 'Psychic';
            if($this->_class_mask & Element::CLASS_VENOMANCER) $classes[] = 'Venomancer';
            if($this->_class_mask & Element::CLASS_BARBARIAN) $classes[] = 'Barbarian';
            if($this->_class_mask & Element::CLASS_ASSASSIN) $classes[] = 'Assassin';
            if($this->_class_mask & Element::CLASS_ARCHER) $classes[] = 'Archer';
            if($this->_class_mask & Element::CLASS_CLERIC) $classes[] = 'Cleric';
            if($this->_class_mask & Element::CLASS_SEEKER) $classes[] = 'Seeker';
            if($this->_class_mask & Element::CLASS_MYSTIC) $classes[] = 'Mystic';
            $tip .= '<p>Requisite Class ' . implode(' ', $classes) . '</p>';
        }
        if($this->_level > 0) {
            $tip .= "<p class='level-requirement'>Requisite Lv. <span class='level-requirement-number'>{$this->_level}</span></p>";
        }
        if($this->_reputation > 0) {
            $tip .= "<p>Requisite Reputation: {$this->_reputation}</p>";
        }
        if($this->_str > 0) {
            $tip .= "<p>Requisite Strength {$this->_str}";
        }
        if($this->_dex > 0) {
            $tip .= "<p>Requisite Dexterity {$this->_dex}";
        }
        if($this->_vit > 0) {
            $tip .= "<p>Requisite Vitality {$this->_vit}";
        }
        if($this->_mag > 0) {
            $tip .= "<p>Requisite Magic {$this->_mag}";
        }
        return $tip;
    }

    protected function render_tooltip_header() {
        //$tip = parent::render_tooltip_header();
        $sockets_d = 0;
        $sockets_c = 0;
        for($i = 0; $i < count($this->_drop_sockets); ++$i) {
            $sockets_d += $i * $this->_drop_sockets[$i];
        }
        for($i = 0; $i < count($this->_craft_sockets); ++$i) {
            $sockets_c += $i * $this->_craft_sockets[$i];
        }
        $sockets = ($sockets_d + $sockets_c) / 2.0;
        $socket_str = '';
        if($sockets > 0) {
            $socket_str = sprintf(' (%.1f socket(s))', $sockets);
        }
        $tip = "<div class='item_tooltip'><p class='item_title pw_color_{$this->_colour}'>{$this->_name}{$socket_str}</p>";
        $translated = Translate::TranslateField($this->_name);
        if($translated) {
            $tip .= "<p class='translation'>{$translated}</p>";
        }
        if($this->_flags & Element::ITEM_BIND_ON_EQUIP) {
            $tip .= "<p class='pw_color_4'>Equipping this item will cause it to be bound.</p>";
        }
        $tip .= "<p>{$this->_subtype}</p>";
        $tip .= "<p>Lv. {$this->_grade}</p>";
        return $tip;
    }

    protected function render_tooltip_addons() {
        $addons = ItemAddon::AddonsForItem($this->_id, 'drop');
        $tip = '';
        foreach($addons as $addon) {
            $tip .= "<p class='tooltip_addon'>" . htmlentities($addon->label()) . "</p>";
        }
        return $tip;
    }

    /* virtual */ protected function render_tooltip_stats() { return ''; }

    public function get_addons($type=null) {
        $addons = ItemAddon::AddonsForItem($this->_id, $type);
        $groups = array();
        foreach($addons as $addon) {
            if(!isset($groups[$addon->group])) {
                $groups[$addon->group] = array(
                    'title' => $addon->render_title(),
                    'addons' => array(),
                    'probability' => 0.0,
                    'min' => 2147483647,
                    'max' => -2147483647
                );
            }
            foreach($addon->values as $val) {
                if($val < $groups[$addon->group]['min']) {
                    $groups[$addon->group]['min'] = $val;
                }
                if($val > $groups[$addon->group]['max']) {
                    $groups[$addon->group]['max'] = $val;
                }
            }
            $groups[$addon->group]['addons'][] = $addon;
            $groups[$addon->group]['probability'] += $addon->probability;
        }
        return $groups;
    }

    protected function render_set() {
        $link = MySQL::instance();
        $id = $this->_id;
        $row = mysql_fetch_object($link->query("SELECT name, item_count, item_1, item_2, item_3, item_4, item_5, item_6, bonus_2, bonus_3, bonus_4, bonus_5, bonus_6 FROM item_sets WHERE item_1 = {$id} OR item_2 = {$id} OR item_3 = {$id} OR item_4 = {$id} OR item_5 = {$id} OR item_6 = {$id}", true));
        if(!$row) {
            return;
        }
        $tip = '<br>';
        $effects = array(2 => $row->bonus_2, 3 => $row->bonus_3, 4 => $row->bonus_4, 5 => $row->bonus_5, 6 => $row->bonus_6);
        foreach($effects as $pieces => $effect) {
            if($effect) {
                $addon = ItemAddon::FromID($effect);
                if(!$addon) {
                    $addon = $effect;
                } else {
                    $addon = $addon->label();
                }
                $tip .= "<p class='tooltip_addon'>({$pieces}) {$addon}</p>";
            }
        }
        $items = array($row->item_1, $row->item_2, $row->item_3, $row->item_4, $row->item_5, $row->item_6);
        $tip .= "<p class='tooltip-set-name'>{$row->name} ({$row->item_count})</p><ul class='tooltip-set'>";
        foreach($items as $item) {
            if(!$item)
                break;
            if($item == $id) {
                $item = $this;
            } else {
                $item = Item::FromID($item);
            }
            if($item) {
                $tip .= "<li>" . $item->link() . "</li>";
            }
        }
        $tip .= "</li>";
        return $tip;
    }

    public function render_tooltip() {
        $tip = $this->render_tooltip_header();
        $tip .= $this->render_tooltip_stats();
        $tip .= $this->render_tooltip_requisites();
        if($this->_nonrandom_addons) {
            $tip .= $this->render_tooltip_addons();
        }
        $tip .= $this->render_tooltip_footer();
        $tip .= $this->render_set();
        return $tip . "</div>";
    }

    public function refine_change($refine) {
        $base = $this->refine_bonus->values[0];
        $multipliers = array(0, 1, 2, 3.05, 4.28, 5.71, 7.5185, 9.92, 13, 17.05, 22.27, 29, 37.5);
        $bonus = $base * $multipliers[$refine];
        $ret = array('delta' => $bonus, 'totals' => array());
        switch($this->refine_bonus->group) {
        case 208:
            $ret['totals']['HP'] = $this->hp + $bonus;
            break;
        case 211:
            $ret['totals']['MAtk'] = array($this->min_matk + $bonus, $this->max_matk + $bonus);
            // Intentionally no break.
        case 200:
            $ret['totals']['PAtk'] = array($this->min_patk + $bonus, $this->max_patk + $bonus);
            break;
        case 209:
            $ret['totals']['Evasion'] = $this->_evasion + $bonus;
            break;
        case 210:
            $ret['totals']['MT'] = $this->metal_def + $bonus;
            $ret['totals']['WD'] = $this->wood_def + $bonus;
            $ret['totals']['FR'] = $this->fire_def + $bonus;
            $ret['totals']['WT'] = $this->water_def + $bonus;
            $ret['totals']['ER'] = $this->earth_def + $bonus;
            break;
        case 202:
            $ret['totals']['PDef'] = $this->phys_def + $bonus;
            break;
        default:
            $ret['totals'][$this->refine_bonus->group] = $bonus;
        }
        return $ret;
    }

    public function find_children() {
        // First see if we've cached this:
        $cache = MemoryCache::instance();
        $cached = igbinary_unserialize($cache->get('ic-'.$this->_id));
        if($cached) {
            $children = array();
            foreach($cached as $child) {
                $children[] = Item::FromID($child);
            }
            $this->_children = $children;
        } else {
            $link = MySQL::instance();
            // Is there any item that directly upgrades us?
            $result = $link->query("SELECT item FROM recipes, recipe_output WHERE upgrade_for = {$this->id} AND recipes.id = recipe_output.recipe", true);
            if(mysql_num_rows($result) > 0) {
                // There is! Possibly several.
                $children = array();
                while($row = mysql_fetch_object($result)) {
                    if($row->item != $this->id) { // Otherwise reforges cause infinite recursion
                        $children[] = Item::FromID($row->item);
                    }
                }
                $this->_children = $children;
            } else {
                // Do we decompose into a material that's then used to make something?
                $decompose_query = '';
                if($this->decompose_to && $this->decompose_to->type == 'reward') {
                    $decompose = $this->decompose_to->id;
                    $decompose_query = " OR recipe_input.item = {$decompose}";
                }
                $result = $link->query("SELECT recipe_output.item AS item FROM recipes, recipe_input, recipe_output, items WHERE (recipe_input.item = {$this->_id} {$decompose_query}) AND recipes.id = recipe_input.recipe AND recipe_output.recipe = recipes.id AND items.id = recipe_output.item AND items.type IN ('armour', 'weapon', 'ornament') GROUP BY recipe_output.item", true);
                if(mysql_num_rows($result)) {
                    $children = array();
                    $child_ids = array();
                    while($row = mysql_fetch_object($result)) {
                        if($row->item != $this->id && !in_array($row->item, $child_ids)) { // Again with the reforges.
                            $children[] = Item::FromID($row->item);
                            $child_ids[] = $row->item;
                        }
                    }
                    $this->_children = $children;
                }
            }
            // Cache this.
            if($this->_children) {
                $to_cache = array();
                foreach($this->_children as $child) {
                    $to_cache[] = $child->id;
                }
                $cache->set('ic-'.$this->_id, igbinary_serialize($to_cache));
            }
        }
        if($this->_children) {
            // Find their children!
            foreach($this->_children as &$child) {
                $child->find_children();
            }
        }
        return $this->_children;
    }

    public function find_parents() {
        // Check if we've cached this.
        $cache = MemoryCache::instance();
        $cached = igbinary_unserialize($cache->get('ip-'.$this->_id));
        if($cached) {
            $parents = array();
            foreach($cached as $parent) {
                $parents[] = Item::FromID($parent);
            }
            $this->_parents = $parents;
        } else {
            // Check for recipes that create us from other items.
            // All decomposed materials are of type "reward", so we want those plus equipment (weapon/armour/ornament)
            // Quantity 1 helps filter out things like rapture crystals; this is filtered more later.
            // Input ≠ output ensures that we don't get nirvana reforging.
            $link = MySQL::instance();
            $parent_ids = array();
            $results = $link->query("SELECT recipe_input.item, items.type FROM recipe_input, recipe_output, items WHERE recipe_input.recipe = recipe_output.recipe AND recipe_input.item = items.id AND recipe_output.item = {$this->_id} AND items.type IN ('weapon', 'armour', 'ornament', 'reward') AND recipe_input.quantity = 1 AND recipe_input.item != recipe_output.item", true);
            while($row = mysql_fetch_object($results)) {
                if($row->type == 'reward') { // This is the non-trivial case.
                    // Potentially decomposed - or potentially nothing at all.
                    $from = Item::DecomposedFrom($row->item);
                    if(count($from) > 0 /*!= 1*/) { // Is this always one? I think so, but I'm not certain.
                        if($from[0] instanceof Equipment && !in_array($from[0]->id, $parent_ids)) { // Prevent duplication to avoid confusing cube necklaces.
                            // This is what we're here for!
                            $this->_parents[] = $from[0];
                            $parent_ids[] = $from[0]->id;
                        } else {
                            continue;
                        }
                    } else {
                        continue;
                    }
                } else if(!in_array($row->item, $parent_ids)) { // This is (should be) the trivial case.
                    $this->_parents[] = Item::FromID($row->item);
                    $parent_ids[] = $row->item;
                }
            }

            // Cache our results.
            if($this->_parents) {
                $parents = array();
                foreach($this->_parents as $parent) {
                    $parents[] = $parent->id;
                }
                $cache->set('ip-'.$this->_id, igbinary_serialize($parents));
            }
        }
        if($this->_parents) {
            foreach($this->_parents as &$parent) {
                $parent->find_parents();
            }
        }
        return $this->_parents;
    }
}
