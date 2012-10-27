<?php
require_once 'utils/utils.php';

$template = new Template();
$id = (int)$_GET['id'];
$template->caching = 0;
if($template->isCached('item/generic.tpl', (string)$id)) {
    $template->display('item/generic.tpl', (string)$id);
    die();
} else if($template->isCached('item/equipment.tpl', (string)$id)) {
    $template->display('item/equipment.tpl', (string)$id);
    die();
}
$item = Item::FromID($_GET['id']);
if(!$item) {
    $template->display('404.tpl');
    die();
}
$created_by = Recipe::CreatesItem($id);
$used_for = Recipe::UsesItem($id);
// Include the thing we decompose to in our uses, but not if it's a chi stone.
if($item->decompose_to && $item->decompose_to->type != 'chi') {
    $used_for = array_merge($used_for, Recipe::UsesItem($item->decompose_to->id));
}
$reforges = array();
$reforged_by = null;
// Reforges are special!
foreach($used_for as $key => $recipe) {
    if($recipe->inputs && $recipe->outputs) {
        foreach($recipe->inputs as $input) {
            foreach($recipe->outputs as $output) {
                if($input['item']->id == $output['item']->id) {
                    if($output['item']->id != $id)
                        $reforges[] = $recipe;
                    else
                    unset($used_for[$key]);
                } else if($output['item']->id == $id) {
                    $reforged_by = array('quantity' => $input['quantity'], 'item' => $input['item']);
                }
            }
        }
    }
}

foreach($created_by as $key => $recipe) {
    if($recipe->inputs && $recipe->outputs) {
        foreach($recipe->outputs as $output) {
            foreach($recipe->inputs as $input) {
                if($input['item']->id == $output['item']->id) {
                    unset($created_by[$key]);
                }
            }
        }
    }
}

$link = MySQL::instance();
$mongo = Humongous::instance();

function do_query($field, $model, $id) {
    global $mongo;
    $querypart = array();
    if(!is_array($field)) {
        $querypart[$field] = $model;
    } else {
        $querypart['$or'] = array();
        foreach($field as $f) {
            $querypart['$or'][] = array($f => $model);
        }
    }

    $querypart['name'] = array('$ne' => '');
    $querypart['id'] = array('$ne' => $id);
    $records = $mongo->items->find($querypart);
    $same_model = array();
    foreach($records as $record) {
        $record = (object)$record;
        $same_model[] = array('item' => Item::FromRecord($record), 'model' => array($model), 'translated' => array(Translate::TranslatePath($model)));
    }
    return $same_model;
}

$same_model = array();
if(!empty($item->model)) {
    $same_model = do_query('model', $item->model, $id);
} else if(!empty($item->left_model)) {
    if(!empty($item->right_model))
        $same_model = do_query(array('model_right', 'model_left'), $item->left_model, $id);
    else
        $same_model = do_query('model_left', $item->left_model, $id);
} else if(!empty($item->right_model)) {
    $same_model = do_query('model_right', $item->right_model, $id);
}

$same_icon = array();
$icon_parts = explode('\\', $item->icon);
$actual_icon = array_pop($icon_parts);
if(!empty($actual_icon)) {
    $records = $mongo->items->find(array('real_icon' => $actual_icon, 'id' => array('$ne' => $id), 'name_prefix' => array('$ne' => '')));
    foreach($records as $record) {
        $record = (object)$record;
        $same_icon[] = array('item' => Item::FromRecord($record), 'icon' => $record->icon, 'translated' => Translate::TranslatePath($record->icon));
    }
}

$drops_from = Mob::DropsItem($id);

$farmed_from = array();
$link->query("SELECT spawn, map, x, y, z FROM spawn_points, resource_items WHERE resource_items.resource = spawn_points.spawn AND resource_items.item = {$id}");
while($row = $link->fetchrow()) {
    if(!isset($farmed_from[$row->map])) {
        $farmed_from[$row->map] = new Map($row->map);
    }
    $farmed_from[$row->map]->add_point($row->x, $row->y, $row->z, 'resource', $row->spawn);
}

$contents = array();
if($item instanceof QuestTrigger) {
    $contents = $item->contents();
}

$template->assign('item', $item);
$template->assign('created_by', $created_by);
$template->assign('used_for', $used_for);
$template->assign('used_to_reforge', $reforges);
$template->assign('reforged_using', $reforged_by);
$template->assign('decomposed_from', $item->decomposes_from());
$template->assign('sold_by', $item->sold_by());
$template->assign('dropped_from', $drops_from);
$template->assign('same_model', $same_model);
$template->assign('same_icon', $same_icon);
$template->assign('farmed_from', $farmed_from);
$template->assign('from_quests', Quest::HasReward($id));
$template->assign('for_quests', Quest::RequiringItem($id));
$template->assign('contents', $contents);
if($item instanceof Equipment) {
    $template->assign('comments', Comment::FetchComments($id, 'item/equipment'));
    $template->assign('comment_id', $id);
    $template->assign('comment_class', 'item/equipment');

    $template->assign('children', $item->find_children());
    $template->assign('parents', $item->find_parents());
    $template->display('item/equipment.tpl', $id); // Equipment
} else {
    $template->assign('comments', Comment::FetchComments($id, 'item/generic'));
    $template->assign('comment_id', $id);
    $template->assign('comment_class', 'item/generic');

    $template->assign('children', null);
    $template->assign('parents', null);
    $template->display('item/generic.tpl', $id); // Generic
}
?>
