<?php
require_once 'utils/utils.php';

$template = new Template();
$template->caching = 1;
$id = (int)$_GET['id'];
if($template->isCached('mob.tpl', $id)) {
    $template->display('mob.tpl', $id);
    die();
}
$mob = Mob::FromID($id);
if(!$mob) {
    $template->display('404.tpl');
    die();
}
$drops = $mob->drops();
$dq_sell = 0;
foreach($drops as $drop) {
    $dq_sell += $mob->real_drop_rate($drop['rate']) * $drop['item']->dq_sell;
}
$same_model = array();
$link = MySQL::instance();

$model_parts = explode('\\', $mob->model);
array_pop($model_parts);
$real_model = array_pop($model_parts);
// [^a-z] avoids the prefix "boss" while keeping the intended effect (hooray Chinese).
if(preg_match('/^[abc][^a-z]|身高/', $real_model)) {
    $real_model = array_pop($model_parts);
}

// "\\\\\\\" -- (PHP) --> "\\\\" --- (MySQL) ---> "\\" --- (LIKE) ---> "\"
$link->query("SELECT * FROM mobs WHERE id != {$id} AND name != '' AND model LIKE '%\\\\\\\\".$link->escape($real_model)."\\\\\\\\%'");
while($row = $link->fetchrow()) {
    $same_model[] = array('mob' =>new Mob($row), 'translated' => Translate::TranslatePath($row->model));
}

$template->assign('dq_sell', $dq_sell);
$template->assign('mob', $mob);
$template->assign('drops', $drops);
$template->assign('same_model', $same_model);
$template->display('mob.tpl', $id);
?>
