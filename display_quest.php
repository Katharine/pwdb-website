<?php
require_once 'utils/utils.php';

$id = (int)$_GET['id'];
$template = new Template();
$template->caching = 1;
if($template->isCached('quest.tpl', $id)) {
    $template->display('quest.tpl', $id);
    die();
}
$quest = Quest::FromID($id);
if(!$quest) {
    $template->display('404.tpl');
    die();
}

function get_root_quest($quest) {
    if($quest->parent) {
        return get_root_quest(Quest::FromID($quest->parent));
    }
    return $quest;
}

$template->assign('quest', $quest);
$template->assign('root', get_root_quest($quest));
$template->display('quest.tpl', $id);
?>
