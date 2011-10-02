<?php
require_once 'utils/utils.php';

$query = $_REQUEST['q'];
$tokens = array();
$valid_tokens = array('level', 'grade', 'pdef', 'mdef', 'patk', 'matk', 'reputation', 'stack', 'constant', 'durability', 'buy', 'sell', 'str', 'vit', 'dex', 'mag', 'mana', 'hp', 'range', 'class', 'type');

$words = explode(' ', $query);
foreach($words as $number => $word) {
    $token = strtolower(strstr($word, ':', true));
    if(!$token) {
        continue;
    }
    if(!in_array($token, $valid_tokens)) {
        continue;
    }
    $content = substr(strstr($word, ':'), 1);
    if(empty($content)) {
        return;
    }
    if(!isset($tokens[$token])) {
        $tokens[$token] = array();
    }
    $tokens[$token][] = $content;
    unset($words[$number]);
}
$link = MySQL::instance();
$query = $link->escape(implode(' ', $words));

$queries = array(
    'armour' => "SELECT * FROM armor WHERE level <= 105 AND name != '' ",
    'ornaments' => "SELECT * FROM ornaments WHERE level <= 105 AND name != '' ",
    'weapons' => "SELECT * FROM weapons WHERE level <= 105 AND name != '' ",
    'items' => "SELECT * FROM generic_items WHERE name != '' ",
    'tomes' => "SELECT * FROM tomes WHERE name != '' ",
    'mobs' => "SELECT * FROM mobs WHERE name != '' ",
);

if(!empty($query)) {
    foreach($queries as &$q) {
        $q .= "AND MATCH(name) AGAINST ('{$query}') ";
    }
}

function value_to_constraint($value) {
    if(substr($value, 0, 1) == '~') {
        $value = (float)substr($value, 1);
        $min = ($value * 0.95);
        $max = ($value * 1.05);
        return "%1\$s BETWEEN {$min} AND {$max}";
    }
    if(substr($value, -1) == '+') {
        return "%1\$s >= ".(float)$value;
    }
    if(substr($value, -1) == '-') {
        return "%1\$s <= ".(float)$value;
    }
    if(strstr($value, '-')) {
        $value = explode('-', $value);
        $min = (float)$value[0];
        $max = (float)$value[1];
        return "%1\$s BETWEEN {$min} AND {$max}";
    }
    if(strstr($value, ',')) {
        $values = explode(',', $value);
        foreach($values as &$value) {
            $value = '"' . MySQL::instance()->escape($value) . '"';
        }
        return "%1\$s IN (" . implode(',', $values) . ")";
    }
    return "%1\$s = '" . MySQL::instance()->escape($value) . "'";
}

function add_constraints($constraints, $field, $handle_multiple, &$query) {
    $query .= ' AND (' . sprintf(implode(" {$handle_multiple} ", $constraints), "`{$field}`") . ')';
}

$no_matches = array();

foreach($tokens as $token => $values) {
    foreach($values as &$value) {
        $value = value_to_constraint($value);
    }
    switch($token) {
        case 'level':
            add_constraints($values, 'level', 'OR', $queries['armour']);
            add_constraints($values, 'level', 'OR', $queries['weapons']);
            add_constraints($values, 'level', 'OR', $queries['ornaments']);
            add_constraints($values, 'level', 'OR', $queries['mobs']);
            $no_matches[] = 'items';
            $no_matches[] = 'tomes';
            break;
        case 'grade':
            add_constraints($values, 'grade', 'OR', $queries['armour']);
            add_constraints($values, 'grade', 'OR', $queries['weapons']);
            add_constraints($values, 'grade', 'OR', $queries['ornaments']);
            add_constraints($values, 'grade', 'OR', $queries['items']);
            $no_matches[] = 'tomes';
            $no_matches[] = 'mobs';
            break;
        case 'pdef':
            add_constraints($values, 'pdef', 'OR', $queries['armour']);
            add_constraints($values, 'pdef', 'OR', $queries['ornaments']);
            add_constraints($values, 'phys_def', 'OR', $queries['mobs']);
            $no_matches[] = 'weapons';
            $no_matches[] = 'items';
            $no_matches[] = 'tomes';
            break;
        case 'mdef':
            add_constraints($values, 'mdef', 'OR', $queries['armour']);
            add_constraints($values, 'mdef', 'OR', $queries['ornaments']);
            $no_matches[] = 'weapons';
            $no_matches[] = 'items';
            $no_matches[] = 'tomes';
            $no_matches[] = 'mobs';
            break;
        case 'patk':
            add_constraints($values, 'patk', 'OR', $queries['weapons']);
            add_constraints($values, 'patk', 'OR', $queries['ornaments']);
            $no_matches[] = 'armour';
            $no_matches[] = 'items';
            $no_matches[] = 'tomes';
            $no_matches[] = 'mobs';
            break;
        case 'matk':
            add_constraints($values, 'matk', 'OR', $queries['weapons']);
            add_constraints($values, 'matk', 'OR', $queries['ornaments']);
            $no_matches[] = 'armour';
            $no_matches[] = 'items';
            $no_matches[] = 'tomes';
            $no_matches[] = 'mobs';
            break;
        case 'reputation':
            add_constraints($values, 'reputation', 'OR', $queries['armour']);
            add_constraints($values, 'reputation', 'OR', $queries['weapons']);
            add_constraints($values, 'reputation', 'OR', $queries['ornaments']);
            $no_matches[] = 'items';
            $no_matches[] = 'tomes';
            $no_matches[] = 'mobs';
            break;
        case 'stack':
            add_constraints($values, 'stack_count', 'OR', $queries['armour']);
            add_constraints($values, 'stack_count', 'OR', $queries['weapons']);
            add_constraints($values, 'stack_count', 'OR', $queries['ornaments']);
            add_constraints($values, 'stack_count', 'OR', $queries['items']);
            add_constraints($values, 'stack_count', 'OR', $queries['tomes']);
            $no_matches[] = 'mobs';
            break;
        case 'durability':
            add_constraints($values, 'durability', 'OR', $queries['armour']);
            add_constraints($values, 'durability', 'OR', $queries['weapons']);
            add_constraints($values, 'durability', 'OR', $queries['ornaments']);
            $no_matches[] = 'items';
            $no_matches[] = 'tomes';
            $no_matches[] = 'mobs';
            break;
        case 'mana':
            add_constraints($values, 'mana', 'OR', $queries['armour']);
            $no_matches[] = 'weapons';
            $no_matches[] = 'ornaments';
            $no_matches[] = 'items';
            $no_matches[] = 'tomes';
            $no_matches[] = 'mobs';
            break;
        case 'type':
            add_constraints($values, 'type', 'OR', $queries['items']);
            $no_matches[] = 'weapons';
            $no_matches[] = 'ornaments';
            $no_matches[] = 'armour';
            $no_matches[] = 'tomes';
            $no_matches[] = 'mobs';
            break;
    }
}

$weapons = array();
$ornaments = array();
$armour = array();
$items = array();
$tomes = array();
$mobs = array();
$total_results = 0;

if(!in_array('weapons', $no_matches)) {
    $link->query($queries['weapons'] . ' LIMIT 500');
    while($row = $link->fetchrow()) {
        $weapons[] = new Weapon($row);
        ++$total_results;
    }
}
if(!in_array('ornaments', $no_matches)) {
    $link->query($queries['ornaments']. ' LIMIT 500');
    while($row = $link->fetchrow()) {
        $ornaments[] = new Ornament($row);
        ++$total_results;
    }
}
if(!in_array('armour', $no_matches)) {
    $link->query($queries['armour'] . ' LIMIT 500');
    while($row = $link->fetchrow()) {
        $armour[] = new Armour($row);
        ++$total_results;
    }
}
if(!in_array('items', $no_matches)) {
    $link->query($queries['items']. ' LIMIT 500');
    while($row = $link->fetchrow()) {
        $items[] = new Item($row);
        ++$total_results;
    }
}
if(!in_array('tomes', $no_matches)) {
    $link->query($queries['tomes']. ' LIMIT 500');
    while($row = $link->fetchrow()) {
        $tomes[] = new Tome($row);
        ++$total_results;
    }
}
if(!in_array('mobs', $no_matches)) {
    $link->query($queries['mobs']. ' LIMIT 500');
    while($row = $link->fetchrow()) {
        $mobs[] = new Mob($row);
        ++$total_results;
    }
}

$template = new Template();
$template->assign('weapons', $weapons);
$template->assign('ornaments', $ornaments);
$template->assign('armour', $armour);
$template->assign('items', $items);
$template->assign('tomes', $tomes);
$template->assign('mobs', $mobs);
$template->assign('result_count', $total_results);
$template->assign('search', $_REQUEST['q']);
$template->display('search_results.tpl');
?>
