<?php
require_once 'utils.php';

class QuestTrigger extends Item {
    protected $_quests;

    const SERIALIZED_SIZE = 1;

    protected function to_array() {
        return array_merge(array($this->_quests), parent::to_array());
    }

    protected function from_array($array) {
        list($this->_quests) = array_slice($array, 0, self::SERIALIZED_SIZE);
        parent::from_array(array_slice($array, self::SERIALIZED_SIZE));
    }
    
    public function __construct($record) {
        parent::__construct($record);
        $this->_quests = $record->quests;
        foreach($this->_quests as &$quest) {
            $quest = (object)$quest;
        }
        unset($quest);
    }

    public function is_anniversary_pack() {
        if(count($this->_quests) == 1) {
            $quest = Quest::FromID($this->_quests[0]->id);
            if(!empty($quest->reward_success->item_groups) && count($quest->reward_success->item_groups[0]) > 0) {
                if(count($quest->get_items) == 0 && count($quest->required_mobs) == 0 && empty($quest->reward_npc)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function contents() {
        if(!$this->is_anniversary_pack()) {
            return array();
        }
        $contents = Quest::FromID($this->_quests[0]->id)->reward_success->item_groups[0];
        foreach($contents as &$content) {
            $content->item = Item::FromID($content->id);
        }
        unset($content);
        return $contents;
    }
}
