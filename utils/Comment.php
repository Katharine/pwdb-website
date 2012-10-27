<?php
require_once 'utils.php';
class Comment {
    public static function Create($id, $kind, $name, $comment, $spam = false) {
        $link = MySQL::instance();
        $id = (int)$id;
        $kind = $link->escape($kind);
        $name = $link->escape($name);
        $comment = $link->escape($comment);
        $spam = (int)$spam;
        $link->query("INSERT INTO comments (object_id, object_kind, commenter_name, comment, spam, time) VALUES (
            {$id}, '{$kind}', '{$name}', '{$comment}', {$spam}, NOW())");
        $comment_id = $link->id();
        $template = new Template();
        $template->clearCache("{$kind}.tpl", $id);
        return $comment_id;
    }

    public static function FetchComments($id, $kind, $spam = false) {
        $link = MySQL::instance();
        $id = (int)$id;
        $kind = $link->escape($kind);
        $spam = (int)$spam;
        $link->query("SELECT * FROM comments WHERE object_id = {$id} AND object_kind = '{$kind}' AND spam = {$spam}");
        $comments = array();
        while($row = $link->fetchrow()) {
            $comments[] = new Comment($row);
        }
        return $comments;
    }

    public $object_id, $object_kind, $commenter, $comment, $spam, $date;

    public function __construct($record) {
        $this->object_id = (int)$record->object_id;
        $this->object_kind = $record->object_kind;
        $this->commenter = $record->commenter_name;
        $this->comment = $record->comment;
        $this->spam = (bool)$record->spam;
        $this->date = strtotime($record->time);
    }
}
