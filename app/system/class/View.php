<?php

/**
 * @author Lê Ngọc Doãn
 * @copyright 2017
 */

// namespace Cube\System;

class View{
    protected static $vars = null;
    protected $currentPath = VIEWDIR;
    protected $_layout = null;
    
    public $filename = null;
    public $variable=array();
    public $cache=null;
    public $cache_time=null;
    protected $pathinfo = false;
    protected $query_string=false;
    protected $currentObjPath = VIEWDIR;
    
    protected $prefix = '';
    protected $path = '';

    protected $_viewContent = null;
    

    protected $arr_prop = array('filename', 'variable', 'cache', 'cache_time', 'pathinfo', 'query_string');


    public function __construct($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false)
    {
        $args = func_get_args();
        foreach ($args as $key => $val) {
            $name = $this->arr_prop[$key];
            $this->{$name} = $val;
        }
    }


    public function setup($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false)
    {
        $args = func_get_args();
        foreach ($args as $key => $val) {
            $name = $this->arr_prop[$key];
            $this->{$name} = $val;
        }
    }

    /**
     * thuc thi file template trong thu muc theme
     * @param String $file file hoac danh sach file, ngan cach bang dau phay (,)
     * @param Array $variable mang gom key ki tu la ten cac bien, key phai tuan theo ten chuan
     * @param bool $cache co su d?ng cache hay ko
     * @param int $cache_time
     * @param bool
     * @param bool
     * @return Void
     * $variable la danh sach bien rieng cho template dang thuc thi va khong bat buoc
     */ 
    public function render($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false)
    {

        include(SYSTEMDIR.'scripts/render.php');
    }

    public function get($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false)
    {
        ob_start();
        
        include(SYSTEMDIR.'scripts/render.php');
        $data = ob_get_clean();
        return $data;
    }

    /**
     * thuc thi file template trong thu muc theme
     * @param String $file file hoac danh sach file, ngan cach bang dau phay (,)
     * @param Array $variable mang gom key ki tu la ten cac bien, key phai tuan theo ten chuan
     * @param bool $cache co su d?ng cache hay ko
     * @param int $cache_time
     * @param bool
     * @param bool
     * @return Void
     * $variable la danh sach bien rieng cho template dang thuc thi va khong bat buoc
     */ 
    


    public function inc($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false)
    {
        
         include(SYSTEMDIR.'scripts/include.php');
    }


    /**
     * thuc thi file template trong thu muc theme
     * @param String $file file hoac danh sach file, ngan cach bang dau phay (,)
     * @param Array $variable mang gom key ki tu la ten cac bien, key phai tuan theo ten chuan
     * @param bool $cache co su d?ng cache hay ko
     * @param int $cache_time
     * @param bool
     * @param bool
     * @return Void
     * $variable la danh sach bien rieng cho template dang thuc thi va khong bat buoc
     */ 
    public function req($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false)
    {
        
        include(SYSTEMDIR.'scripts/include.php');
    }

    /**
     * thuc thi file template trong thu muc theme
     * @param String $file file hoac danh sach file, ngan cach bang dau phay (,)
     * @param Array $variable mang gom key ki tu la ten cac bien, key phai tuan theo ten chuan
     * @param bool $cache co su d?ng cache hay ko
     * @param int $cache_time
     * @param bool
     * @param bool
     * @return Void
     * $variable la danh sach bien rieng cho template dang thuc thi va khong bat buoc
     */ 
    public function tpl($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false)
    {
        include(SYSTEMDIR.'scripts/include.php');
    }

    /**
     * thuc thi file template trong thu muc views
     * @param String $file file 
     * @param Array $variable mang gom key ki tu la ten cac bien, key phai tuan theo ten chuan
     * @return Void
     * $variable la danh sach bien rieng cho template dang thuc thi va khong bat buoc
     */ 
    public function template($tplname = null, $variable = null, $cache=null,$cache_time=0){
        $fn = $this->parseFilepPath('template.').$this->parseFilePrefex($tplname);
        $View = new static($fn,$variable,$cache,$cache_time,true,true);
        $View->render();
    }
    
    public function get_header($ext = null, $variable = null,$cache=null,$cache_time=0){
        $ex = '';
        if(is_string($ext)||is_numeric($ext)){
            $ex = '-'.$ext;
        }
        $fn = $this->parseFilepPath('header'.$ex);
        $View = new static($fn,$variable,$cache,$cache_time,true,true);
        $View->render();
    }
    public function get_footer($ext = null, $variable = null,$cache=null,$cache_time=0){
        $ex = '';
        if(is_string($ext)||is_numeric($ext)){
            $ex = '-'.$ext;
        }
        $fn = $this->parseFilepPath('footer'.$ex);
        $View = new static($fn,$variable,$cache,$cache_time,true,true);
        $View->render();
    }
    public function get_sidebar($ext = null, $variable = null,$cache=null,$cache_time=0){
        $ex = '';
        if(is_string($ext)||is_numeric($ext)){
            $ex = '-'.$ext;
        }
        $fn = $this->parseFilepPath('sidebar'.$ex);
        $View = new static($fn,$variable,$cache,$cache_time,true,true);
        $View->render();
    }
    


    public function layout($filename=null)
    {
        if($filename) $this->_layout = 'layout.'.$filename;
    }

    public function getViewContent()
    {
        echo $this->_viewContent;
    }

    public function setViewContent($val=null)
    {
        $this->_viewContent = $val;
    }


    public function getLayout($data=null,$variable=null)
    {
        $View = new static($this->_layout,$variable);
        $View->setPath($this->path);
        $View->setViewContent($data);
        
        return $View->get();
    }



    public function parseFilePrefex($filename = null)
    {
        $rt = '';
        if($this->prefix){
            $rt.= $this->prefix;
        }
        return ($rt.$filename);

    }

    public function parseFilepPath($filename = null)
    {
        $rt = '';
        if($this->path){
            $rt.= trim($this->path,'/').'/';
        }
        return ($rt.$filename);

    }

    public function parseFilename($filename = null)
    {
        $rt = '';
        if($this->path){
            $rt.= trim($this->path,'/').'/';
        }
        if($this->prefix){
            $rt.= $this->prefix;
        }
        return ($rt.$filename);

    }



    public function setPath($path = null)
    {
        if($path || is_null($path)) $this->path = $path;
    }

    public function setPrefix($prefix = null)
    {
        if($prefix || is_null($prefix)) $this->prefix = $prefix;
    }










    public static function useVars()
    {
        self::$vars = new cube_vars();
    }

    public static function share($name,$val=SD_VAR_DEF_VAL)
    {
        return self::$vars->set($name,$val);
    }
    public static function assign($name,$val=SD_VAR_DEF_VAL)
    {
        return self::$vars->set($name,$val);
    }
    public static function getVar($name=null)
    {
        return self::$vars->get($name);
    }

    public static function isVar($name=null)
    {
        return self::$vars->is($name);
    }

    public static function removeVar($name=null)
    {
        return self::$vars->remove($name);
    }

    public static function printif($name=null)
    {
        if($name && $val = self::getVar($name)){
            if(is_string($val) || is_numeric($val)){
                echo $val;
            }
        }
    }



    public static function getCacheFilename($filename=null,$pathinfo=false,$query_string=false)
    {
        return 'views/'.substr(md5($filename.($pathinfo?'_'.(App::pathinfo()):'').($query_string?'_'.(App::query_string()):'')),0,16);
    }

    public static function execute($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false){

        $View = new static($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
        $View->render();
    }


    public static function display($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false){
        $View = new static($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
        $View->render();
    }

    

    public static function prepare($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false){
        $View = new static($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
        return $View;
    }

    public static function tpl_include($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false){
        $View = new static($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
        $View->render();
    }


    

    /**
     * thuc thi file template trong thu muc theme
     * @param String $file file hoac danh sach file, ngan cach bang dau phay (,)
     * @param Array $variable mang gom key ki tu la ten cac bien, key phai tuan theo ten chuan
     * @return Void
     * $variable la danh sach bien rieng cho template dang thuc thi va khong bat buoc
     */ 
    
    public static function header($ext = null,$cache=null,$cache_time=0){
        $ex = '';
        if(is_string($ext)||is_numeric($ext)){
            $ex = '-'.$ext;
        }
        self::display('header'.$ex,null,$cache,$cache_time,true,true);
    }
    public static function footer($ext = null,$cache=null,$cache_time=0){
        $ex = '';
        if(is_string($ext)||is_numeric($ext)){
            $ex = '-'.$ext;
        }
        self::display('footer'.$ex,null,$cache,$cache_time,true);
    }
    public static function sidebar($ext = null,$cache=null,$cache_time=0){
        $ex = '';
        if(is_string($ext)||is_numeric($ext)){
            $ex = '-'.$ext;
        }
        self::tpl_include('sidebar'.$ex,null,$cache,$cache_time);
    }
}

?>