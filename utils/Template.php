<?php
require_once 'Smarty/Smarty.class.php';
class Template extends Smarty {
    function __construct() {
        parent::__construct();

        $root = dirname(__FILE__) . '/../smarty/';
        $this->template_dir = $root . 'templates';
        $this->compile_dir = $root . 'templates_c';
        $this->config_dir = $root . 'configs';
        $this->cache_dir = $root . 'cache';
        $this->cache_lifetime = -1;
    }

    function display($template, $cache_id = null, $compile_id = null, $parent = null) {
        header("Content-Type: text/html;charset=utf-8");
        parent::display($template, $cache_id, $compile_id, $parent);
    }
}

function format_time_interval($interval) {
    $days = (int)($interval / 86400);
    $interval -= $days * 86400;
    $hours = (int)($interval / 3600);
    $interval -= $hours * 3600;
    $minutes = (int)($interval / 60);
    $interval -= $minutes * 60;
    $seconds = $interval;
    $parts = array();
    if($days > 0) {
        $parts[] = "{$days} day" . ($days != 1 ? 's' : '');
    }
    if($hours > 0) {
        $parts[] = "{$hours} hour" . ($hours != 1 ? 's' : '');
    }
    if($minutes > 0) {
        $parts[] = "{$minutes} minute" . ($minutes != 1 ? 's' : '');
    }
    if($seconds > 0) {
        $parts[] = "{$seconds} second" . ($seconds != 1 ? 's' : '');
    }
    $last = array_pop($parts);
    $string = implode(', ', $parts);
    if(count($parts) > 0) {
        $string .= ' and ';
    }
    $string .= $last;
    return $string;
}
