<?php
class MemoryCache {
    private static $instance;
    public static function instance() {
        if(!self::$instance) {
            self::$instance = new MemoryCache();
        }
        return self::$instance;
    }

    private $memcache, $gets, $sets, $hits, $missed_keys;

    public function __construct() {
        $this->memcache = new Memcache();
        $this->memcache->addServer('localhost', 11211);
        $this->missed_keys = array();
    }

    public function get($var) {
        if(is_array($var)) {
            $this->gets += count($var);
        } else {
            ++$this->gets;
        }
        $ret = $this->memcache->get($var);
        if($ret !== false) {
            if(is_array($ret)) {
                $this->hits += count($ret);
                if(count($ret) != count($var)) {
                    foreach($var as $k) {
                        if(!isset($ret[$k])) {
                            $this->missed_keys[] = $k;
                        }
                    }
                }
            } else {
                ++$this->hits;
            }
        } else {
            if(is_array($var)) {
                $this->missed_keys = array_merge($this->missed_keys, $var);
            } else {
                $this->missed_keys[] = $var;
            }
        }
        return $ret;
    }

    public function set($var, $val) {
        ++$this->sets;
        return $this->memcache->set($var, $val);
    }

    public function get_count() {
        return $this->gets;
    }

    public function set_count() {
        return $this->sets;
    }

    public function hit_count() {
        return $this->hits;
    }

    public function miss_count() {
        return ($this->gets - $this->hits);
    }

    public function missed_keys() {
        return $this->missed_keys;
    }
}
