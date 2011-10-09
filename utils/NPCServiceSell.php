<?php
require_once 'utils.php';

class NPCServiceSell extends NPCService {
    public $id, $name, $tabs;

    public function __construct($id) {
        $this->id = (int)$id;
        $link = MySQL::instance();
        $this->name = mysql_fetch_object($link->query("SELECT name FROM npc_service_sell WHERE id = {$this->id}", true))->name;
        $this->tabs = array();
        $results = $link->query("SELECT item, contribution, name AS tab FROM npc_service_sell_tabs AS tabs, npc_service_sell_items AS items WHERE items.tab = tabs.id AND items.service = {$this->id}", true);
        while($row = mysql_fetch_object($results)) {
            if(!isset($this->tabs[$row->tab])) {
                $this->tabs[$row->tab] = array();
            }
            $item = Item::FromID($row->item);
            if($item) {
                $this->tabs[$row->tab][] = array('contribution' => (int)$row->contribution, 'item' => $item);
            }
        }
    }

    public function name() {
        return $this->name;
    }
}
