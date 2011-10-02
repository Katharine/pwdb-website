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
}
if(!$item) {
    print "<div class='item_tooltip'>Item not found.</div>";
} else {
    print $item->render_tooltip();
}
?>
