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
