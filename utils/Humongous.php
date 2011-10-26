<?php
class Humongous {
    static $db;
    public static function instance() {
        if(!self::$db) {
            $mongo = new Mongo();
            $db = $mongo->selectDB('pwdb');
            $db->authenticate('pwdbrw', 'f1fCwC6jmKKOtcxy93Va');
            self::$db = $db;
        }
        return self::$db;
    }
}
