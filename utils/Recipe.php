<?php
require_once 'utils.php';
class Recipe {
    public static function UsesItem($id) {
        $id = (int)$id;
        $results = MySQL::instance()->query(
           "SELECT recipe_input.recipe 
            FROM recipe_input, npc_service_crafting_recipes AS craft, npc_services, spawn_points
            WHERE item = {$id}
                AND craft.recipe = recipe_input.recipe 
                AND npc_services.service = craft.service 
                AND spawn = npc 
            GROUP BY recipe_input.recipe", true);
        $recipes = array();
        while($row = mysql_fetch_object($results)) {
            $recipes[] = new Recipe($row->recipe);
        }
        return $recipes;
    }

    public static function CreatesItem($id) {
        $id = (int)$id;
        $results = MySQL::instance()->query(
           "SELECT recipe_output.recipe 
            FROM recipe_output, npc_service_crafting_recipes AS craft, npc_services, spawn_points
            WHERE item = {$id}
                AND craft.recipe = recipe_output.recipe
                AND npc_services.service = craft.service
                AND spawn = npc
            GROUP BY recipe_output.recipe", true);
        $recipes = array();
        while($row = mysql_fetch_object($results)) {
            $recipes[] = new Recipe($row->recipe);
        }
        return $recipes;
    }

    public static function FromID($id) {
        return new Recipe($id);
    }

    public $type, $subtype, $name, $craft_level, $craft_skill, $price, $failure_rate, $exp, $spirit, $quantity, $upgrade_for;
    public $inputs, $outputs, $craft_services, $id;

    protected function to_array() {
        $inputs = array();
        $outputs = array();
        foreach($this->inputs as $input) {
            $inputs[] = array($input['item']->id, $input['quantity']);
        }
        foreach($this->outputs as $output) {
            $outputs[] = array($output['item']->id, $output['probability']);
        }
        return array(
            $this->type, $this->subtype, $this->name, $this->craft_level, $this->craft_skill, $this->price, $this->failure_rate, $this->exp, $this->spirit, $this->quantity, $this->upgrade_for,
            $inputs, $outputs, $this->craft_services, $this->id
        );
    }

    protected function from_array($array) {
        if(count($array) != 15) {
            return false;
        }
        list($this->type, $this->subtype, $this->name, $this->craft_level, $this->craft_skill, $this->price, $this->failure_rate, $this->exp, $this->spirit, $this->quantity, $this->upgrade_for,
            $inputs, $outputs, $this->craft_services, $this->id) = $array;
        foreach($inputs as $input) {
            $item = Item::FromID($input[0]);
            if($item) {
                $this->inputs[] = array('item' => $item, 'quantity' => $input[1]);
            }
        }
        foreach($outputs as $output) {
            $item = Item::FromID($output[0]);
            if($item) {
                $this->outputs[] = array('item' => $item, 'probability' => $output[1]);
            }
        }
        return true;
    }

    public function __construct($id) {
        $id = (int)$id;
        $this->id = $id;
        $cached = MemoryCache::instance()->get('r-'.$id);
        if($cached) {
            if($this->from_array(igbinary_unserialize($cached))) {
                return;
            }
        }
        $link = MySQL::instance();
        $result = $link->query("SELECT type, subtype, name, craft_level, craft_skill, price, failure_rate, exp, spirit, quantity, upgrade_for FROM recipes WHERE id = {$id}", true);
        $record = mysql_fetch_object($result);
        if(!$record) {
            return;
        }
        $this->type = (int)$record->type;
        $this->subtype = (int)$record->subtype;
        $this->craft_level = (int)$record->craft_level;
        $this->craft_skill = (int)$record->craft_skill;
        $this->price = (int)$record->price;
        $this->failure_rate = (float)$record->failure_rate;
        $this->exp = (int)$record->exp;
        $this->spirit = (int)$record->spirit;
        $this->quantity = (int)$record->quantity;
        $this->upgrade_for = (int)$record->upgrade_for;

        $result = $link->query("SELECT item, quantity FROM recipe_input WHERE recipe = {$id}", true);
        $this->inputs = array();
        while($row = mysql_fetch_object($result)) {
            foreach($this->inputs as &$input) {
                if($input['item']->id == $row->item) {
                    $input['quantity']++;
                    continue 2;
                }
            }
            $item = Item::FromID($row->item);
            if($item)
                $this->inputs[] = array('item' => $item, 'quantity' => (int)$row->quantity);
        }

        $result = $link->query("SELECT item, probability FROM recipe_output WHERE recipe = {$id}", true);
        $this->outputs = array();
        while($row = mysql_fetch_object($result)) {
            $item = Item::FromID($row->item);
            if($item)
                $this->outputs[] = array('item' => $item, 'probability' => (float)$row->probability * (1 - $this->failure_rate));
        }
        $result = $link->query("SELECT service FROM npc_service_crafting_recipes WHERE recipe = {$this->id}", true);
        $this->craft_services = array();
        while($row = mysql_fetch_object($result)) {
            $this->craft_services[] = (int)$row->service;
        }
        MemoryCache::instance()->set('r-'.$id, igbinary_serialize($this->to_array()));
    }

    public function get_craft_npcs() {
        $npcs = array();
        foreach($this->craft_services as $service) {
            $service = new NPCServiceCraft($service);
            if($service) {
                foreach($service->get_npcs() as $npc) {
                    if(!in_array($npc, $npcs)) {
                        $npcs[] = $npc;
                    }
                }
            }
        }
        return $npcs;
    }
}
