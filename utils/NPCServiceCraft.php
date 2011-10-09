<?php
require_once 'utils.php';

class NPCServiceCraft extends NPCService {
    public $id, $name, $tabs, $skill, $dragdrop;

    public function __construct($id) {
        $this->id = (int)$id;
        $link = MySQL::instance();
        $record = mysql_fetch_object($link->query("SELECT name, skill, dragdrop FROM npc_service_crafting WHERE id = {$this->id}", true));
        $this->name = $record->name;
        $this->skill = (int)$record->skill;
        $this->dragdrop = (bool)((int)$record->dragdrop);
        $this->tabs = array();
        $results = $link->query("SELECT recipe, name AS tab FROM npc_service_crafting_tabs AS tabs, npc_service_crafting_recipes AS recipes WHERE recipes.tab = tabs.id AND recipes.service = {$this->id}", true);
        while($row = mysql_fetch_object($results)) {
            if(!isset($this->tabs[$row->tab])) {
                $this->tabs[$row->tab] = array();
            }
            $recipe = Recipe::FromID($row->recipe);
            if($recipe) {
                $this->tabs[$row->tab][] = $recipe;
            }
        }
    }

    public function name() {
        return $this->name;
    }
}
