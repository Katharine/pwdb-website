<?php
require_once 'utils/utils.php';
$url = current(explode('/', $_POST['kind'])) . "/{$_POST['id']}";
$akismet = new Akismet('pwdb.kathar.in', '2e98a7fd3f4c');
$akismet->setCommentAuthor($_POST['name']);
$akismet->setCommentContent($_POST['comment']);
$akismet->setPermalink("http://pwdb.kathar.in/{$url}");
$spam = $akismet->isCommentSpam();
$comment = Comment::Create($_POST['id'], $_POST['kind'], $_POST['name'], $_POST['comment'], $spam);
if($spam) {
    header("Location: {$url}#nocomment");
} else {
    header("Location: {$url}#more-comments");
}
?>
