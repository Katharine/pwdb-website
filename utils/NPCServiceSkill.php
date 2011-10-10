<?php
class NPCServiceSkill extends NPCService {
    public function __construct($id) {
        $this->id = (int)$id;
        $link = MySQL::instance();
        $this->name = mysql_fetch_object($link->query("SELECT name FROM npc_service_skill WHERE id = {$this->id}", true))->name;
        $results = $link->query(
           "SELECT name, skill 
            FROM npc_service_skill_skills, skill_descriptions 
            WHERE skill_descriptions.id = npc_service_skill_skills.skill 
                AND service = {$this->id}", true);
        $this->skills = array();
        while($row = mysql_fetch_object($results)) {
            $this->skills[(int)$row->skill] = $row->name;
        }
    }

    public function name() {
        return $this->name;
    }
}
