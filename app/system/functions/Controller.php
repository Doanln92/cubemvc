<?php

/**
 * @author Doanln
 * @copyright 2017
 */

function get_controller_path($controller){
    $p = rtrim(CTRLDIR,'/').'/'.ltrim($controller);
    $f = (is_file($p) && !is_dir($p)) ? $p : (is_file($p.'.php')?$p.'.php':null);
    return $f;
}




function useOneController($controller){
    $ar = explode('/',$controller);
    $ca = array_pop($ar);
    
    $nsp = str_replace("/", "\\", $controller);
    if(!class_exists($ca) && !class_exists("Controllers\\$nsp")){
        $f = get_controller_path($controller);
        if(!$f) return null;
        include_once($f);
    }
    
    if(!class_exists($ca)  && !class_exists("Controllers\\$nsp")){
        return null;
    }
    return true;
}


function get_controller($controller){
    $ar = explode('/',$controller);
    $ca = array_pop($ar);
    
    if(!useOnecontroller($controller)){
        return null;
    }
    $nsp = str_replace("/", "\\", $controller);
    $cl = $ca;
    if(class_exists("controllers\\$nsp")){
        $cl = "controllers\\$nsp";
    }elseif(class_exists($ca)){
        $cl = $ca;
    }
    
    $args = func_get_args();
    array_shift($args);
    $t = count($args);
    $rc = new ReflectionClass($cl);
    $s = $rc->newInstanceArgs( $args );
    if(!is_a($s, 'Controller')) return null;
    $s->setControllerID($ca);
    $s->setControllerCacheName($ca);
    if($s->getControllerName() == 'Controller') $s->init($ca);
    return $s;
}


function use_controller($controllers){
    if(is_string($controllers)){
        if(!preg_match('/,/', $controllers)){
            return useOneController($controllers);            
        }
        $controllers = explode(',',$controllers);
    }

    if(!is_array($controllers)) return false;

    $stt = true;

    foreach($controllers as $controller){
        if(!useOneController(trim($controller))) $stt = false;
    }
    
    return $stt;
}









?>