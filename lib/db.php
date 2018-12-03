<?php
// -----------------------------------------------------------------------------
// exception supressing
if(function_exists('register_supressing_file'))
    register_supressing_file(__FILE__);

// -----------------------------------------------------------------------------
// get active database obj
function db($id=null) {
    return database_manager()->get_db($id);
}

// -----------------------------------------------------------------------------
// Record class
class Record implements ArrayAccess, Countable {
    // slow...
    protected function get_field_name($idx) {
        $reflect = new ReflectionObject($this);
        $prop = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        if(!array_key_exists($idx, $prop)) return null;
        return $prop[$idx]->name;
    }

    function __toString() {
        return '<pre>'.print_r($this, true).'</pre>';
    }

    // ArrayAccess
    function offsetExists($k) {
        if (!is_int($k)) return isset($this->$k);
        else {
            $key = $this->get_field_name($k);
            return ($key===null) ? false : isset($this->$key); 
        }
    }
    function offsetUnset($k) {
        if (!is_int($k)) unset($this->$k);
        else {
            $key = $this->get_field_name($k);
            if($key!==null) unset($this->$key);
        }
    }
    function offsetSet($k, $v) {
        if (!is_int($k)) $this->$k = $v;
        else {
            $key = $this->get_field_name($k);
            if($key!==null) $this->$key = $v;
        }
    }
    function offsetGet($k) {
        if (!is_int($k)) return $this->$k;
        else {
            $key = $this->get_field_name($k);
            return ($key===null) ? null : $this->$key;
        }
    }

     // Countable
    public function count() {
        return count(get_object_vars($this));
    }
}

// -----------------------------------------------------------------------------
// Recordset class
class Recordset implements ArrayAccess, Iterator, Countable {
    public $data = array();
    public $pager = null;

    // helper
    function pager() {return $this->pager;}

    // link of sorting
    function sort_url($field_name) {
        if(empty($_REQUEST['d']))
            $qstr =  qstr_replace('d', 1);
        else
            $qstr =  qstr_replace('d', 0);
        return get_current_url_filename() . '?' . qstr_replace('s', $field_name, $qstr);
    }

    function __call($name, $args) {
        if(!function_exists($name))
            throw new Exception('Call to undefined function '.$name.'()');

        array_splice($args, 0, 0, '');
        foreach ($this->data as $rk=>$r) {
            if(is_object($r))
                $r = get_object_vars($r);

            if(is_array($r)) {
                foreach ($r as $k=>$v) {
                    $args[0] = $v;
                    $r[$k] = call_user_func_array($name, $args);
                }
                $this->data[$rk] = $r;
            }
            else {
                $args[0] = $r;
                $this->data[$rk] = call_user_func_array($name, $args);
            }
        }
        return $this;
    }

    function __get($k) {
        $key = key($this->data);
        if($key===null) return null;
        if(isset($this->data[$key]->$k)) return $this->data[$key]->$k;
        return null;
    }
    function __set($k, $v) {
        $key = key($this->data);
        if($key===null) $this->data[] = new Record;
        $key = key($this->data);
        $this->data[$key]->$k = $v;
    }
    function __isset($k) {
        $key = key($this->data);
        if($key===null) return false;
        if(isset($this->data[$key][$k])) return true;
        return false;
    }
    //__unset()
    
    function __toString() {
        return '<pre>'.print_r($this->data, true).'</pre>';
    }

    // ArrayAccess
    function offsetExists($k) {
        if (is_int($k)) return isset($this->data[$k]);
        return isset($this->data[0]->$k);
    }
    function offsetUnset($k) {
        unset($this->data[$k]);
    }
    function offsetSet($k, $v) {
        if (is_null($k)) $this->data[] = $v; 
        else $this->data[$k] = $v;
    }
    function offsetGet($k) {
        if (is_int($k)) {
            if(!isset($this->data[$k])) $this->data[$k] = new Record;
            return $this->data[$k];
        }
        return $this->data[0]->$k;
    }

    // Iterator
    function rewind() {return reset($this->data);}
    function current() {return current($this->data);}
    function key() {return key($this->data);}
    function next() {return next($this->data);}
    function valid() {return key($this->data) !== null;}

    // Countable
    function count() {return count($this->data);}
}

// -----------------------------------------------------------------------------
// pager
class Pager {
    public $page_curr = 0;
    public $page_size = 0;
    public $page_total = 0;
    public $record_total = 0;

    function __toString() {
        return $this->render();
    }

    function render() {
        if ($this->record_total==0)
            return "<div class=\"pager_empty\">沒有符合的資料</div>";

        $record_begin = ($this->page_curr-1)*$this->page_size+1;
        $record_end = $record_begin + $this->page_size-1;
        if ($this->record_total<$record_end)
            $record_end = $this->record_total;

        // get current url
        $url = get_current_url_filename();

        $s = '<div class="pager">';
        if ($this->page_curr>1)
            $s .= '<a href="' . $url . '?' . qstr_replace('p', $this->page_curr-1) . '">« 上一頁</a>';

        $count = 0;
        for ($i=1; $i<=$this->page_total; ++$i) {
            if ($i==$this->page_curr) {
                $s .= '<span class="page_curr">' . $i . '</span>';
                $count = 0;
                continue;
            }

            if ($i>$this->page_curr-3 && $i<$this->page_curr+2) {
                $s .= '<a href="' . $url . '?' . qstr_replace('p', $i) . '">' . $i . '</a>';
                $count = 0;
                continue;
            }

            if ($i<5 || $i>$this->page_total-4) {
                $s .= '<a href="' . $url . '?' . qstr_replace('p', $i) . '">' . $i . '</a>';
                $count = 0;
                continue;
            }

            if (++$count<4)
                $s .= '.';
        }

        if($this->page_curr<$this->page_total)
            $s .= '<a href="' . $url . '?' . qstr_replace('p', $this->page_curr+1) . '">下一頁 »</a>';

        if ($this->page_total>1)
            $s .= ' &nbsp;&nbsp;&nbsp;<a class="page_jumper" href="' . $url . '?' . qstr_replace('p', '__page_jumper') . '">跳頁</a>';

        $s .= '<br><br>總共 <span class="rec_total">' . $this->record_total . '</span> 筆資料, 目前顯示第 <span class="rec_start">' . $record_begin . '</span>~<span class="rec_end">' . $record_end  . '</span> 筆';

        return $s . '</div>';
    }
}

// -----------------------------------------------------------------------------
class PDOExException extends PDOException {
    public function __construct(PDOException $e, $sql, $param) {
        $this->file = $e->getFile();
        $this->line = $e->getLine();
        $this->message = $e->getMessage()."\n";
        $this->message .= $sql."\n[";
        foreach($param as $k=>$v)
            $this->message .= "$k=>".var_export($v, true).", ";
        $this->message .= "]\n";
    }
}

// -----------------------------------------------------------------------------
// db class
class Database {
    public $id;
    public $pdo = null;
    public $logging = false;
    public $log = array();
    public $history = array();
    public $sql = '';
    public $param = array();
    public $order_by = '';
    public $cmd_mode = '';
    public $page = array('enable'=>false, 'size'=>50, 'no'=>1);
    
    function __toString() {
        return '<pre>'.print_r($this->log, true).'</pre>';
    }

    // connect
    function connect($dsn=null, $database=null, $username=null, $password=null, $presistent=false) {
        if ($this->pdo) return $this;

        global $_CONFIG;
        $id = $this->id;
        $dsn = ($dsn) ? $dsn : (isset($_CONFIG[$id]['dsn']) ? $_CONFIG[$id]['dsn'] : 'mysql:host=127.0.0.1');

        if(stripos($dsn, 'sqlite')!==false)
            $this->pdo = new PDO($dsn);
        else {
            $database = ($database) ? $database : $_CONFIG[$id]['database'];
            $username = ($username) ? $username : $_CONFIG[$id]['username'];
            $password = ($password) ? $password : $_CONFIG[$id]['password'];
            $presistent = ($presistent) ? $presistent : $_CONFIG[$id]['presistent'];
            if(substr($dsn, -1)!=';') $dsn .= ';';
            $this->pdo = new PDO($dsn.'dbname='.$database, $username, $password, array(PDO::ATTR_PERSISTENT => $presistent));
        }
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(stripos($dsn, 'mysql')!==false)
            $this->pdo->exec("SET NAMES 'utf8';");
        return $this;
    }

    // close
    function close() {
        $this->pdo = null;
    }

    // set sql
    function sql($sql) {
        $this->sql = $sql; 
        return $this;
    }

    // set param
    function param($k, $v=null) {
        if(is_array($k))
            $this->param = array_merge($this->param, $k);
        else
            $this->param[$k] = $v;
        return $this;
    }

    // exec
    function exec($sql=null, $param=array()) {
        $this->cmd_mode = __function__;
        $this->parse_param(func_get_args());

        // add log
        if ($this->logging) {
            $this->log = array('sql'=>$this->sql, 'param'=>$this->param);
            $this->history[] = $this->log;    
        }
            
        try {            
            $this->connect();
            $ret = $this->pdo->prepare($this->sql)->execute($this->param);
            $this->reset();
            return $ret;
        } catch (Exception $e) {
            throw new PDOExException($e, $this->sql, $this->param);
            //throw $e;
        }
    }

    // query
    function query($sql=null, $param=array()) {
        $this->cmd_mode = __function__;
        $this->parse_param(func_get_args());
        
        if(!empty($this->order_by)) {
            $pos = stripos($this->sql, 'order by');
            if ($pos!==false)
                throw new Exception("Can not support order_by in SQL:\n".$this->sql);

            $this->sql .= ' ORDER BY '. $this->order_by;
        }

        // pging
        if($this->page['enable'] && $this->page['size']>0) {
            $pos = stripos($this->sql, 'limit');
            if ($pos!==false)
                throw new Exception("Can not support pagenation in SQL:\n".$this->sql);

            $pos = stripos($this->sql, 'from');
            if ($pos===false)
                throw new Exception("Can not support pagenation in SQL:\n".$this->sql);
            
            // page no
            $no = $this->page['no'];
            $size = $this->page['size'];

            // record total
            $sql_total = "SELECT COUNT(*) AS 'count' ".substr($this->sql, $pos);
            $total = $this->_query($sql_total, $this->param)->data[0]->count;
            $page_total = ceil($total/$size);

            // offset
            if($no<1) $no = 1;
            if($no>$page_total) $no = $page_total;
            $offset = ($no-1) * $size;
            if($offset<0) $offset = 0;
            
            $rs = $this->_query($this->sql." LIMIT $offset,$size", $this->param);
            $rs->pager = new Pager();
            $rs->pager->record_total = $total;
            $rs->pager->page_total = ceil($total/$size);
            $rs->pager->page_size = $size;
            $rs->pager->page_curr = $no;
        }
        else
            $rs = $this->_query($this->sql, $this->param);

        $this->reset();
        return $rs;
    }

    // inner query
    private function _query($sql=null, $param=array()) {
        // add log
         if ($this->logging) {
            $this->log = array('sql'=>$this->sql, 'param'=>$this->param);
            $this->history[] = $this->log;
        }

        try {
            $this->connect();
            $stm = $this->pdo->prepare($sql);
            $stm->execute($param);
            $rs = new Recordset;
            $rs->data = $stm->fetchAll(PDO::FETCH_CLASS, 'Record');
            return $rs;
        } catch (Exception $e) {
            if(get_class($e)=='PDOException')
                throw new PDOExException($e, $this->sql, $this->param);
            else
                throw $e;
        }
    }

    private function parse_param($args) {
        if(isset($args[0]) && is_array($args[0])) $args = $args[0];
        if(isset($args[0]) && is_string($args[0])) $this->sql = array_shift($args);

        if(isset($args[0]) && is_array($args[0])) {
            $args = $args[0];
            if(isset($args[0]))
                $args = array_fill_keys($args, null);
        }
        foreach($args as $k=>$v) {
            if(!is_int($k))
                $this->param[$k] = $v;
        }
        
        // trim order by
        $pos = stripos($this->sql, 'order by');
        if ($pos!==false) {
            $this->order_by = substr($this->sql, $pos+9);
            $this->sql = substr($this->sql, 0, $pos);
        }
        
        // parse param from sql
        $this->sql = trim($this->sql);
        if($this->cmd_mode=='query' && strpos($this->sql, ' ')===false)
            $this->sql = 'SELECT * FROM '.$this->sql;    
        
        preg_match_all("/:([a-zA-Z0-9_.]+)/", $this->sql, $match);

        // check param in sql
        $handled_param = array();
        foreach($match[1] as $k=>$v) {
            if(isset($args[$k]) && $args[$k]!==null) {
                $this->param[$v] = $args[$k];
                $handled_param[$v] = true;
            }
            else if(isset($args[$v]) && $args[$v]!==null) {
                $this->param[$v] = $args[$v];
                $handled_param[$v] = true;
            }
            else if(isset($_REQUEST[$v])) {
                $this->param[$v] = $_REQUEST[$v];
                $handled_param[$v] = true;
            }
            else if(isset($this->param[$v]))
                $handled_param[$v] = true;
            else {
                throw new Exception("Parameter [" . $v . "] not found in SQL:\n" . $this->sql."\nPARAM:".var_export($this->param, true));
            }
        }

        // append sql by param
        if(count($handled_param)!=count($this->param)) {
            if(stripos($this->sql, 'where')===false)
                $this->sql .= ' WHERE 1=1';

            foreach ($this->param as $k=>$v) {
               if(isset($handled_param[$k]))
                    continue;

                // param as sql
                preg_match_all("/:([a-zA-Z0-9_.]+)/", $k, $match);
                if(count($match[1])>0) {
                    $this->sql .= ' AND '.$k;
                    foreach($match[1] as $mk=>$mv) {
                        if(isset($_REQUEST[$mv])) {
                            $this->param[$mv] = $_REQUEST[$mv];
                            $handled_param[$mv] = true;
                        }
                    }
                    unset($this->param[$k]);
                }
                else {
                    // LIKE
                    $seg = explode('%', $k);
                    $count_seg = count($seg);
                    if($count_seg>1) {
                        $lk = '';
                        if(!empty($seg[0]))
                            $lk = $seg[0];
                        else if(!empty($seg[1]))
                            $lk = $seg[1];
                        if(!empty($lk)) {
                            $vk = null;

                            if(!empty($this->param[$k]))
                                $vk = $this->param[$k];
                            else if (!empty($_REQUEST[$lk]))
                                $vk = $_REQUEST[$lk];

                            if($vk) {
                                $this->param[$lk] = str_replace($lk, $vk, $k);
                                $this->sql .= " AND $lk LIKE :$lk";
                                $handled_param[$k] = true;
                            }
                        }
                        unset($this->param[$k]);
                    }
                    else if($this->param[$k]!=null) {
                        // AND
                        $this->sql .= " AND $k=:$k";
                        $handled_param[$k] = true;
                    }
                    else if(isset($_REQUEST[$k])) {
                        // AND
                        $this->sql .= " AND $k=:$k";
                        $this->param[$k] = $_REQUEST[$k];
                        $handled_param[$k] = true;
                    }
                    else
                        unset($this->param[$k]);
                }   
            }   
        }
    }

    // insert
    function insert($table, $param=array(), $ignore_null=false) {
        $str_key = '';
        $str_val = '';

        $param = array_merge($this->param, $param);
        foreach($param as $k=>$v) {
            if ($v==null && $ignore_null==true) {
                unset($param[$k]);
                continue;
            }
            if(is_int($k)) {
                unset($param[$k]);
                $k = $v;
                $param[$k] = null;
            }
            $str_key .= '`'.$k . '`,';
            $str_val .= ':' . $k . ',';
        }
        $sql = 'INSERT INTO `'.$table.'` (' . substr($str_key, 0, -1) . ') VALUES(' . substr($str_val, 0, -1) . ')';
        $this->exec($sql, $param);
        return $this->get_last_insert_id();
    }

    // update
    function update($table, $where, $param=[], $ignore_null=false) {
        if(is_array($where)) {
            $param = $where;
            $where = "id=:id";
        }
        
        $where = trim($where);
        preg_match_all("/:([a-zA-Z0-9_.]+)/", $where, $match);
        $match = array_flip($match[1]);
        if(count($match)==0 && strpos($where,'=')===false) {
            $where = $where.'=:'.$where;
            $match[$where] = 0;
        }
        
        $param = array_merge($this->param, $param);
        $sql = 'UPDATE `'.$table.'` SET ';
        foreach($param as $k=>$v) {
            if ($v==null && $ignore_null==true) {
                unset($param[$k]);
                continue;
            }
            
            if(is_int($k)) {
                unset($param[$k]);
                $k = $v;
                $param[$k] = null;
            }

            // skip where conditions
            if(!isset($match[$k]))
                $sql .= ' `'. $k . '`=:' . $k . ',';
        }

        $sql = substr($sql, 0, -1) . ' WHERE ' . $where;        
        return $this->exec($sql, $param);
    }

    // reset
    private function reset() {
        $this->sql = '';
        $this->order_by = '';
        $this->param = array();
        $this->page = array('enable'=>false, 'size'=>50, 'no'=>1);
    }
    
    // order_by
    function order_by($field, $desc=true) {
        $str = trim($field);

        // field name from request
        if(isset($_REQUEST['s']))
            $str = $_REQUEST['s'];

        $this->order_by = trim($str);
        
        if(isset($_REQUEST['d']) && $_REQUEST['d']=='1')
            $desc = false;
        
        if($desc) {
            if(stripos($str, ' desc')===false)
                $this->order_by .= ' DESC';
        }
        else {
            if(stripos($str, ' asc')===false)
                $this->order_by .= ' ASC';
        }
        return $this;
    }
    
    // alias of order_by
    function sort() {
        $args = func_get_args();
        return call_user_func_array(array($this, 'order_by'), $args);
    }

    // paging
    function paging($page_size=0, $page_no=0) {
        global $_CONFIG;
        $this->page = array('enable'=>true, 'size'=>intval($page_size), 'no'=>intval($page_no));

        if($page_size>0) $this->page['size'] = $page_size;
        else if (!empty($_CONFIG[$this->id]['page_size'])) $this->page['size'] = intval($_CONFIG[$this->id]['page_size']);
        if ($this->page['size']<1)  $this->page['size'] = 50;

        if($page_no>0) $this->page['no'] = $page_no;
        else if(!empty($_REQUEST['p']))
            $this->page['no'] = intval('0'.$_REQUEST['p']);
        if ($this->page['no']<1) $this->page['no'] = 1;

        return $this;
    }

    function get_last_insert_id($name=null) {return $this->pdo->lastInsertId($name);}
    function transaction() {return $this->pdo->beginTransaction();}
    function commit() {return $this->pdo->commit();}
    function rollback() {return $this->pdo->rollBack();}
}

// -----------------------------------------------------------------------------
// db manager class
function database_manager() {
    static $obj;
    if(!isset($obj)) $obj = new DatabaseManager;
    return $obj;
}

class DatabaseManager {
    private $db = array();

    // get database obj 
    function get_db($id=null) {
        if ($id==null) $id='db';
        if(isset($this->db[$id])) return $this->db[$id];
        $db = new Database();
        $db->id = $id;
        $this->db[$id] = $db;
        return $db;
    }
}

// -----------------------------------------------------------------------------
// util
// replace querystring
function qstr_replace($name, $value, $querystr=null) {
    if ($querystr===null) $querystr = $_SERVER['QUERY_STRING'];
    $ret = '';
    $found = false;

    foreach(explode('&', $querystr) as $pair) {
        $kv = explode('=', $pair, 2);
        if (!isset($kv[1])) continue;
        if ($kv[0]==$name) {
            $found = true;
            $kv[1] = $value;
        }
        $ret .= $kv[0] . '=' . $kv[1] . '&';
    }

    // append
    if (!$found) $ret .= $name . '=' . urlencode($value) .'&';
    // trim string's end '&'
    $ret = substr($ret, 0, -1);
    // chekc empty
    if ($ret=='=') $ret = '';
    return $ret;
}

// get current url filename
function get_current_url_filename() {
    static $url;
    if(!empty($url)) return $url;
    $url = basename($_SERVER['REQUEST_URI']);
    if(($pos=strpos($url, '?'))!==false) $url = substr($url, 0, $pos);
    return $url;
}

?>