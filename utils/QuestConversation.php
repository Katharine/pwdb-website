<?php
require_once 'utils.php';
class QuestConversation {
    public $prompt, $text, $dialogs;

    public function __construct($conversation) {
        $this->prompt = $conversation['prompt'];
        $this->text = $conversation['text'];
        $this->dialogs = $conversation['dialogs'];
    }
}
