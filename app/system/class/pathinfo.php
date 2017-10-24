<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2016
 */

class cube_pathinfo {
    public $path_arr = array();
    public $path_str = '';
    
    function __construct(){
        $this->update(get_pathinfo_arr());
    }
    public function update($path=null){
        if(is_array($path)){
            $this->path_arr = $path;
            $this->path_str = implode('/',$path);
        }
    }
    public function get($pos=null){
        if(!is_null($pos)){
            settype($pos,'int');
            return isset($this->path_arr[$pos-1])?$this->path_arr[$pos-1]:null;
        }
        return $this->path_str;
    }
    public function get_lower($pos=null){
        $p = $this->get($pos);
        if($p){
            $p = strtolower($p);
        }
        return $p;
    }
}



//REDIRECT_URL
function get_pathinfo_arr(){
    $path = null;
    if(_server('PATH_INFO')){
        $path = _server('PATH_INFO');
    }
    elseif(_server('ORIG_PATH_INFO')){
        $path = _server('ORIG_PATH_INFO');
    }elseif(_server('REDIRECT_URL')){
        $path = _server('REDIRECT_URL');
        $apa = explode('/'.trim(LOCALPATH,'/'),$path);
        $a = array_shift($apa);
        $path = implode('/'.trim(LOCALPATH,'/'),$apa);
        $m = explode('?',$path);
        $path = $m[0];
            
    }
    if(!$path){
        $u = _server('REQUEST_URI');
        if($u){
            $m = explode('?',$u);
            $path = $m[0];
            if(LOCALPATH){
                $pg = explode(LOCALPATH,$path);
                $t = count($pg);
                if($t>1){
                    $p = '';
                    for($i=1;$i<$t;$i++){
                        $p.= (($i==1)?'':LOCALPATH).$pg[$i];
                    }
                    $path = $p;
                }else{
                    $path = $m[0];
                }
            }
        }
    }
    $p = array();
    if($path){
        $p = explode('/',trim($path,'/'));
    }
    return $p;
}

?>