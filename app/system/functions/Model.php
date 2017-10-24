<?php

/**
 * @author Doanln
 * @copyright 2017
 */

function get_model_path($model){
    $p = rtrim(MODDIR,'/').'/'.ltrim($model);
    $f = (is_file($p) && !is_dir($p)) ? $p : (is_file($p.'.php')?$p.'.php':null);
    return $f;
}
function useOneModel($model){
    $ar = explode('/',$model);
    $ca = array_pop($ar);
    
    $nsp = str_replace("/", "\\", $model);
    if(!class_exists($ca) && !class_exists("Models\\$nsp")){
        $f = get_model_path($model);
        if(!$f) return null;
        include_once($f);
    }
    
    if(!class_exists($ca)  && !class_exists("Models\\$nsp")){
        return null;
    }
    return true;
}


function get_model($model){
    $ar = explode('/',$model);
    $ca = array_pop($ar);
    
    if(!useOneModel($model)){
        return null;
    }
    $nsp = str_replace("/", "\\", $model);
    $cl = $ca;
    if(class_exists("Models\\$nsp")){
        $cl = "Models\\$nsp";
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


function use_model($models){
    if(is_string($models)){
        if(!preg_match('/,/', $models)){
            return useOneModel($models);            
        }
        $models = explode(',',$models);
    }

    if(!is_array($models)) return false;

    $stt = true;

    foreach($models as $model){
        if(!useOneModel(trim($model))) $stt = false;
    }
    
    return true;
}

function useBaseModel(){
    return useOneModel('BaseModel');
}

function useTableMask(){
    return useOneModel('TableMask');
}

?>