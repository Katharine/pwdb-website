<?php
class Text {
    public static function ToHTML($element_string) {
        $count = 0;
        $string = $element_string;
        $string = preg_replace("/\\^([0-9a-fA-F]{6,6})/", "</span><span style='color:#\\1'>", trim($string), -1, $count);
        if($count > 0) {
            $string = preg_replace('~</span>~', '', $string, 1) . '</span>';
        }
        if(trim(strip_tags($string)) == '') {
            return '';
        }
        return nl2br($string);
    }
}
