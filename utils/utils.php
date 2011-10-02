<?php
define('PW_ICON_URL', '/images/pwi/icons/%1$s/%2$s');
$GLOBALS['loaded'] = array();
// Automatically includes any class requested.
function autoload($class) {
    if(file_exists(dirname(__FILE__)."/{$class}.php")) {
        require_once dirname(__FILE__)."/{$class}.php";
        $GLOBALS['loaded'][] = $class;
    }
}

spl_autoload_register('autoload');
