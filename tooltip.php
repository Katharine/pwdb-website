<?php
require_once 'utils/utils.php';
header("Content-Type: text/html; charset=utf-8");
$id = (int)$_GET['id'];
$item = null;
switch($_GET['kind']) {
    case 'item':
        $item = Item::FromID($id);
        break;
    case 'mob':
        $item = Mob::FromID($id);
        break;
    case 'resource':
        $item = Resource::FromID($id);
        break;
    case 'pet':
        $item = Pet::FromID($id);
        break;
    case 'npc':
        $item = NPC::FromID($id);
        break;
}
if(!$item) {
    print "<div class='item_tooltip'>Item not found.</div>";
} else {
    $map = $x = $y = null;
    if(isset($_GET['map']) && isset($_GET['x']) && isset($_GET['y'])) {
        print $item->render_tooltip($_GET['map'], $_GET['x'], $_GET['y']);
    } else {
        print $item->render_tooltip();
    }
}
?>
