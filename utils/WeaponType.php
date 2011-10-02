<?php
require_once 'utils.php';

class WeaponType implements Serializable {
    public $id, $name, $gfx, $sfx, $interval, $min_range, $action_type, $exists;

    private static $cache = array();

    public static function FromID($id) {
        if(isset(self::$cache[$id])) {
            return self::$cache[$id];
        }
        $it = MemoryCache::instance()->get('wt-id-'.$id);
        if(!$it) {
            $it = new WeaponType($id);
            MemoryCache::instance()->set('wt-id-'.$id, $it);
        }
        self::$cache[$id] = $it;
        return $it;
    }

    public function __construct($id, $link=null) {
        if(!$link) {
            $link = MySQL::instance();
        }
        $record = mysql_fetch_object($link->query("SELECT id, name, gfx, sfx, `interval`, short_range, action_type, `exists` FROM weapon_subtypes WHERE id = " . (int)$id, true));
        $this->id = (int)$record->id;
        $this->name = $record->name;
        $this->gfx = $record->gfx;
        $this->sfx = $record->sfx;
        $this->interval = (float)$record->interval;
        $this->min_range = (float)$record->short_range;
        $this->action_type = (int)$record->action_type;
        $this->exists = (bool)$record->exists;
    }

    public function serialize() {
        return igbinary_serialize(array($this->id, $this->name, $this->gfx, $this->sfx, $this->interval, $this->min_range, $this->action_type, $this->exists));
    }

    public function unserialize($data) {
        list($this->id, $this->name, $this->gfx, $this->sfx, $this->interval, $this->min_range, $this->action_type, $this->exists) = igbinary_unserialize($data);
    }

    public function __toString() {
        return $this->name;
    }
}
