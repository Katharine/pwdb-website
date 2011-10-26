<?php
class Element {
    const CLASS_BLADEMASTER = 0x001;
    const CLASS_WIZARD      = 0x002;
    const CLASS_PSYCHIC     = 0x004;
    const CLASS_VENOMANCER  = 0x008;
    const CLASS_BARBARIAN   = 0x010;
    const CLASS_ASSASSIN    = 0x020;
    const CLASS_ARCHER      = 0x040;
    const CLASS_CLERIC      = 0x080;
    const CLASS_SEEKER      = 0x100;
    const CLASS_MYSTIC      = 0x200;
    const CLASS_ALL         = 0x3FF;
    const CLASS_NONE        = 0x000;

    const ITEM_DEATH_PROTECTED  = 0x0001;
    const ITEM_NO_DISCARD       = 0x0002;
    const ITEM_NO_NPC_SELL      = 0x0004;
    const ITEM_NO_TRADE         = 0x0010;
    const ITEM_BIND_ON_EQUIP    = 0x0040;
    const ITEM_NO_LEAVE_AREA    = 0x0100;
    const ITEM_NO_ACCOUNT_STASH = 0x4000; // or 0x20?

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    public static function IconURL($path, $gender='m') {
        $temp = explode('\\', $path);
        $temp = end($temp);
        $temp = explode('.', $temp);
        $icon = $temp[0];
        return sprintf(PW_ICON_URL, $gender, $icon);
    }

    public static function Int2Float($int) {
        $float = unpack('f', pack('L', $int));
        return $float[1];
    }

    public static function CultivationString($id) {
        switch($id) {
            case 0:
                return "Spiritual Initiate";
            case 1:
                return "Spiritual Adept";
            case 2:
                return "Aware of Principle";
            case 3:
                return "Aware of Harmony";
            case 4:
                return "Aware of Discord";
            case 5:
                return "Aware of Coalescence";
            case 6:
                return "Transcendant";
            case 7:
                return "Enlightened One";
            case 8:
                return "Aware of Vacuity";
            case 20:
                return "Aware of the Myriad";
            case 21:
                return "Master of Harmony";
            case 22:
                return "Celestial Sage";
            case 30:
                return "Aware of the Void";
            case 31:
                return "Master of Discord";
            case 32:
                return "Celestial Demon";
            default:
                return (string)$id;
        }
    }
}
