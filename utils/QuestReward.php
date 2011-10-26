<?php
class QuestReward implements Serializable {
    public $coins, $exp, $new_quest, $spirit, $rep, $cultivation, $storage_slots;
    public $cupboard_slots, $wardrobe_slots, $account_stash_slots, $inventory_slots;
    public $pet_bag_slots, $chi, $teleport, $ai_trigger, $pq_quest, $pq_level;
    public $pq_rand_min, $pq_rand_max, $pq_reward_min, $item_groups;

    public function __construct($record) {
        $this->coins = $record['coins'];
        $this->exp = $record['exp'];
        $this->new_quest = $record['new_quest'] ? $record['new_quest'] : null;
        $this->spirit = $record['spirit'];
        $this->rep = $record['rep'];
        $this->cultivation = $record['culti'] ? $record['culti'] : null;
        $this->storage_slots = $record['storage_slots'] ? $record['storage_slots'] : null;
        $this->wardrobe_slots = $record['wardrobe_slots'] ? $record['wardrobe_slots'] : null;
        $this->account_stash_slots = $record['account_stash_slots'] ? $record['account_stash_slots'] : null;
        $this->inventory_slots = $record['inventory_slots'] ? $record['inventory_slots'] : null;
        $this->pet_bag_slots = $record['pet_bag_slots'] ? $record['pet_bag_slots'] : null;
        $this->chi = $record['chi'] ? $record['chi'] : null;
        $this->pq_quest = $record['pq_quest'] ? $record['pq_quest'] : null;
        $this->pq_level = $record['pq_level'] ? $record['pq_level'] : null;
        $this->pq_rand_min = $record['pq_rand_min'] ? $record['pq_rand_min'] : null;
        $this->pq_rand_max = $record['pq_rand_max'] ? $record['pq_rand_max'] : null;
        $this->pq_reward_min = $record['pq_reward_min'] ? $record['pq_reward_min'] : null;
        $this->teleport = $record['teleport']['x'] != 0 ? (object)$record['teleport'] : null;
        $this->items = array();
        $this->item_groups = array();

        foreach($record['item_groups'] as $item) {
            $item = (object)$item;
            $this->items[$item->id] = $item;
            if(!isset($this->item_groups[$item->group])) {
                $this->item_groups[$item->group] = array();
            }
            $this->item_groups[$item->group][] = $item;
        }
    }

    public function serialize() {
        return igbinary_serialize(array(
            $this->coins, $this->exp, $this->new_quest, $this->spirit, $this->rep, $this->cultivation, $this->storage_slots,
            $this->cupboard_slots, $this->wardrobe_slots, $this->account_stash_slots, $this->inventory_slots,
            $this->pet_bag_slots, $this->chi, $this->teleport, $this->ai_trigger, $this->pq_quest, $this->pq_level,
            $this->pq_rand_min, $this->pq_rand_max, $this->pq_reward_min, $this->item_groups
        ));
    }

    public function unserialize($data) {
        list(
            $this->coins, $this->exp, $this->new_quest, $this->spirit, $this->rep, $this->cultivation, $this->storage_slots,
            $this->cupboard_slots, $this->wardrobe_slots, $this->account_stash_slots, $this->inventory_slots,
            $this->pet_bag_slots, $this->chi, $this->teleport, $this->ai_trigger, $this->pq_quest, $this->pq_level,
            $this->pq_rand_min, $this->pq_rand_max, $this->pq_reward_min, $this->item_groups
        ) = igbinary_unserialize($data);
    }
}
