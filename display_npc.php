<?php
require_once 'utils/utils.php';

$template = new Template();
$template->caching = 0;
$id = (int)$_GET['id'];
if($template->isCached('npc.tpl', $id)) {
    $template->display('npc.tpl', $id);
    die();
}
$npc = NPC::FromID($id);
if(!$npc) {
    $template->display('404.tpl');
    die();
}
/*$same_model = array();
$link = MySQL::instance();

$model_parts = explode('\\', $mob->model);
array_pop($model_parts);
$real_model = array_pop($model_parts);
// [^a-z] avoids the prefix "boss" while keeping the intended effect (hooray Chinese).
if(preg_match('/^[abc][^a-z]|身高/', $real_model)) {
    $real_model = array_pop($model_parts);
}

// "\\\\\\\" -- (PHP) --> "\\\\" --- (MySQL) ---> "\\" --- (LIKE) ---> "\"
$link->query("SELECT * FROM npcs WHERE id != {$id} AND name != '' AND model LIKE '%\\\\\\\\".$link->escape($real_model)."\\\\\\\\%'");
while($row = $link->fetchrow()) {
    $same_model[] = array('mob' =>new Mob($row), 'translated' => Translate::TranslatePath($row->model));
}

$template->assign('same_model', $same_model);
*/

$template->assign('npc', $npc);
$template->assign('services', NPCService::ForNPC($npc->id));
$template->assign('spawns', $npc->spawn_points());
$template->display('npc.tpl', $id);
?>
