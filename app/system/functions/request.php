<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2015
 */

/**
 * Ham danh rieng cho truy van post;
 * @param string: ten tham so
 * @param varible: de trong neu muon lay gia tri
 */

define('REQ_DEF_VAL','1010001100010101011001100101101010101010010110101010101010101010101010101010101010101010101');
function reqs($name=null,$value=REQ_DEF_VAL){
    static $vars;
    if(is_null($name)) return $vars;
    if(is_string($name)){
        if(!is_null($value)) {
            if(!is_array($vars)) $vars = array();
            $vars[$name] = $value;
        }
        elseif($name!='') return isset($vars[$name])?$vars[$name]:null;
    }
    elseif(is_array($name)){
        if(!is_array($vars)) $vars = array();
        foreach($name as $n => $v){
            $vars[$n] = $v;
        }
    }
}

function _request($name=null){
    if($name) return (isset($_REQUEST[$name]))?$_REQUEST[$name]:null;
    return $_REQUEST;
}
function _post($name=null){
    if($name) return (isset($_POST[$name]))?$_POST[$name]:null;
    return $_POST;
}
function _get($name=null){
    if($name) return (isset($_GET[$name]))?$_GET[$name]:null;
    return $_GET;
}
function _cookie($name=null,$value=null,$time=null){
    if($name && is_numeric($time)){
        if(setcookie($name,$value,$time)) return true;
    }
    elseif($name) return (isset($_COOKIE[$name]))?$_COOKIE[$name]:null;
    return $_COOKIE;
}
function _session($name=null,$val=REQ_DEF_VAL){
    if($val!=REQ_DEF_VAL){
        $f = (string) $name;
        if(!$f) $f = 'ss_'.md5(rand(0,1000));
        $_SESSION[$f] = $val;
        return;
    }
    if(!is_null($name)) return isset($_SESSION[$name])?$_SESSION[$name]:null;
    return $_SESSION;
}
function _server($name=null){
    if($name) return (isset($_SERVER[$name]))?$_SERVER[$name]:null;
    return $_SERVER;
}
/**
 * @param String: ten cua bien set vao hoac lay ra tu _form
 * @param varible: Gia tri cua bien
 * name co the nhan vao mang neu mab nhon set nhieu bien
 * vi du name = array('a'=>0,'b=>1')
 * ban cung co the lay ra gia tri cua bien bang cach lay so thu tu cua bieb ma ban da set, bat dxu = 0;
 * muon reset _form ban chi can gan cho nema = true hoac false
 */
function _form($name=null,$value=null){
    static $this_data;
    if(is_array($name)){
        $this_data = $name;
    }elseif(is_bool($name)){
        $this_data = array();
    }elseif((is_numeric($name) || is_string($name)) && is_null($value)){
        return (is_array($this_data) && isset($this_data[$name]))?$this_data[$name]:null;
    }
    elseif((is_numeric($name) || is_string($name))&&is_null($value)){
        if(!is_array($this_data)) $this_data = array();
        $this_data[$name] = $value;
    }
    elseif(is_null($name)){
        return $this_data;
    }
}

function check_captcha($code=''){
    $s = false;
    if($code && (is_string($code)||is_numeric($code))){
        if(_session('captcha')){
            if(strtolower(_session('captcha'))==strtolower($code)) $s = true;
        }
    }
    return $s;
}

?>