<?php
require_once 'utils.php';

class ArmourType implements Serializable {
    public $id, $name, $mask;

    private static $cache = array();

    public static function FromID($id) {
        if(isset(self::$cache[$id])) {
            return self::$cache[$id];
        }
        $it = MemoryCache::instance()->get('at-id-'.$id);
        if(!$it) {
            $it = new ArmourType($id);
            MemoryCache::instance()->set('at-id-'.$id, $it);
        }
        self::$cache[$id] = $it;
        return $it;
    }

    public function __construct($id, $link=null) {
        if(!$link) {
            $link = MySQL::instance();
        }
        // We do it this way to avoid interfering in any current result set our creator might have.
        $record = mysql_fetch_object($link->query("SELECT id, name, mask FROM armor_subtypes WHERE id = " . (int)$id, true));
        $this->id = (int)$record->id;
        $this->name = $record->name;
        $this->mask = (int)$record->mask;
    }

    public function serialize() {
        return igbinary_serialize(array($this->id, $this->name, $this->mask));
    }

    public function unserialize($data) {
        list($this->id, $this->name, $this->mask) = igbinary_unserialize($data);
    }

    public function __toString() {
        return $this->name;
    }
}
