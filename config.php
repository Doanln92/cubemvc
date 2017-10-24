<?php

/**
 * @author Doanln
 * @copyright 2017
 */

//config database

define('DB_HOST', 'localhost');  //your db host here
define('DB_NAME', 'thegioivuong');        //your db name here
define('DB_USER', 'root');       //your db username here
define('DB_PASS', '');           //your db password here
define('DB_PREFIX', '');         //your table name prefix


session_start();

session_set_cookie_params(strtotime("+1 year"),'/');


ini_set('magic_quotes_gpc',0);

date_default_timezone_set("Asia/Ho_Chi_Minh"); //select timezone

define('BASEDIR', dirname(__FILE__).'/');

$protocols = explode('/',$_SERVER["SERVER_PROTOCOL"]);
$protocol = strtolower($protocols[0]);
$site_url = $protocol .'://'. $_SERVER['SERVER_NAME'];
$doc_root = $_SERVER['DOCUMENT_ROOT'];


define('PROTOCOL',$protocol);
$fd = $_SERVER['SCRIPT_NAME'];
$p = explode('/',$fd);
$rd = str_replace('/'.$p[count($p)-1],'',$fd);
$current = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$home_url = str_replace($doc_root, "$protocol://$_SERVER[HTTP_HOST]", dirname($_SERVER['SCRIPT_FILENAME']));


//you can change anything here
define('LOCALPATH',trim($rd,'/'));
define("HOMEURL",$home_url);
define('CURRENTURL',$current);
define('APPDIR',BASEDIR.'app/');
define('RESOURCESDIR',APPDIR.'resources/');


define('CUBEMOD', 'ON');


unset($protocol,$dirname,$doc_root,$home_url,$site_url,$fd,$rd,$p);




function cube_error_reporting($code=0){
    if(!$code) $code = 0;$es = $code;
    ini_set( 'display_startup_errors',$es);
    ini_set( 'track_errors', $es );
    ini_set( 'log_errors', $es );
    ini_set( 'display_errors', $es );
    if($code!=0)error_reporting($code);
}
?>