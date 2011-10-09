<?php
require_once 'utils/utils.php';

class NPCService {
    const SERVICE_BUY = 1108;
    const SERVICE_REPAIR = 2088;
    const SERVICE_IMBUE = 2086;
    const SERVICE_PURGE = 2087;
    const SERVICE_HEAL = 2089;
    const SERVICE_IDENTIFY = 3375;
    const SERVICE_RESET = 10227;
    const SERVICE_RESET_BUT_SOMEHOW_DIFFERENT = 10226;
    const SERVICE_PET_RENAME = 12283;
    const SERVICE_PET_LEARN = 12298;
    const SERVICE_PET_FORGET = 12284;
    const SERVICE_BIND = 12333;
    const SERVICE_DESTROY = 12335;
    const SERVICE_UNDESTROY = 12334;

    const SERVICE_DECOMPOSE_ITEM = 2099;
    const SERVICE_DECOMPOSE_WEAPON = 3574;
    const SERVICE_DECOMPOSE_EQUIPMENT = 3575;
    const SERVICE_DECOMPOSE_ORNAMENT = 3576;

    public static function ForNPC($id) {
        $id = (int)$id;
        $results = MySQL::instance()->query("SELECT service, type FROM npc_services WHERE npc = {$id}", true);
        $services = array();
        while($row = mysql_fetch_object($results)) {
            $services[] = self::FromID($row->service, $row->type);
        }
        return $services;
    }

    public static function FromID($id, $type = null) {
        $id = (int)$id;
        if($type === null) {
            $type = mysql_fetch_object(MySQL::instance()->query("SELECT type FROM npc_services WHERE service = {$id} LIMIT 1", true))->type;
        }
        $ret = null;
        switch($type) {
            case 'sell':
                $ret = new NPCServiceSell($id);
                break;
            case 'crafting':
                $ret = new NPCServiceCraft($id);
                break;
            case 'start_quest':
                // $ret = NPCServiceStartQuest::FromID($id);
                // break;
            case 'end_quest':
                // $ret = NPCServiceEndQuest::FromID($id);
                // break;
            case 'buy':
            case 'repair':
            case 'imbue':
            case 'purge':
            case 'healing':
            case 'bank':
            case 'decompose':
            case 'identify':
            case 'turret':
            case 'reset':
            case 'pet_rename':
            case 'pet_learn':
            case 'pet_forget':
            case 'bind':
            case 'destroy':
            case 'undestroy':
            case 'genie':
                $ret = new NPCService($id, $type);
        }
        return $ret;
    }

    public $id, $type;

    public function __construct($id, $type) {
        $this->id = (int)$id;
        $this->type = $type;
    }

    public function get_npcs() {
        $results = MySQL::instance()->query(
           "SELECT npcs.*
            FROM npc_services, npcs, spawn_points
            WHERE npcs.id = npc_services.npc
                AND service = {$this->id}
                AND spawn = npcs.id", true);
        $npcs = array();
        while($row = mysql_fetch_object($results)) {
            $npcs[]= new NPC($row);
        }
        return $npcs;
    }

    public function name() {
        if($this->type == 'start_quest') {
            return "Starts quests";
        }
        if($this->type == 'end_quest') {
            return "Ends quests";
        }
        switch($this->id) {
            case self::SERVICE_IMBUE:
                return "Imbue Soulgem";
            case self::SERVICE_PURGE:
                return "Purge Soulgem";
            case self::SERVICE_REPAIR:
                return "Repair Equipment";
            case self::SERVICE_HEAL:
                return "Healing Service";
            case self::SERVICE_IDENTIFY:
                return "Identify Equipment";
            case self::SERVICE_RESET:
            case self::SERVICE_RESET_BUT_SOMEHOW_DIFFERENT:
                return "Reset Skill Points";
            case self::SERVICE_PET_RENAME:
                return "Rename Pet";
            case self::SERVICE_PET_LEARN:
                return "Teach Pet Skill";
            case self::SERVICE_PET_FORGET:
                return "Forget Pet Skill";
            case self::SERVICE_BIND:
                return "Bind Equipment";
            case self::SERVICE_DESTROY:
                return "Destroy Bound Equipment";
            case self::SERVICE_UNDESTROY:
                return "Cancel Equipment Destruction";
            case self::SERVICE_DECOMPOSE_ITEM:
                return "Decompose Item";
            case self::SERVICE_DECOMPOSE_WEAPON:
                return "Decompose Weapon";
            case self::SERVICE_DECOMPOSE_EQUIPMENT:
                return "Decompose Armour";
            case self::SERVICE_DECOMPOSE_ORNAMENT:
                return "Decompose Ornament";
            case self::SERVICE_BUY:
                return "Buy Items";
            default:
                return null;
        }
    }
}
