<?php
require_once 'utils.php';

class NPC extends Spawn {
    protected $_id, $_name, $_type, $_killable, $_model, $_mob, $_introduction, $_territory;

    public static function FromID($id) {
        $id = (int)$id;
        $npc = MemoryCache::instance()->get('npc-id-'.$id);
        if(!$npc) {
            $record = mysql_fetch_object(MySQL::instance()->query("SELECT * FROM npcs WHERE id = {$id}", true));
            if($record) {
                $npc = new NPC($record);
                MemoryCache::instance()->set('npc-id-'.$id, $npc);
            }
        }
        if(!$npc) {
            $npc = null;
        }
        return $npc;
    }

    public function __construct($record) {
        $this->_kind = 'npc';
        if(!$record) {
            return;
        }
        $this->_id = (int)$record->id;
        $this->_name = $record->name;
        $this->_type = (int)$record->type;
        $this->_killable = (bool)((int)$record->killable);
        $this->_model = $record->model;
        $this->_mob = Mob::FromID($record->mob);
        $this->_introduction = $record->introduction;
        $this->_territory = (bool)((int)$record->territory);
    }

    protected function render_tooltip_header() {
        $tip = "<div class='item_tooltip'><p><span class='npc-title pw_color_0'>{$this->_name}</span></p>";
        $translated = Translate::TranslateField($this->_name);
        if($translated) {
            $tip .= "<p class='translation'>{$translated}</p>";
        }
        return $tip;
    }

    public function render_tooltip($map=null, $x=null, $y=null) {
        $tip = $this->render_tooltip_header();
        $tip .= $this->render_tooltip_location($map, $x, $y);
        $tip .= "</div>";
        return $tip;
    }
}
