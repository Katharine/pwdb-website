<?php
require_once 'utils/utils.php';

$template = new Template();
$id = (int)$_GET['id'];
$template->caching = 1;
if($template->isCached('items/generic.tpl', (string)$id)) {
    $template->display('items/generic.tpl', (string)$id);
    die();
} else if($template->isCached('items/equipment.tpl', (string)$id)) {
    $template->display('items/equipment.tpl', (string)$id);
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

$same_model = array();
if(!empty($item->model)) {
    $link->query("SELECT * FROM armor WHERE id != {$id} AND name != '' AND model = '".$link->escape($item->model)."'");
    while($row = $link->fetchrow()) {
        $same_model[] = array('item' => new Armor($row), 'model' => array($item->model), 'translated' => array(Translate::TranslatePath($item->model)));
    }
    $link->query("SELECT * FROM ornaments WHERE id != {$id} AND name != '' AND model = '".$link->escape($item->model)."'");
    while($row = $link->fetchrow()) {
        $same_model[] = array('item' => new Ornament($row), 'model' => array($item->model), 'translated' => array(Translate::TranslatePath($item->model)));
    }
} else if(!empty($item->left_model) || !empty($item->right_model)) {
    $query = "SELECT * FROM weapons WHERE id != {$id} AND name != ''";
    $models = array();
    $translations = array();
    if(!empty($item->left_model)) {
        $query .= " AND model_left = '".$link->escape($item->left_model)."'";
        $models[] = $item->left_model;
        $translations[] = Translate::TranslatePath($item->left_model);
    }
    if(!empty($item->right_model)) {
        $query .= " AND model_right = '".$link->escape($item->right_model)."'";
        $models[] = $item->right_model;
        $translations[] = Translate::TranslatePath($item->right_model);
    }
    $link->query($query);
    while($row = $link->fetchrow()) {
        $same_model[] = array('item' => new Weapon($row), 'model' => $models, 'translated' => $translations);
    }
}

$same_icon = array();
$icon_parts = explode('\\', $item->icon);
$actual_icon = array_pop($icon_parts);
// Don't do this if actual_icon is empty or we try and load every extant item and run out of memory.
if(!empty($actual_icon)) {
    $link->query("SELECT id, icon FROM items WHERE id != {$id} AND name != '' AND icon LIKE '%\\\\\\\\".$link->escape($actual_icon)."'");
    while($row = $link->fetchrow()) {
        $identical = Item::FromID($row->id);
        if($identical) {
            $same_icon[] = array('item' => $identical, 'icon' => $row->icon, 'translated' => Translate::TranslatePath($row->icon));
        }
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


$template->assign('item', $item);
$template->assign('created_by', $created_by);
$template->assign('used_for', $used_for);
$template->assign('used_to_reforge', $reforges);
$template->assign('reforged_using', $reforged_by);
$template->assign('decomposed_from', $item->decomposes_from());
$template->assign('dropped_from', $drops_from);
$template->assign('same_model', $same_model);
$template->assign('same_icon', $same_icon);
$template->assign('farmed_from', $farmed_from);
if($item instanceof Equipment) {
    $template->assign('children', $item->find_children());
    $template->assign('parents', $item->find_parents());
    $template->display('items/equipment.tpl', $id); // Equipment
} else {
    $template->assign('children', null);
    $template->assign('parents', null);
    $template->display('items/generic.tpl', $id); // Generic!
}
?>
