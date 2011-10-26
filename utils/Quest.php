<?php
require_once 'utils.php';

class Quest implements Serializable {
    public static function FromID($id) {
        $cache = MemoryCache::instance();
        $ret = $cache->get('q-id-' . $id);
        if(!$ret) {
            $quests = Humongous::instance()->selectCollection('quests');
            $record = $quests->findOne(array('id' => (int)$id));
            if($record) {
                $ret = new Quest($record);
                $cache->set('q-id-' . $id, $ret);
            }
        }
        return $ret;
    }

    public static function HasReward($item) {
        $quests = Humongous::instance()->selectCollection('quests');
        $records = $quests->find(array('reward_success.item_groups.id' => (int)$item));
        $quests = array();
        foreach($records as $record) {
            $quests[] = new Quest($record);
        }
        usort($quests, function($a, $b) use ($item) {
            $a = $a->reward_success->items[$item]->probability;
            $b = $b->reward_success->items[$item]->probability;
            if($a == $b) return 0;
            if($a < $b) return 1;
            else return -1;
        });
        return $quests;
    }

    public static function RequiringMob($mob) {
        $quests = Humongous::instance()->selectCollection('quests');
        $records = $quests->find(array('required_mobs.mob_id' => (int)$mob));
        $quests = array();
        foreach($records as $record) {
            $quests[] = new Quest($record);
        }
        return $quests;
    }

    public static function RequiringItem($item) {
        $item = (int)$item;
        $quests = Humongous::instance()->selectCollection('quests');
        $records = $quests->find(
            array('$or' => array(
                array('required_mobs.item_id' => $item), 
                array('required_items.id' => $item),
                array('get_items.id' => $item)
            ))
        );
        $quests = array();
        foreach($records as $record) {
            $quests[] = new Quest($record);
        }
        return $quests;
    }

    public static function RequiringQuest($quest) {
        $quests = Humongous::instance()->selectCollection('quests');
        $records = $quests->find(array('required_quests' => (int)$quest));
        $quests = array();
        foreach($records as $record) {
            if($record['id'] != $quest)
                $quests[] = new Quest($record);
        }
        return $quests;
    }

    protected $_id, $_name, $_type, $_time_limit, $_can_give_up, $_repeatable, $_repeatable_after_failure;
    protected $_instant_teleport, $_ai_trigger, $_quest_npc, $_reward_npc, $_min_level, $_max_level;
    protected $_required_instant_coins, $_required_rep, $_required_quests, $_unrequired_quests;
    protected $_required_gender, $_class_mask, $_required_blacksmith, $_required_tailor, $_required_craftsman;
    protected $_required_apothecary, $_required_coins, $_wait_time, $_parent, $_previous, $_next, $_first_child;
    protected $_author, $_dates, $_required_items, $_given_items, $_trigger_locations, $_target_locations;
    protected $_reward_success, $_reward_failure, $_timers, $_rewards_timed, $_children, $_required_mobs, $_get_items;
    protected $_conversation, $_level;

    public function __construct($record) {
        $this->_id = $record['id'];
        $this->_name = $record['name'];
        $this->_type = isset($record['type']) ? $record['type'] : 0;
        if(isset($record['time_limit']))
            $this->_time_limit = $record['time_limit'];
        $this->_can_give_up = isset($record['can_give_up']) ? $record['can_give_up'] : false;
        $this->_repeatable = isset($record['repeatable']) ? $record['repeatable'] : false;
        $this->_repeatable_after_failure = isset($record['repeatable_after_failure']) ? $record['repeatable_after_failure'] : false;
        if($record['instant_teleport']['x'] != 0)
            $this->_instant_teleport = (object)$record['instant_teleport'];
        if(isset($record['ai_trigger']))
            $this->_ai_trigger = $record['ai_trigger'];
        if(isset($record['quest_npc']))
            $this->_quest_npc = NPC::FromID($record['quest_npc']);
        if(isset($record['reward_npc']))
            $this->_reward_npc = NPC::FromID($record['reward_npc']);
        $this->_min_level = isset($record['min_level']) ? $record['min_level'] : 1;
        $this->_max_level = isset($record['max_level']) ? $record['max_level'] : 150;
        $this->_level = isset($record['level']) ? $record['level'] : null;
        $this->_required_instant_coins = isset($record['required_instant_coins']) ? $record['required_instant_coins'] : 0;
        $this->_required_rep = isset($record['required_rep']) ? $record['required_rep'] : 0;
        $this->_required_quests = isset($record['required_quests']) ? $record['required_quests'] : array();
        $this->_unrequired_quests = isset($record['unrequired_quests']) ? $record['unrequired_quests'] : array();
        if(isset($record['required_gender']))
            $this->_required_gender = $record['required_gender'];
        if(isset($record['required_classes'])) {
            $classes = $record['required_classes'];
            $mask = 0;
            foreach($classes as $class) {
                $mask = ($mask | (1 << $class));
            }
            $this->_class_mask = $mask;
        } else {
            $this->_class_mask = Element::CLASS_ALL;
        }
        $this->_required_blacksmith = isset($record['required_blacksmith']) ? $record['required_blacksmith'] : 0;
        $this->_required_tailor = isset($record['required_tailor']) ? $record['required_tailor'] : 0;
        $this->_required_craftsman = isset($record['required_craftsman']) ? $record['required_craftsman'] : 0;
        $this->_required_apothecary = isset($record['required_apothecary']) ? $record['required_apothecary'] : 0;
        $this->_required_coins = isset($record['required_coins']) ? $record['required_coins'] : 0;
        if(isset($record['wait_time']))
            $this->_wait_time = $record['wait_time'];
        if(isset($record['parent']))
            $this->_parent = $record['parent'];
        if(isset($record['previous']))
            $this->_previous = $record['previous'];
        if(isset($record['next']))
            $this->_next = $record['next'];
        if(isset($record['first_child']))
            $this->_first_child = $record['first_child'];
        if(isset($record['author']))
            $this->_author = $record['author'];
        
        $this->_dates = isset($record['dates']) ? $record['dates'] : array();
        foreach($this->_dates as &$d) {
            $r = array(array(), array());
            if(!empty($d[0]['datetime'])) $r[0]['datetime'] = $d[0]['datetime']->sec;
            if(!empty($d[1]['datetime'])) $r[1]['datetime'] = $d[1]['datetime']->sec;
            if(!empty($d[0]['weekday'])) $r[0]['weekday'] = $d[0]['weekday'];
            if(!empty($d[1]['weekday'])) $r[1]['weekday'] = $d[1]['weekday'];
            $r[0] = (object)$r[0];
            $r[1] = (object)$r[1];
            $d = (object)array('start' => $r[0], 'end' => $r[1]);
        }
        unset($d);

        $this->_required_items = isset($record['required_items']) ? $record['required_items'] : array();
        $this->_get_items = isset($record['get_items']) ? $record['get_items'] : array();
        $this->_given_items = isset($record['given_items']) ? $record['given_items'] : array();
        $this->_trigger_locations = isset($record['trigger_locations']) ? $record['trigger_locations'] : array();
        $this->_target_locations = isset($record['target_locations']) ? $record['target_locations'] : array();
        $this->_timers = isset($record['timers']) ? $record['timers'] : array();
        $this->_children = isset($record['children']) ? $record['children'] : array();
        $this->_conversation = new QuestConversation($record['conversation']);
        
        if(isset($record['reward_success']))
            $this->_reward_success = new QuestReward($record['reward_success']);
        if(isset($record['reward_failure']))
            $this->_reward_failure = new QuestReward($record['reward_failure']);
        $this->_rewards_timed = array();
        if(isset($record['rewards_timed'])) {
            foreach($record['rewards_timed'] as $reward) {
                $this->_rewards_timed[] = new QuestReward($reward);
            }
        }

        $this->_required_mobs = array();
        if(isset($record['required_mobs'])) {
            foreach($record['required_mobs'] as $chase) {
                $this->_required_mobs[$chase['mob_id']] = (object)$chase;
            }
        }

        $this->_items = array();
        foreach($this->_required_mobs as $mob) {
            if(!empty($mob->item_id)) {
                if(!isset($this->_items[$mob->item_id])) {
                    $this->_items[$mob->item_id] = $mob->item_count;
                } else {
                    $this->_items[$mob->item_id] += $mob->item_count;
                }
            }
        }
        foreach($this->_required_items as $item) {
            if(!isset($this->_items[$item['id']])) {
                $this->_items[$item['id']] = $item['amount'];
            } else {
                $this->_items[$item['id']] += $item['amount'];
            }
        }
        foreach($this->_get_items as $item) {
            if(!isset($this->_items[$item['id']])) {
                $this->_items[$item['id']] = $item['amount'];
            } else {
                $this->_items[$item['id']] += $item['amount'];
            }
        }
    }

    public function plain_name() {
        return Text::StripFormatting($this->_name);
    }

    public function colour() {
        if(substr($this->_name, 0, 1) == '^') {
            return substr($this->_name, 1, 6);
        }
        return 'ffffff';
    }

    public function render_tooltip() {
        $tip = "<div class='item_tooltip'><p class='quest-title' style='color: #" . $this->colour() . "'>" . $this->plain_name() . "</p>";
        $translation = Translate::TranslateField(Text::ToHTML($this->_name), true);
        if($translation)
            $tip .= "<p class='translation'>{$translation}</p>";
        if($this->_level)
            $tip .= "<p>Level {$this->_level} ({$this->_min_level} – {$this->_max_level})</p>";
        foreach($this->_dates as $date) {
            $days = array(null, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
            if(!empty($date->start->weekday) && !empty($date->end->weekday) && $date->start->weekday == $date->end->weekday) {
                $tip .= "<p>Available on " . $days[$date->start->weekday] . "s</p>";
            } else {
                $tip .= "<p>Available from ";
                if(!empty($date->start->datetime)) {
                    $tip .= date('j/n/Y', $date->start->datetime) . ' ';
                } else if(!empty($date->start->weekday)) {
                    $tip .= $days[$date->start->weekday] . ' ';
                }
                $tip .= ' until ';
                if(!empty($date->end->datetime)) {
                    $tip .= date('j/n/Y', $date->end->datetime) . ' ';
                } else if(!empty($date->end->weekday)) {
                    $tip .= $days[$date->end->weekday] . ' ';
                }
                $tip .= "</p>";
            }
        }
        foreach($this->_trigger_locations as $location) {
            $location = (object)$location;
            $x = (int)round(($location->east + $location->west) / 2.0);
            $y = (int)round(($location->north + $location->south) / 2.0);
            $z = (int)round(($location->bottom + $location->top) / 2.0);
            $x_var = (int)round(($location->west - $location->east) / 2.0);
            $y_var = (int)round(($location->north - $location->south) / 2.0);
            $z_var = (int)round(($location->top - $location->bottom) / 2.0);
            $map = Map::FromID($location->map_id);
            $tip .= "<p>Triggered at: " . $map->get_place_name($x, $y) . " (<abbr title='{$x}±{$x_var} {$y}±{$y_var} ({$z}±{$z_var})'>{$x} {$y} ({$z})</abbr>)</p>";
        }
        foreach($this->_required_items as $item) {
            $item = (object)$item;
            $i = Item::FromID($item->id);
            if($i)
                $tip .= "<p>Requires: " . number_format($item->amount) . $i->link() . "</p>";
            else
                $tip .= "<p>Requires an unknown item ({$item->id})</p>";
        }
        if($this->_reward_success->exp || $this->_reward_success->spirit || $this->_reward_success->coins || $this->_reward_success->rep) {
            $tip .= "<p>Rewards:<ul>";
            if($this->_reward_success->exp)
                $tip .= "<li>Experience: ".number_format($this->_reward_success->exp) . "</li>";
            if($this->_reward_success->spirit)
                $tip .= "<li>Spirit: ".number_format($this->_reward_success->spirit) . "</li>";
            if($this->_reward_success->coins)
                $tip .= "<li>Coins: " . number_format($this->_reward_success->coins) . "</li>";
            if($this->_reward_success->rep)
                $tip .= "<li>Reputation: " . number_format($this->_reward_success->rep) . "</li>";
            $tip .= "</ul></p>";
        }
        foreach($this->_required_mobs as $mob) {
            if($mob->item_count) {
                $tip .= "<p>Collect: " . number_format($mob->item_count) . Item::FromID($mob->item_id)->link() . ' from ' . Mob::FromID($mob->mob_id)->link() . '(' .number_format($mob->drop_rate * 100) . "%)</p>";
            } else {
                $tip .= "<p>Kill: " . number_format($mob->mob_count) . Mob::FromID($mob->mob_id)->link() . "</p>";
            }
        }
        foreach($this->_get_items as $item) {
            $item = (object)$item;
            $i = Item::FromID($item->id);
            if($i)
                $tip .= "<p>Obtain: " . number_format($item->amount) . $i->link() . "</p>";
            else
                $tip .= "<p>Obtain an unknown item ({$item->id})</p>";
        }
        foreach($this->_target_locations as $location) {
            $location = (object)$location;
            $x = (int)round(($location->east + $location->west) / 2.0);
            $y = (int)round(($location->north + $location->south) / 2.0);
            $z = (int)round(($location->bottom + $location->top) / 2.0);
            $x_var = (int)round(($location->west - $location->east) / 2.0);
            $y_var = (int)round(($location->north - $location->south) / 2.0);
            $z_var = (int)round(($location->top - $location->bottom) / 2.0);
            $map = Map::FromID($location->map_id);
            $tip .= "<p>Reach: " . $map->get_place_name($x, $y) . " (<abbr title='{$x}±{$x_var} {$y}±{$y_var} ({$z}±{$z_var})'>{$x} {$y} ({$z})</abbr>)</p>";
        }
        $tip .= "</div>";
        return $tip;
    }

    public function link() {
        return "<a class='quest-link' href='/quest/{$this->_id}' style='color: #".$this->colour()."'>【".$this->plain_name()."】</a>";
    }

    public function get_parents() {
        if(!$this->_parent) {
            return array();
        }
        $parent = Quest::FromID($this->_parent);
        return array_merge($parent->get_parents(), array($parent));
    }

    public function path() {
        $parents = $this->get_parents();
        $path = array();
        foreach($parents as $parent) {
            $path[] = $parent->link();
        }
        $path[] = $this->link();
        return implode('→', $path);
    }

    public function __get($var) {
        $real_name = "_{$var}";
        if(property_exists($this, $real_name)) {
            return $this->$real_name;
        }
    }

    public function __isset($var) {
        $real_name = "_{$var}";
        return property_exists($this, $real_name);
    }

    public function get_chain($direction=0) {
        $chain = array($this);

        // Count it back!
        if($direction <= 0) {
            if(count($this->_required_quests) == 1) {
                $chain = array_merge(Quest::FromID($this->_required_quests[0])->get_chain(-1), $chain);
            }
        }

        // Count it forward!
        if($direction >= 0) {
            $next_quests = self::RequiringQuest($this->_id);
            foreach($next_quests as $next) {
                $chain = array_merge($chain, $next->get_chain(1));
            }
        }
        return $chain;
    }

    public function serialize() {
        $quest_npc = empty($this->_quest_npc) ? null : $this->_quest_npc->id;
        $reward_npc = empty($this->_reward_npc) ? null : $this->_reward_npc->id;
        return igbinary_serialize(array(
            $this->_id, $this->_name, $this->_type, $this->_time_limit, $this->_can_give_up, $this->_repeatable, $this->_repeatable_after_failure,
            $this->_instant_teleport, $this->_ai_trigger, $quest_npc, $reward_npc, $this->_min_level, $this->_max_level,
            $this->_required_instant_coins, $this->_required_rep, $this->_required_quests, $this->_unrequired_quests,
            $this->_required_gender, $this->_class_mask, $this->_required_blacksmith, $this->_required_tailor, $this->_required_craftsman,
            $this->_required_apothecary, $this->_required_coins, $this->_wait_time, $this->_parent, $this->_previous, $this->_next, $this->_first_child,
            $this->_author, $this->_dates, $this->_required_items, $this->_given_items, $this->_trigger_locations, $this->_target_locations,
            $this->_reward_success, $this->_reward_failure, $this->_timers, $this->_rewards_timed, $this->_children, $this->_required_mobs, $this->_get_items,
            $this->_conversation, $this->_level
        ));
    }

    public function unserialize($data) {
        list(
            $this->_id, $this->_name, $this->_type, $this->_time_limit, $this->_can_give_up, $this->_repeatable, $this->_repeatable_after_failure,
            $this->_instant_teleport, $this->_ai_trigger, $quest_npc, $reward_npc, $this->_min_level, $this->_max_level,
            $this->_required_instant_coins, $this->_required_rep, $this->_required_quests, $this->_unrequired_quests,
            $this->_required_gender, $this->_class_mask, $this->_required_blacksmith, $this->_required_tailor, $this->_required_craftsman,
            $this->_required_apothecary, $this->_required_coins, $this->_wait_time, $this->_parent, $this->_previous, $this->_next, $this->_first_child,
            $this->_author, $this->_dates, $this->_required_items, $this->_given_items, $this->_trigger_locations, $this->_target_locations,
            $this->_reward_success, $this->_reward_failure, $this->_timers, $this->_rewards_timed, $this->_children, $this->_required_mobs, $this->_get_items,
            $this->_conversation, $this->_level
        ) = igbinary_unserialize($data);
        $this->_quest_npc = empty($quest_npc) ? null : NPC::FromID($quest_npc);
        $this->_reward_npc = empty($reward_npc) ? null : NPC::FromID($reward_npc);
    }
}
