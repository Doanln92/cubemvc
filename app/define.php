<?php

define('CTRLDIR',APPDIR.'controllers/');
define('MODDIR',APPDIR.'models/');
define('LIBDIR',APPDIR.'libraries/');
define('LIBCLASSDIR',LIBDIR.'class/');
define('LIBFUNCDIR',LIBDIR.'functions/');
define('LIBDATADIR',LIBDIR.'data/');
define('SYSDIR',APPDIR.'system/');
define('SYSCLASSDIR',SYSDIR.'class/');
define('SYSFUNCDIR',SYSDIR.'functions/');
define('SYSDATADIR',SYSDIR.'data/');
define('CONFIGDIR',APPDIR.'config/');
define('CUBEDIR',APPDIR.'cubes/');
define('SYSTEMDIR',APPDIR.'system/');
define('EXTDIR',APPDIR.'extensions/');
define('DATADIR',APPDIR.'data/');
define('PUBLICDIR',BASEDIR.'public/');
define('CONTENTDIR',PUBLICDIR.'contents/');
define('UPLOADDIR',PUBLICDIR.'uploads/');
define('IMAGEDIR',PUBLICDIR.'images/');
define('CSSDIR',PUBLICDIR.'css/');
define('JSDIR',PUBLICDIR.'js/');
define('AJAXURL',HOMEURL.'/ajax');
define('PUBLICURL',HOMEURL.'/public');
define('CONTENTSURL',PUBLICURL.'/contents/');
define('IMAGESURL',PUBLICURL.'/images/');
define('UPLOADURL',PUBLICURL.'/uploads/');
define('CSSURL',PUBLICURL.'/css/');
define('JSURL',PUBLICURL.'/js/');
define('RESDATADIR',RESOURCESDIR.'data/');
define('CACHEDIR',RESOURCESDIR.'cache/');



/**
 * select theme
 */
  
define('VIEWDIR',APPDIR.'views/');

function get_config_var(){    
    $args = array(
        'local_path' => LOCALPATH,
        'home_url' => HOMEURL,
        'ajax_url' => AJAXURL,
        'home_dir' => BASEDIR,
        'base_dir' => BASEDIR,
        'app_dir' => APPDIR,
        'lib_dir' => LIBDIR,
        'model_dir' => MODDIR,
        'controller_dir' => CTRLDIR,
        'lib_class_dir' => LIBCLASSDIR,
        'lib_func_dir' => LIBFUNCDIR,
        'sys_class_dir' => SYSCLASSDIR,
        'sys_func_dir' => SYSFUNCDIR,
        'content_dir' => CONTENTDIR,
        'upload_dir' => UPLOADDIR,
        'config_dir' => CONFIGDIR,
        'cube_dir' => CUBEDIR,
        'system_dir' => SYSTEMDIR,
        'ext_dir' => EXTDIR,
        'data_dir' => DATADIR,
        'public_dir' => PUBLICDIR,
        'image_dir' => IMAGEDIR,
        'css_dir' => CSSDIR,
        'js_dir' => JSDIR,
        'public_url' => PUBLICURL,
        'image_url' => IMAGESURL,
        'css_url' => CSSURL,
        'js_url' => JSURL,
        'content_url' => CONTENTSURL,
        'upload_url' => UPLOADURL,
        'view_dir' => VIEWDIR,
        'cache_dir' => CACHEDIR,
    );
    
    return $args;
}

?>