<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2015
 */

/**
 * @param String
 */

function _lang($key=null){
    global $SD;
    if(!is_string($key)&&!is_numeric($key)) return;
    return $SD->lang->get($key);
}
/**
 * tra ve chuoi da duoc dich sang ngon ngu cua ban
 * @param String
 * @param Array
 */ 
function get_text($en_text=null,$var=null){
    global $SD;
    if(is_null($en_text)) return _lang('text');
    if(!is_string($en_text)&&!is_numeric($en_text)) return;
    $text = '';
    $et = explode(',',$en_text);
    $t = count($et);
    for($i=0;$i<$t;$i++){
        $rt = '';
        $g = explode(' ',$et[$i]);
        $l = count($g);
        
        for($j=0;$j<$l;$j++){
            $u = $g[$j];
            $f = _lang('text/'.$u);
            if(is_string($f)) $rt .= $f.' ';
            else $rt .= str_replace('_',' ',$u).' ';
        }
        $rt = rtrim($rt);
        $text .= $rt.', ';
    }
    $text = rtrim($text,', ');
    
    if(is_array($var)){
        $find = array();
        $find2 = array();
        
        $replace = array();
        
        foreach($var as $k => $v){
            $find[] = '{$'.$k.'}';
            $find2[] = '['.$k.']';
            $replace[] = $v;
            
        }
        $text = str_replace($find, $replace, str_replace($find2, $replace, $text));
    }
    return $text;
}

function _text($en_text=null,$var=null){
    echo get_text($en_text,$var);
}
function sd_text($en_text=null,$var=null){
    echo get_text($en_text,$var);
}
function text($en_text=null,$var=null){
    echo get_text($en_text,$var);
}
/**
 * tra ve chuoi da duoc dich sang ngon ngu cua ban
 * @param String
 * @param Array
 */ 
function get_long_text($en_text=null,$var=null){
    global $SD;
    if(is_null($en_text)) return _lang('long_text');
    if(!is_string($en_text)&&!is_numeric($en_text)) return;
    $text = '';
    $et = explode(',',$en_text);
    $t = count($et);
    for($i=0;$i<$t;$i++){
        $rt = '';
        $g = explode(' ',$et[$i]);
        $l = count($g);
        
        for($j=0;$j<$l;$j++){
            $u = $g[$j];
            $f = _lang('long_text/'.$u);
            if(is_string($f)) $rt .= $f.' ';
            else $rt .= str_replace('_',' ',$u).' ';
        }
        $rt = rtrim($rt);
        $text .= $rt.', ';
    }
    $text = rtrim($text,', ');
    
    if(is_array($var)){
        foreach($var as $k => $v){
            $text = str_replace(array('{$'.$k.'}','['.$k.']'), $v,$text);
        }
    }
    return $text;
}

function long_text($en_text=null,$var=null){
    echo get_long_text($en_text,$var);
}

/**
 * tra ve chuoi da duoc dich sang ngon ngu cua ban
 * @param String
 * @param Array
 */ 
function get_group_text($en_text=null,$var=null){
    global $SD;
    if(is_null($en_text)) return _lang('group_text');
    if(!is_string($en_text)&&!is_numeric($en_text)) return;
    $text = _lang('group_text/'.$en_text);
    
    if(is_array($var) && is_string($text)){
        foreach($var as $k => $v){
            $text = str_replace(array('{$'.$k.'}','['.$k.']'), $v,$text);
        }
    }
    return $text;
}


?>