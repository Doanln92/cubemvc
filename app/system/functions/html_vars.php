<?php

/**
 * @author Doanln
 * @copyright 2017
 */


/**
 * @param String
 */ 




function theme_page_class($class=null){
    if(!is_null($class)) return cube::set_var('theme_page_class',$class);
    $c = cube::get_var('theme_page_class');
    if($c) echo " class=\"$c\" ";
}
function add_theme_page_class($class=null){
    if(!is_null($class)) return cube::set_var('theme_page_class',$class);
}
function setActiveMenu($menu=null){
    if(!is_null($menu)) cube::set_var('__active_menu__',$menu);
}

function getActiveMenu(){
    return cube::get_var('__active_menu__');
}

function activeMenu($menu=null){
    if($menu) return setActiveMenu($menu);
    return cube::get_var('__active_menu__');
}
function _activeMenu($menu=null){
    if(!is_null($menu)) return setActiveMenu($menu);
    echo cube::get_var('__active_menu__');
}


/**
 * de in gia tri chi can goi ham _pagetitle(); khong duoc dua them tham so
 * @param String
 * 
*/

function _pagetitle($title=null){
    if(is_bool($title)&&$title==false)
        return cube::get_var("_pagetitle");
    
    if(is_null($title)){
        $t = cube::get_var("_pagetitle");
        echo $t;
    }elseif(is_string($title)){
        cube::set_var("_pagetitle", $title);
    }
}
function add_pagetitle($title=null){
    if(is_string($title)){
        cube::set_var("_pagetitle", $title);
    }
}

function get_pagetitle(){
    return cube::get_var("_pagetitle");
}
function add_pagetitle_before($title=null,$d = ' | '){
    if(is_string($title)||is_numeric($title)){
        $ti = $title.(is_string($d)? $d:'').cube::get_var("_pagetitle");
        cube::set_var("_pagetitle", $ti);
    }
}
function add_pagetitle_after($title=null,$d = ' | '){
    if(is_string($title)||is_numeric($title)){
        $ti = cube::get_var("_pagetitle").(is_string($d)? $d:'').$title;
        cube::set_var("_pagetitle", $ti);
    }
}

function _image_src($src=null){
    if(is_null($src)){
        $t = cube::get_var("_image_src");
        echo $t;
    }elseif(is_string($src)){
        cube::set_var("_image_src", $src);
    }elseif(is_bool($param)&&$param==false)
    {
        return cube::get_var("_image_src");
    }
}
/*
function _doctype($param=null){
    global $SD;
    $type = $SD->configData->get('og_type');
    if(is_null($param)){
        $t = get_var("_doctype");
        if(is_array($t)) $t=json_encode($t);
        echo htmlvi($t);
    }elseif($param){
        if(isset($type[$param])) $ot = $type[$param];
        else $ot = $type['default'];
        cube::set_var("_doctype", $ot);
    }
}
*/
function _description($param=null){
    if(is_bool($param)&&$param==false)
        return cube::get_var("_description");
    
    if(is_null($param)){
        $t = cube::get_var("_description");
        if(is_array($t)) $t=json_encode($t);
        echo strip_tags($t);
   }elseif($param){
        cube::set_var("_description", $param);
   }
}

function _keywords($param=null){
    if(is_bool($param)&&$param==false)
        return cube::get_var("_keywords");
    
    if(is_null($param)){
        $t = get_var("_keywords");
        if(is_array($t)) $t=json_encode($t);
        echo $t;
    }elseif($param){
        cube::set_var("_keywords", $param);
    }
}





?>