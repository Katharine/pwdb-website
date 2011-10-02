<?php
require_once 'utils.php';
class Translate {
    const GOOGLE_API_KEY = "AIzaSyAq8nK8h0B8bBTqe4guwx1PLhCR5diwvSw"; // This key only works from 66.228.39.46.
    private static $cache = array(
        'Surfaces' => 'Surfaces',
        'surface' => 'surface',
        'Models' => 'Models',
        'NPCs' => 'NPCs',
        '男物品' => 'Male items',
        '图标' => 'Icon',
        '物品' => 'Goods',
        '怪物' => 'Monster',
    );

    public static function TranslateText($texts, $html = false) {
        $is_array = true;
        if(!is_array($texts)) {
            $texts = array($texts);
            $is_array = false;
        }
        $link = MySQL::instance();
        $returns = array();
        $to_translate = array();

        // Check what we have in memcache, if anything.
        $for_memcache = array();
        foreach($texts as $text) {
            if(isset(self::$cache[$text])) {
                continue;
            }
            if(strlen($text) > 240) { // That's bytes, not characters.
                continue;
            }
            $for_memcache[] = "t-{$text}";
        }
        $results = MemoryCache::instance()->get($for_memcache);
        if($results) {
            foreach($results as $text => $translation) {
                self::$cache[substr($text,2)] = $translation;
            }
        }
        $to_memcache = array();
        foreach($texts as $text) {
            if($text == '') {
                $returns[] = $text;
            } else if(is_numeric($text)) {
                $returns[] = $text;
            } else if(isset(self::$cache[$text])) {
                $returns[] = self::$cache[$text];
            } else {
                $row = mysql_fetch_object($link->query("SELECT translation FROM translations WHERE original = '" . $link->escape($text) . "'", true));
                if($row) {
                    $returns[] = $row->translation;
                    if(strlen($text) <= 240)
                        $to_memcache['t-'.$text] = $row->translation;
                    self::$cache[$text] = $row->translation;
                } else {
                    $returns[] = null;
                    $to_translate[] = urlencode($text);
                }
            }
        }
        
        if(count($to_translate)) {
            $url = "https://www.googleapis.com/language/translate/v2?key=".self::GOOGLE_API_KEY."&source=zh-CN&target=en&q=";
            $url .= implode('&q=', $to_translate);
            if($html) {
                $url .= '&format=html';
            }
            $response = json_decode(file_get_contents($url));
            if($response) {
                $translations = $response->data->translations;
                $tpointer = 0;
                $opointer = 0;
                foreach($returns as &$return) {
                    if($return === null) {
                        $return = $translations[$tpointer++]->translatedText;
                        self::$cache[$texts[$opointer]] = $return;
                        if(strlen($return) <= 240) {
                            $to_memcache['t-'.$texts[$opointer]] = $return;
                        }
                        $link->query("INSERT IGNORE INTO translations (original, translation) VALUES ('" . $link->escape($texts[$opointer]) . "', '" . $link->escape($return) . "')");
                    }
                    ++$opointer;
                }
            }
        }

        $memcache = MemoryCache::instance();
        foreach($to_memcache as $key => $val) {
            $memcache->set($key, $val);
        }

        if($is_array) {
            return $returns;
        } else {
            return $returns[0];
        }
    }

    public static function TranslatePath($path) {
        $texts = explode('\\', $path);
        $last = explode('.', array_pop($texts));
        $ext = null;
        if(count($last) > 1) {
            $ext = array_pop($last);
        }
        $texts[] = implode('.', $last);
        $translated = self::TranslateText($texts);
        $translated_path = implode('\\', $translated);
        if($ext) {
            $translated_path .= ".{$ext}";
        }
        return $translated_path;
    }

    public static function TranslateField($name, $formatted=false) {
        if(strpos($name, 'N/A') === false) {
            return null;
        } else {
            return self::TranslateText(str_replace('N/A', '', $name), $formatted);
        }
    }
}
