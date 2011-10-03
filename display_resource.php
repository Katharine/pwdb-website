<?php
require_once 'utils/utils.php';

$template = new Template();
$template->caching = 1;
$id = (int)$_GET['id'];
if($template->isCached('resource.tpl', $id)) {
    $template->display('resource.tpl', $id);
    die();
}
$resource = Resource::FromID($id);
if(!$resource) {
    $template->caching = 0;
    $template->display('404.tpl');
    die();
}
$template->assign('resource', $resource);
$template->assign('spawns', $resource->spawn_points());
$template->display('resource.tpl', $id);
?>
