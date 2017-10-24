<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2015
 */

/**
 * convert object to array
 * @param String
 */ 
function _do2a($d) {
    if (is_object($d)) {
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        return array_map(__FUNCTION__, $d);
    }
    else {
        return $d;
    }
}

class datas{
    protected $dir = null;
    public function json($file=null,$data=null){
        if(is_null($file)) return $data;
        $f = get_data_dir($file);
        if(is_null($data)){
            $fb = null;
            if(file_exists($file) && !is_dir($file)){
                $fb = $file;
            }
            elseif(file_exists($file.'.json')){
                $fb = $file.'.json';
            }
            elseif(file_exists($f) && !is_dir($f)){
                $fb = $f;
            }
            elseif(file_exists($f.'.json')){
                $fb = $f.'.json';
            }
            $d = array();
            if($fb){
                $a = _do2a(json_decode(file_get_contents($fb)));
                $b = new Arr($a);
                $d = $b->get('data');//;
            }
            return $d;
        }
        $g = explode('.',$file);
        $ex = strtolower($g[count($g)-1]);
        if($ex!='json') $f.='.json';
        if(is_object($data)) $data = _do2a($data);
        $d = array('data'=>$data,'created'=>date("Y-m-d H:i:s"),'time'=>time());
        $d = json_encode($d);
        return App::save_file_contents($d,$f);
    }
    public function json_url($url=null){
        return _do2a(json_decode(file_get_contents($url)));
    }
    public function serliz($file=null,$data=null){
        if(is_null($file)) return $data;
        $f = get_data_dir($file);
        if(is_null($data)){
            $fb = null;
            if(file_exists($file) && !is_dir($file)){
                $fb = $file;
            }
            elseif(file_exists($file.'.sl')){
                $fb = $file.'.sl';
            }
            elseif(file_exists($f) && !is_dir($f)){
                $fb = $f;
            }
            elseif(file_exists($f.'.sl')){
                $fb = $f.'.sl';
            }
            if($fb){
                $a = _do2a(unserialize(file_get_contents($fb)));
                $b = new Arr($a);
                return $b->get('data');//;
            }
        }
        $g = explode('.',$file);
        $ex = strtolower($g[count($g)-1]);
        if($ex!='sl') $f.='.sl';
        if(is_object($data)) $data = _do2a($data);
        $d = array('data'=>$data,'created'=>date("Y-m-d H:i:s"),'time'=>time());
        $d = serialize($d);
        return App::save_file_contents($d,$f);
    }
    public function text($file=null,$data=null){
        if(is_null($file)) return $data;
        $f = get_data_dir($file);
        if(is_null($data)){
            $fb = null;
            if(file_exists($file) && !is_dir($file)){
                $fb = $file;
            }
            elseif(file_exists($file.'.txt')){
                $fb = $file.'.txt';
            }
            elseif(file_exists($f) && !is_dir($f)){
                $fb = $f;
            }
            elseif(file_exists($f.'.txt')){
                $fb = $f.'.txt';
            }
            $d = array();
            if($fb){
                
                
                $d = file_get_contents($fb);
            }
            return $d;
        }
        $g = explode('.',$file);
        $ex = strtolower($g[count($g)-1]);
        if($ex!='txt') $f.='.txt';
        return App::save_file_contents($data,$f);;
    }
    public function html($file=null,$data=null){
        if(is_null($file)) return $data;
        $f = get_data_dir($file);
        if(is_null($data)){
            $fb = null;
            if(file_exists($file) && !is_dir($file)){
                $fb = $file;
            }
            elseif(file_exists($file.'.html')){
                $fb = $file.'.html';
            }
            elseif(file_exists($f) && !is_dir($f)){
                $fb = $f;
            }
            elseif(file_exists($f.'.html')){
                $fb = $f.'.html';
            }
            $d = array();
            if($fb){
                
                $d = file_get_contents($fb);
            }
            return $d;
        }
        $g = explode('.',$file);
        $ex = strtolower($g[count($g)-1]);
        if($ex!='html') $f.='.html';
        return App::save_file_contents($data,$f);
    }
    
    public function get($file=null,$key=null){
        if(is_null($file)) return null;;
        $f = get_data_dir($file);
        $t = 0;
        $fb = null;
        if(file_exists($file) && !is_dir($file)){
            $fb = $file;
        }
        elseif(file_exists($f) && !is_dir($f)){
            $fb = $f;
        }
        elseif(file_exists($file.'.json')){
            $fb = $file.'.json';
        }
        elseif(file_exists($f.'.json')){
            $fb = $f.'.json';
        }
        elseif(file_exists($file.'.sl')){
            $fb = $file.'.sl';
            $t = 1;
        }
        elseif(file_exists($f.'.sl')){
            $fb = $f.'.sl';
            $t = 1;
        }
        if($fb){
            $c = file_get_contents($fb);
            if($t==0||$t==1){
                $d = ($t)?unserialize($c):json_decode($c);
                $a = _do2a($d);
                $b = new Arr($a);
                return $b->get($key);//;
            }
            return $c;
        }
        return null;
        
    }
    public function put($file=null,$type='txt',$data = null){
        if(is_string($file) && is_string($type)){
            $s = false;
            switch(strtolower($type)){
                case 'json':
                    $s = $this->json($file,$data);
                break;
                case 'sl':
                    $s = $this->serliz($file,$data);
                break;
                case 'txt':
                    $s = $this->text($file,$data);
                break;
                case 'html':
                    $s = $this->html($file,$data);
                break;
                
                default:
                    $g = explode('.',$file);
                    $ex = strtolower($g[count($g)-1]);
                    if($ex!=$type) $f.='.'.$type;
                    $s = App::save_file_contents($data,$f);
                break;
            }
            return $s;
        }
        return false;
    }
}
?>