<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2015
 */

//REDIRECT_URL
function get_cube_pathinfo(){
    return App::get_pathinfo();
}

function get_pathinfo($pos=null,$c_path=null){
    $path = (!is_null($c_path))?trim($c_path):(get_cube_pathinfo()?trim(get_cube_pathinfo(),'/'):null);
    if(!$pos) return $path;
    if($path &&(is_numeric($pos)||is_string($pos))){
        $pos--;
        $ps = explode('/',$path);
        return isset($ps[$pos])?$ps[$pos]:null;
    }
    return null;
}
function get_lower_pathinfo($pos=null,$c_path=null){
    $u = get_pathinfo($pos,$c_path);
    if($u) $u = strtolower($u);
    return $u;
}

function get_path_info($pos=null){
    $path = get_cube_path();
    if(!$pos) return trim($path,'/');
    $ps = explode('/',trim($path,'/'));
    if(!is_null($pos))return isset($ps[$pos])?$ps[$pos]:null;
    return $ps;
}

function get_list_pathinfo($lower=null){
    $p = get_cube_pathinfo();
    if($lower) $p = strtolower($p);
    if(!$p || $p=='/') return null;
    $a = array();
    $b = array();
    $p = trim($p,'/');
    $d = '';
    $e = explode('/',$p);
    $t = count($e);
    for($i=0;$i<$t;$i++){
        $s = ($i>0)?'/':'';
        $d.=$s.$e[$i];
        $a[$i] = $d;
    }
    $l = count($a);
    for($i=$l-1;$i>=0;$i--){
        $b[] = $a[$i];
    }
    return $b;
    
}

?>