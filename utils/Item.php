<?php
require_once 'utils.php';
class Item implements Serializable {
    protected $_id, $_colour, $_name, $_description, $_class_mask, $_icon, $_stack_count;
    protected $_type, $_subtype, $_gender_icons;
    protected $_sell_price, $_buy_price, $_grade, $_dq_sell;

    const SERIALIZED_SIZE = 14;

    private static $cache = array();

    public static function FromID($id) {
        if(isset(self::$cache[$id])) {
            return self::$cache[$id];
        }
        $ret = MemoryCache::instance()->get('item-id-'.$id);
        if(!$ret) {
            $link = MySQL::instance();
            $result = mysql_fetch_object($link->query("SELECT type FROM items WHERE id = " . (int)$id, true));
            if(!$result) {
                return null;
            }
            switch($result->type) {
                case 'weapon':
                    $ret = Weapon::FromID($id);
                    break;
                case 'armour':
                    $ret = Armour::FromID($id);
                    break;
                case 'ornament':
                    $ret = Ornament::FromID($id);
                    break;
                case 'tome':
                    $ret = Tome::FromID($id);
                    break;
                case 'remedy':
                    $ret = Remedy::FromID($id);
                    break;
                case 'shard':
                    $ret = Soulgem::FromID($id);
                    break;
                case 'egg':
                    $ret = Egg::FromID($id);
                    break;
                default:
                    $ret = new Item(mysql_fetch_object($link->query("SELECT * FROM generic_items WHERE id = " . (int)$id, true)));
                    break;
            }
            MemoryCache::instance()->set('item-id-'.$id, $ret);
        }
        self::$cache[$id] = $ret;
        return $ret;
    }

    public static function DecomposedFrom($id) {
        $id = (int)$id;
        $decomposed_from = array();
        $link = MySQL::instance();
        $results = $link->query("SELECT * FROM weapons WHERE decompose_to = {$id}", true);
        while($row = mysql_fetch_object($results)) {
            $it = new Weapon($row);
            if($it->decompose_amount > 0) {
                $decomposed_from[] = $it;
            }
        }
        $results = $link->query("SELECT * FROM armor WHERE decompose_to = {$id}", true);
        while($row = mysql_fetch_object($results)) {
            $it = new Armour($row);
            if($it->decompose_amount > 0) {
                $decomposed_from[] = $it;
            }
        }
        $results = $link->query("SELECT * FROM ornaments WHERE decompose_to = {$id}", true);
        while($row = mysql_fetch_object($results)) {
            $it = new Ornament($row);
            if($it->decompose_amount > 0) {
                $decomposed_from[] = $it;
            }
        }
        return $decomposed_from;
    }

    protected function to_array() {
        return array(
            $this->_id, $this->_colour, $this->_name, $this->_description, $this->_class_mask, $this->_icon, $this->_stack_count,
            $this->_type, $this->_subtype, $this->_gender_icons,
            $this->_sell_price, $this->_buy_price, $this->_grade, $this->_dq_sell
        );
    }

    protected function from_array($array) {
        list($this->_id, $this->_colour, $this->_name, $this->_description, $this->_class_mask, $this->_icon, $this->_stack_count,
            $this->_type, $this->_subtype, $this->_gender_icons,
            $this->_sell_price, $this->_buy_price, $this->_grade, $this->_dq_sell) = $array;
    }

    public function serialize() {
        return igbinary_serialize($this->to_array());
    }

    public function unserialize($data) {
        $this->from_array(igbinary_unserialize($data));
    }

    public function __construct($record=null) {
        if($record == null) {
            return;
        }
        $this->_id = (int)$record->id;
        $this->_colour = (int)$record->colour;
        $this->_name = $record->name;
        $this->_description = $record->description;
        if(isset($record->class_mask)) {
            $this->_class_mask = (int)$record->class_mask;
        } else {
            $this->_class_mask = 0;
        }
        $this->_icon = $record->icon;
        if(isset($record->type)) {
            $this->_type = $record->type;
        }
        if(isset($record->subtype)) {
            $this->_subtype = (int)$record->subtype;
        }
        $this->_sell_price = (int)$record->sell_price;
        $this->_buy_price = (int)$record->buy_price;
        $this->_gender_icons = false;
        if($record->stack_count) {
            $this->_stack_count = (int)$record->stack_count;
        } else {
            $this->_stack_count = 1;
        }
        if(isset($record->grade)) {
            $this->_grade = (int)$record->grade;
        }
        if(isset($record->dq_sell)) {
            $this->_dq_sell = (int)$record->dq_sell;
        } else {
            $this->_dq_sell = 0;
        }

        // Try guessing at empty colouring.
        $this->_colour = self::GuessColour($this->_colour, $this->_name);
    }

    public static function GuessColour($colour, $name) {
        if(!$colour) {
            if(mb_substr($name, 0, 2, 'UTF-8') == '★★') {
                $colour = 4;
            } else if(mb_substr($name, 0, 1, 'UTF-8') == '★') {
                if(mb_substr($name, -8, 8, 'UTF-8') == '·Nirvana') {
                    $colour = 4;
                } else {
                    $colour = 2;
                }
            }
        }
        return $colour;
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

    public function link() {
        return "<a class='pw_color_{$this->_colour} item-link' href='/item/{$this->_id}'>【".$this->_name."】</a>";
    }

    protected function render_tooltip_header() {
        $tip = "<div class='item_tooltip'><p class='item_title pw_color_{$this->_colour}'>{$this->_name}</p>";
        $translated = Translate::TranslateField($this->_name);
        if($translated) {
            $tip .= "<p class='translation pw_color_{$this->_colour}'>{$translated}</p>";
        }
        if($this->_grade) {
            $tip .= "<p>Lv. {$this->_grade}</p>";
        }
        return $tip;
    }

    protected function render_tooltip_footer() {
        $tip = '';
        $flags = $this->_flags;
        if(!($flags & Element::ITEM_NO_NPC_SELL) && $this->_sell_price > 0) {
            $tip .= '<p>Price ' . number_format($this->_sell_price) . '</p>';
        }
        if($this->_buy_price > 0) {
            //$tip .= '<p>Buy Price: ' . number_format($this->_buy_price) . '</p>';
        }
        $more_tip = '';
        if($flags & Element::ITEM_DEATH_PROTECTED) {
            $more_tip .= "<p>Doesn't drop on death.</p>";
        }
        if($flags & Element::ITEM_NO_DISCARD) {
            $more_tip .= "<p>Unable to be discarded</p>";
        }
        if($flags & Element::ITEM_NO_NPC_SELL) {
            $more_tip .= "<p>Unable to be sold</p>";
        }
        if($flags & Element::ITEM_NO_TRADE) {
            $more_tip .= "<p>Unable to be traded</p>";
        }
        if($flags & Element::ITEM_NO_ACCOUNT_STASH) {
            $more_tip .= "<p>Unable to be put into Account Stash</p>";
        }
        if(!empty($this->_description)) {
            $desc = Text::ToHTML($this->_description);
            $more_tip .= "<p class='tooltip_description'>{$desc}</p>";
            $translated = Translate::TranslateField($desc, true);
            if($translated) {
                $more_tip .= "<p class='translation tooltip_description'>{$translated}</p>";
            }
        }
        if(!empty($more_tip)) {
            $tip .= "<br>{$more_tip}";
        }
        return $tip;
    }

    public function render_tooltip() {
        $tip = $this->render_tooltip_header();
        $tip .= $this->render_tooltip_footer();
        $tip .= "</div>";
        return $tip;
    }

    public function icon_url($gender='m') {
        return Element::IconURL($this->_icon, $gender);
    }

    public function decomposes_from() {
        return self::DecomposedFrom($this->_id);
    }

    public function sold_by() {
        $link = MySQL::instance();
        $result = $link->query(
           "SELECT contribution, npcs.*
            FROM npc_service_sell_items AS sell, npc_services AS service, npcs, spawn_points
            WHERE item = {$this->_id}
                AND service.service = sell.service
                AND npcs.id = service.npc
                AND spawn_points.spawn = npcs.id", true);
        $sellers = array();
        while($row = mysql_fetch_object($result)) {
            $sellers[] = array("contribution" => (int)$row->contribution, 'npc' => new NPC($row));
        }
        return $sellers;
    }
}
