<?php
require_once 'utils/utils.php';
$link = MySQL::instance();
if(!isset($_GET['term'])) {
    die("[]");
}

$query = $link->escape($_GET['term']);
$link->query("SELECT id, colour, name, icon, type FROM items WHERE name LIKE '%{$query}%' ORDER BY name LIMIT 10");
$items = array();
while($row = $link->fetchrow()) {
    $items[] = array(
        'id' => $row->id,
        'value' => $row->name,
        'label' => $row->name,
        'icon' => Element::IconURL($row->icon),
        'colour' => Item::GuessColour($row->colour, $row->name),
        'type' => $row->type
    );
}
$link->query("SELECT id, name FROM mobs WHERE name LIKE '%{$query}%' ORDER BY name LIMIT 10");
while($row = $link->fetchrow()) {
    $items[] = array(
        'id' => $row->id,
        'value' => $row->name,
        'label' => $row->name,
        'icon' => '',
        'colour' => 0,
        'type' => 'mob'
    );
}
print json_encode($items);
?>
