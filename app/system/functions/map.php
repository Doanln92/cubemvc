<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2016
 * @package SauDo url
 */


function cube_join_url($main=null,$join=null){
    $h = is_string($main)?rtrim($main,'/'):'';
    $j = (is_string($join) || is_numeric($join))?$join:'';
    if(is_string($j)) $j = ltrim($j,'/');
    $g = '/';
    $u = $h.$g.$j;
    return $u; 
}


function cube_update_map($args=null){
    App::$_map->update($args);
}

/**
 * @param String
 */

function get_map($item=null,$d=null){
    return App::$_map->get($item,$d);
}
function get_site_url($path=null){
    $h = get_map('site_url');
    return cube_join_url($h,$path);
}
function get_public_url($path=null){
    $h = get_map('public_url');
    return cube_join_url($h,$path);
}
function get_home_url($path=null){
    $h = get_map('home_url');
    return cube_join_url($h,$path);
}
function get_ext_url($path=null){
    $h = get_map('ext_url');
    return cube_join_url($h,$path);
}
function get_cube_url($path=null){
    $h = get_map('cube_url');
    return cube_join_url($h,$path);
}
function get_content_url($path=null){
    $h = get_map('content_url');
    return cube_join_url($h,$path);
}
function get_theme_url($path=null){
    $h = get_map('theme_url');
    return cube_join_url($h,$path);
}


function get_home_dir($path=null){
    $h = get_map('home_dir');
    return cube_join_url($h,$path);
}
function get_public_dir($path=null){
    $h = get_map('public_dir');
    return cube_join_url($h,$path);
}
function get_ext_dir($path=null){
    $h = get_map('ext_dir');
    return cube_join_url($h,$path);
}
function get_theme_dir($path=null){
    $h = get_map('theme_dir');
    return cube_join_url($h,$path);
}
function get_cube_dir($path=null){
    $h = get_map('cube_dir');
    return cube_join_url($h,$path);
}
function get_content_dir($path=null){
    $h = get_map('content_dir');
    return cube_join_url($h,$path);
}
function get_data_dir($path=null){
    $h = get_map('data_dir');
    return cube_join_url($h,$path);
}


function get_home_path($path=null){
    $h = get_map('home_dir');
    return cube_join_url($h,$path);
}
function get_content_path($path=null){
    $h = get_map('content_dir');
    return cube_join_url($h,$path);
}
function get_ext_path($path=null){
    $h = get_map('ext_dir');
    return cube_join_url($h,$path);
}
function get_data_path($path=null){
    $h = get_map('data_dir');
    return cube_join_url($h,$path);
}

function home_url($path=null){
    echo get_home_url($path);
}
function content_url($path=null){
    echo get_content_url($path);
}
function public_url($path=null){
    echo get_public_url($path);
}
function theme_url($path=null){
    echo get_theme_url($path);
}


function home_dir($path=null){
    echo get_home_dir($path);
}
function site_url($path=null){
    echo get_site_url($path);
}

function path_url($path=null){
    echo get_path_url($path);
}

class _sdc_url_{
    static $url;
    public static function set($url){
        self::$url = $url;
    }
    public static function add_after($path=null){
        if($path){
            self::$url = rtrim(self::$url,'/').'/'.ltrim($path,'/');
        }
    }
    public static function get(){
        $u = self::$url;;
        return $u?$u:get_home_url();
    }
}
function set_current_link($url=null){
    _sdc_url_::set($url);
}
function current_link_after($path=null){
    _sdc_url_::add_after($path);
}
function get_current_link($urlencode=false){
    $u = _sdc_url_::get();
    return $urlencode?urlencode($u):$u;
}
function current_link($url=null,$path=null){
    if(is_null($url)&&is_null($path)){
        return get_current_link();
    }
    elseif(is_string($url)||is_numeric($url)){
        $u = rtrim($url,'/').'/'.((is_string($path)||is_numeric($path))?ltrim($path,'/'):'');
        set_current_link($u);
    }
    elseif(is_string($path)||is_numeric($path)){
        current_link_after($path);
    }
    return true;
}

function get_current_url($query_string=false,$urlencode=false){
    $url = get_home_url(App::pathinfo());
    if($query_string && $_SERVER['QUERY_STRING']){
        $url .= '?'.$_SERVER['QUERY_STRING'];
    }
    if($urlencode){
        $url = urlencode($url);
    }
    return $url;
}
function current_url($query_string=false,$urlencode=false){
    echo get_current_url($query_string,$urlencode);
}

/**
 * 
 * add quey string to url
 * @param string $url dung dan lien ket
 * @param array $name mang query hoac ten bien neu la bien don
 * @param string $val gia tri bien
 * @return string $url
 */  

function reqURL($url, $name, $val = null){
    $u = $url;
    $r = array();
    $f = explode('?',$url);
    $q = array();
    if(count($f)>1){
        $u = $f[0];
        parse_str($f[1],$d);
        $r = $d;
    }elseif(count($f)==1){
        $u = $f[0];
    }
    if(is_string($name)) $r[$name] = $val;
    elseif(is_array($name)){
        foreach($name as $n => $v){
            if(is_string($n)){
                $r[$n] = $v;
            }
        }
    }
    $p = '';
    foreach($r as $k => $v){
        if(is_string($v)||is_numeric($v)){
            $p.=$k."=".urlencode($v).'&';
        }
    }
    $re = trim($p,'&');
    $u.=(($re)?"?".$re:"");
    return $u;
}

function cube_redir($url='/'){
    if(!is_string($url)) $url = get_home_url();
    header('location: '.$url);
    exit;
}

/**
 * @param String $dir Duong dan noi bo (tuyet doi)
 * @param String $path duong dan can tro toi
 */
function get_path_url($dir=null,$path=null){
    if(!is_string($dir)) $u = get_home_url();
    else $u = get_home_url(str_replace(BASEDIR,'',$dir));
    if($path) $u = cube_join_url($u,$path);
    return $u;
}
function get_dir_url($dir=null,$path=null){
    if(!is_string($dir)) $u = get_home_url();
    else $u = get_home_url(str_replace(BASEDIR,'',$dir));
    if($path) $u = cube_join_url($u,$path);
    return $u;
}

function url($name=null){
    if(Cube::getUrl($name)){
        $args = func_get_args();
        $url = call_user_func_array('Cube::getUrl',$args);
    }elseif($u = get_map($name)){
        $url = $u;
    }elseif($i = get_map($name.'_url')){
        $url = $i;
    }else{
        $url = get_home_url($name);
    }
    return $url;
}
function redirect($url='/'){
    if(!is_string($url)) $url = get_home_url();
    header('location: '.$url);
    exit;
}

?>