<?php
require_once 'utils/utils.php';

$id = (int)$_GET['id'];
$template = new Template();
$template->caching = 1;
if($template->isCached('pet.tpl', $id)) {
    $template->display('pet.tpl', $id);
    die();
}
$pet = Pet::FromID($id);
if(!$pet) {
    $template->display('404.tpl');
    die();
}

$interesting_levels = array(1, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 101, 102, 103, 104, 105);

$skills = $pet->initial_skills();
$egg = $pet->egg();

$template->assign('pet', $pet);
$template->assign('stat_levels', $interesting_levels);
$template->assign('skills', $skills);
$template->assign('egg', $egg);
$template->display('pet.tpl', $id);
?>