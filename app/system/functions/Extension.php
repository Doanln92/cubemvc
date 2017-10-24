<?php

/**
 * @author Doanln
 * @copyright 2017
 */

function get_extension_path($extension){
    $p = rtrim(EXTDIR,'/').'/'.ltrim($extension);
    $f = (is_file($p) && !is_dir($p)) ? $p : (is_file($p.'.php')?$p.'.php':null);
    return $f;
}
function useOneExtension($extension){
    $ar = explode('/',$extension);
    $ca = array_pop($ar);
    
    $nsp = str_replace("/", "\\", $extension);
    if(!class_exists($ca) && !class_exists("Extensions\\$nsp")){
        $f = get_extension_path($extension);
        if(!$f) return null;
        include_once($f);
    }
    
    if(!class_exists($ca)  && !class_exists("Extensions\\$nsp")){
        return null;
    }
    return true;
}


function get_extension($extension){
    $ar = explode('/',$extension);
    $ca = array_pop($ar);
    
    if(!useOneextension($extension)){
        return null;
    }
    $nsp = str_replace("/", "\\", $extension);
    $cl = $ca;
    if(class_exists("Extensions\\$nsp")){
        $cl = "Extensions\\$nsp";
    }elseif(class_exists($ca)){
        $cl = $ca;
    }
    
    $args = func_get_args();
    array_shift($args);
    $t = count($args);
    $rc = new ReflectionClass($cl);
    $s = $rc->newInstanceArgs( $args );
    return $s;
}


function use_extension($extensions){
    if(is_string($extensions)){
        if(!preg_match('/,/', $extensions)){
            return useOneExtension($extensions);            
        }
        $extensions = explode(',',$extensions);
    }

    if(!is_array($extensions)) return false;

    $stt = true;

    foreach($extensions as $extension){
        if(!useOneExtension(trim($extension))) $stt = false;
    }
    
    return true;
}
