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
}
