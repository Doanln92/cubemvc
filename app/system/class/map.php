<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2016
 */

define('MAP_URL_DEFAULT',0);
define('MAP_URL_ENCODE',1);
define('MAP_JSON_ENCODE',2);

class cube_map{
    public $_map = array();
    public function __construct($args=null){
        $this->update($args);
    }
    public function update($args=null){
        if(is_array($args)){
            foreach($args as $k => $v){
                $this->_map[strtolower($k)] = $v;
            }
        }
    }
    public function get($key=null,$flag = MAP_URL_DEFAULT){
        if(is_null($key)) return $this->_map;
        if(is_string($key)||is_numeric($key)){
            $u = isset($this->_map[$key])?$this->_map[$key]:null;
            if($u && $flag>0){
                switch($flag){
                    case 1:
                        $u = urlencode($u);
                    break;
                    case 2:
                        $u = json_encode($u);
                    break;
                }
            }
            return $u;
        }
        return null;
    }
    
}

?>