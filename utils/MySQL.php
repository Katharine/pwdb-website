<?php
class MySQL
{
    const DATABASE_ADDRESS = 'localhost';
    const USER_NAME = 'pwdb';
    const USER_PASSWORD = '85_CE3oAN*<ct4[pslaX';
    const DATABASE_NAME = 'pwdb';
    private $link = NULL;
    private $queries = 0;
    private $query_log = array();
    private $server;
    private $user;
    private $pass;
    private $db;
    private $results;
    private $numrows = 0;
    private $cloned = false;
    private $time = 0.0;
    private static $instance;
    
    public function __construct($server = self::DATABASE_ADDRESS, $user = self::USER_NAME, $pass = self::USER_PASSWORD, $db = self::DATABASE_NAME)
    {
        try
        {
            $this->connect($server, $user, $pass);
            $this->setdb($db);
        }
        catch(Exception $e)
        {
            throw new Exception("Caught exception in ".__CLASS__." constructor: ".$e->getMessage());
        }
        return true;
    }

    public function __destruct()
    {
        $this->disconnect();
    }
    
    public function __clone()
    {
        $this->cloned = true;
    }

    public static function instance() {
        if(!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }
        
    public function connect($server, $user, $pass)
    {
        $now = microtime(true);
        $link = @mysql_connect($server, $user, $pass, true);
        if(!is_resource($link))
        {
            throw new Exception("Could not connect to database at '{$server}'");
            return false;
        }
        mysql_set_charset('utf8', $link); 
        $this->time += microtime(true) - $now;
        $this->link = $link;
    }

    public function escape($string)
    {
        $now = microtime(true);
        return mysql_real_escape_string($string, $this->link);
        $this->time += microtime(true) - $now;
    }
    
    public function numrows()
    {
        return $this->numrows;
    }

    public function setdb($db)
    {
        $now = microtime(true);
        if(!@mysql_select_db($db, $this->link))
        {
            throw new Exception("Could not set database to {$db}");
            return false;
        }
        $this->time += microtime(true) - $now;
        return true;
    }

    public function query($query, $return = false)
    {
        $now = microtime(true);
        $results = @mysql_query($query, $this->link);
        //$this->query_log[] = array('query' => $query, 'time' => microtime(true) - $now);
        if(mysql_errno($this->link) != 0)
        {
            throw new Exception(mysql_error($this->link));
            trigger_error("Error #".mysql_errno($this->link)." running MySQL query: ".htmlentities(mysql_error($this->link))."\n<br />Query: ".htmlentities($query), E_USER_WARNING);
            return false;
        }
        ++$this->queries;
        $query = trim($query);
        $command = strtoupper(substr($query,0,strpos($query,' ')));
        if($command == 'SELECT')
        {
            if($return)
            {
                $this->time += microtime(true) - $now;
                return $results;
            }
            else
            {
                $this->results = $results;
                $this->numrows = mysql_num_rows($results);
                $this->time += microtime(true) - $now;
            }
        }
        else
        {
            if(!$return)
            {
                $this->numrows = mysql_affected_rows($this->link);
            }
            $this->time += microtime(true) - $now;
            return true;
        }
    }

    public function fetchrow()
    {
        $now = microtime(true);
        $return = @mysql_fetch_object($this->results);
        $this->time += microtime(true) - $now;
        return $return;
    }

    public function result($row, $column)
    {
        return @mysql_result($this->results,$row,$column); // or throw new Exception("Error fetching result {$row},{$column}.");
    }

    public function id()
    {
        return mysql_insert_id($this->link);
    }

    public function disconnect()
    {
        if($this->cloned) return;
        mysql_close($this->link);
    }

    public function query_count() {
        return $this->queries;
    }

    public function query_time() {
        return $this->time;
    }

    public function log() {
        return $this->query_log;
    }
}
