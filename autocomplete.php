<?php
require_once 'utils/utils.php';
if(!isset($_GET['term'])) {
    die("[]");
}
$link = MySQL::instance();
$db = Humongous::instance();

$query = $link->escape($_GET['term']);
$results = $db->items->find(array('name_prefix' => new MongoRegex("/^".strtolower($_GET['term'])."/")))->sort(array('name' => 1))->limit(10);
$items = array();
foreach($results as $row) {
    $row = (object)$row;
    $items[] = array(
        'id' => $row->id,
        'value' => $row->name,
        'label' => $row->name,
        'icon' => Element::IconURL($row->icon),
        'colour' => Item::GuessColour($row->colour, $row->name),
        'type' => $row->kind
    );
}
$link->query("SELECT id, name FROM mobs WHERE name LIKE '{$query}%' ORDER BY name LIMIT 10");
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
