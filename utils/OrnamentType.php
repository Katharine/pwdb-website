<?php
require_once 'utils.php';

class OrnamentType implements Serializable {
    public $id, $name, $flags;

    private static $cache = array();

    public static function FromID($id) {
        if(isset(self::$cache[$id])) {
            return self::$cache[$id];
        }
        $it = MemoryCache::instance()->get('ot-id-'.$id);
        if(!$it) {
            $it = new OrnamentType($id);
            MemoryCache::instance()->set('ot-id-'.$id, $it);
        }
        self::$cache[$id] = $it;
        return $it;
    }

    public function __construct($id) {
        $link = MySQL::instance();
        $record = mysql_fetch_object($link->query("SELECT id, name, flags FROM ornament_subtypes WHERE id = " . (int)$id, true));
        $this->id = (int)$record->id;
        $this->name = $record->name;
        $this->flags = (int)$record->flags;
    }

    public function serialize() {
        return igbinary_serialize(array($this->id, $this->name, $this->flags));
    }

    public function unserialize($data) {
        list($this->id, $this->name, $this->flags) = igbinary_unserialize($data);
    }

    public function __toString() {
        return $this->name;
    }
}
