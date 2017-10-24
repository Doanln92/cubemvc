<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2015
 */



/**
 * @param String
 */

function cube_exists($file=null){
    if((!is_numeric($file)&&!is_string($file))||$file=='') return null;
    $file = rtrim($file,'/');
    $f = get_cube_dir($file);
    if((!is_dir($f)&&file_exists($f))||file_exists($f.'.php')||file_exists($f.'.inc')||file_exists($f.'/index.php')) return true;
    
    return false;
}



function get_cube_file_path($file=null){
    if(!cube_exists($file)) return;
    $mf = null;
    $file = rtrim($file,'/');
    $f = get_cube_dir($file);
    if(!is_dir($f) && file_exists($f))
        $mf = $f;
    elseif(file_exists($f.'.php'))
        $mf = $f.'.php';
    elseif(is_dir($f) && file_exists($f.'/index.php'))
        $mf = $f.'/index.php';
    elseif(file_exists($f.'/index.php'))
        $mf = $f.'/index.php';
    return $mf;
}


function get_cube($file=null){
    if(!cube_exists($file)) return;
    if($m = get_cube_file_path($file)){
        include($m);
    }
    else
        include(get_cube_file_path('empty'));
}

function get_cubes($files=null){
    if(!is_numeric($files)&&!is_string($files)) return;
    global $SD;
    $fs = explode(',',$files);
    for($i=0; $i < count($fs); $i++){
        $file = $fs[$i];
        if(cube_exists($file))
        {
            $file = rtrim($file,'/');
            $f = get_cube_dir($file);
            if(!is_dir($f) && file_exists($f))
                include($f);
            elseif(file_exists($f.'.php'))
                include($f.'.php');
            elseif(file_exists($f.'/index.php'))
                include($f.'/index.php');
            else
                include(get_cube_file_path('empty.php'));
        }
    }
}

?>