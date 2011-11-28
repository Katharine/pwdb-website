<?php
require_once 'utils.php';

class ItemAddon implements Serializable {
    private static $cache = array();
    public static function FromID($ids) {
        $array = true;
        if(!is_array($ids)) {
            $ids = array($ids);
            $array = false;
        }
        $returns = array();
        $queries = array();
        foreach($ids as $id) {
            $id = (int)$id;
            if($id == 0) {
                $returns[$id] = 0;
                continue;
            }
            if(isset(self::$cache[$id])) {
                $returns[$id] = self::$cache[$id];
                continue;
            }
            $ret = MemoryCache::instance()->get('ia-id'.$id);
            if($ret) {
                $returns[$id] = $ret;
                self::$cache[$id] = $ret;
                continue;
            }
            $queries[] = $id;
        }
        if(count($queries)) {
            $link = MySQL::instance();
            $result = $link->query("SELECT addons.id as addon, display, value_type, multiply, `values`, value1, value2, value3, `group` FROM addon_groups, addons WHERE addons.group = addon_groups.id AND addons.id IN (" . implode(', ', $queries) . ")", true);
            while($row = mysql_fetch_object($result)) {
                $returns[$row->addon] = new ItemAddon($row);
                MemoryCache::instance()->set('ia-id'.$row->addon, $ret);
            }
        }
        foreach($queries as $query) {
            if(!isset($returns[$query])) {
                $returns[$query] = 0;
            }
        }
        if($array)
            return $returns;
        else
            return array_pop($returns);
    }

    public static function FromRecords($records) {
        $addons = array();
        foreach($records as $record) {
            $addons[] = $record['id'];
        }
        $addons = self::FromID($addons);
        foreach($records as $record) {
            $addons[$record['id']]->probability = $record['probability'];
        }
        return array_values($addons);
    }

    public static function AddonsForItem($item, $type = null) {
        trigger_error("Use of dead function ItemAddon::AddonsForItem({$item}, '{$type}')", E_USER_WARNING);
        $link = MySQL::instance();
        $type_query = '';
        if($type != null) {
            $type_query = "AND `type` = '" . $link->escape($type) . "'";
        }
        $item = (int)$item;
        $result = $link->query("SELECT `item_addons`.`addon`, display, value_type, multiply, `values`, value1, value2, value3, `type`, `group`, `probability` FROM addon_groups, addons, item_addons WHERE item_addons.addon = addons.id AND addons.group = addon_groups.id {$type_query} AND item_addons.item = $item", true);
        $addons = array();
        while($row = mysql_fetch_object($result)) {
            $addons[] = new ItemAddon($row);
        }
        return $addons;
    }

    public $display, $values, $id, $type, $probability, $group;

    public function serialize() {
        return igbinary_serialize(array($this->display, $this->values, $this->id, $this->type, $this->probability, $this->group));
    }

    public function unserialize($data) {
        list($this->display, $this->values, $this->id, $this->type, $this->probability, $this->group) = igbinary_unserialize($data);
    }

    public function __construct($record) {
        $this->display = $record->display;
        $this->values = array();
        if($record->values > 0) {
            $this->values[] = $record->value1;
            if($record->values > 1) {
                $this->values[] = $record->value2;
                if($record->values > 2) {
                    $this->values[] = $record->value3;
                }
            }
        }
        $this->group = $record->group;
        if(isset($record->probability)) {
            $this->probability = (float)$record->probability;
        }
        if(isset($record->type)) {
            $this->type = $record->type;
        }
        $this->id = (int)$record->addon;
        if($record->value_type == 'float') {
            foreach($this->values as &$value) {
                $value = (Element::Int2Float($value) + 0.0001) * $record->multiply;
            }
        }
    }

    public function render_range($min = null, $max = null) {    
        if($min == null) {
            $values = $this->values;
        } else {
            $values = array($min);
            if($max != null) {
                $values[] = $max;
            }
        }
        $matches = preg_match('/(%\+?)([^ ]+)/', $this->display, $formats);
        $format = '';
        $format_range = '';
        if($matches) {
            $format = $formats[0];
            $format_range = $formats[1] . $formats[2] . '~%' . $formats[2];
        }
        if(count($values) > 1 && $values[0] != $values[1]) {
            return vsprintf($format_range, $values);
        } else {
            return vsprintf($format, $values);
        }
    }

    public function render_title() {
        return sprintf(preg_replace('/\s*%[^%].*/', '', $this->display));
    }

    public function label() {
        if($this->group == 55) {
            return sprintf(mysql_fetch_object(MySQL::instance()->query("SELECT description FROM skill_descriptions WHERE id = {$this->values[0]}", true))->description);
        } else {
            $display = $this->display;
            if(count($this->values) > 1) {
                if($this->values[0] != $this->values[1] && $this->values[1] != 0) {
                    $display = preg_replace('/(%\+?)([^ ]+)/', '\1\2~%\2', $this->display);
                }
            }
            return vsprintf($display, $this->values);
        }
    }
}
