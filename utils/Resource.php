<?php
require_once 'utils.php';

class Resource extends Spawn implements Serializable {
    protected $_id, $_type, $_name, $_grade, $_level, $_tool, $_min_time, $_max_time;
    protected $_exp, $_spirit, $_model, $_quantities, $_quest, $_uninterruptible, $_permanent;

    public static function FromID($id) {
        $id = (int)$id;
        $cache = MemoryCache::instance();
        $ret = $cache->get('res-id-' . $id);
        if(!$ret) {
            $record = mysql_fetch_object(MySQL::instance()->query("SELECT * FROM resources WHERE id = {$id}", true));
            if($record) {
                $ret = new Resource($record);
                $cache->set('res-id-'.$id, $ret);
            }
        }
        return $ret;
    }

    public function __construct($record) {
        $this->_kind = 'resource';
        $this->_id = (int)$record->id;
        $this->_type = (int)$record->type;
        $this->_name = $record->name;
        $this->_grade = (int)$record->grade;
        $this->_level = (int)$record->level ? (int)$record->level : null;
        $this->_tool = (int)$record->tool ? Item::FromID($record->tool) : null;
        $this->_min_time = (int)$record->min_time;
        $this->_max_time = (int)$record->max_time;
        $this->_exp = (int)$record->exp;
        $this->_spirit = (int)$record->spirit;
        $this->_model = $record->model;
        $this->_quantities = array((int)$record->q1 => (float)$record->q1_prob);
        if((int)$record->q2 != 0 && (float)$record->q2_prob != 0) {
            $this->_quantities[(int)$record->q2] = (float)$record->q2_prob;
        }
        $this->_quest = (int)$record->quest ? (int)$record->quest : null;
        $this->_uninterruptible = (bool)((int)$record->uninterruptible);
        $this->_permanent = (bool)((int)$record->permanent);
    }

    public function serialize() {
        return igbinary_serialize(array(
            $this->_id, $this->_type, $this->_name, $this->_grade, $this->_level, $this->_tool, $this->_min_time, $this->_max_time,
            $this->_exp, $this->_spirit, $this->_model, $this->_quantities, $this->_quest, $this->_uninterruptible, $this->_permanent
        ));
    }

    public function unserialize($data) {
        list($this->_id, $this->_type, $this->_name, $this->_grade, $this->_level, $this->_tool, $this->_min_time, $this->_max_time,
            $this->_exp, $this->_spirit, $this->_model, $this->_quantities, $this->_quest, $this->_uninterruptible, $this->_permanent) = igbinary_unserialize($data);
        $this->_kind = 'resource';
    }

    public function render_tooltip() {
        $tip = "<div class='item_tooltip'>";
        $tip .= "<p class='resource-title pw_color_0'>{$this->_name} <span class='tooltip-resource'>(resource)</span></p>";
        $tip .= $this->render_tooltip_location();
        if($this->_level || $this->_tool) {
            $tip .= "<p>Requires ";
            $requires = array();
            if($this->_level) {
                $requires[] = "level {$this->_level}";
            }
            if($this->_tool) {
                $requires[] = "a" . $this->_tool->link();
            }
            $tip .= implode(' and ', $requires) . "</p>";
        }
        $contents = $this->get_contents();
        if($contents) {
            $tip .= "<p>Yields:</p><ul class='tooltip-set'>";
            foreach($contents as $content) {
                if($content['item']) {
                    $tip .= "<li>".$content['item']->link();
                    if($content['chance'] < 1) {
                        $tip .= " (" . number_format($content['chance'] * 100) . "%)";
                    }
                }
            }
            $tip .= "</ul>";
        }
        $tip .= "</div>";
        return $tip;
    }

    public function get_contents() {
        $results = MySQL::instance()->query("SELECT item, probability FROM resource_items WHERE resource = {$this->_id}", true);
        $items = array();
        while($row = mysql_fetch_object($results)) {
            $items[] = array('item' => Item::FromID($row->item), 'chance' => (float)$row->probability);
        }
        return $items;
    }
}
