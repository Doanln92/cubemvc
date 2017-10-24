<?php
/**
 * @author Le Ngoc Doan
 * @copyright 2017
 */


class App{
    /**
     * @var Object
     */

    protected static $sdata;


    /**
     * @var Object
     */

    protected static $ldata;


    /**
     * @var Object $cube_object
     */

    protected static $rdata;


    /**
     * @var $_path 
     */
    protected static $_path;
    
    /**
     * @var $_files doi tuong xu ly file
     */
    protected static $_files;
    
    
    /**
     * @var $_map
     */
    public static $_map;
    
    /**
     * @var $_datas
     */
    protected static $_datas;
    
    /**
     * @param Array
     */
     
    protected static $_config = array();
    
    /**
     * ham config thiet lap thong so
     * 
     * 
     * @param Array
     */ 
    
    public static function config($args=null){
        self::$_config = $args;
    }
    
    /**
     * bat dau phien lam viec
     * 
     * @param Array
     */
     
    public static function start($args = null){
        if(!$args) $args = self::$_config;
        self::$sdata = new cube_data(SYSDATADIR);
        self::$ldata = new cube_data(LIBDATADIR);
        self::$rdata = new cube_data(RESDATADIR);
        self::$_files = new Files(array('dir'=>BASEDIR));
        self::$_path = new cube_pathinfo();
        self::$_map  = new cube_map(self::$_config);

        Cache::setCacheDir(TPLRSCACHE);
        Cache::setExpireTime(300);

        db::config(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PREFIX);

        view::useVars();
    }
    
    
    public static function finish()
    {
    	db::disconnect();
    	
    }
    
    
    public static function getSystemData($filename=null)
    {
        return self::$sdata->get($filename);
    }

    public static function getSystemDataObject($filename=null,$method=null,$args=null)
    {
        return self::$sdata->getObject($filename,$method,$args);
    }


    public static function getLibData($filename=null)
    {
        return self::$ldata->get($filename);
    }

    public static function getLibObject($filename=null,$method=null,$args=null)
    {
        return self::$ldata->getObject($filename,$method,$args);
    }

    
    public static function getData($filename=null)
    {
        return self::$rdata->get($filename);
    }

    public static function getObject($filename=null,$method=null,$args=null)
    {
        return self::$rdata->getObject($filename,$method,$args);
    }

    public static function saveData($filename=null, $data=null)
    {
        return self::$rdata->save($filename,$data);
    }
    //path
    
    public static function get_pathinfo($pos=null){
        return self::$_path->get($pos);
    }
    public static function pathinfo($pos=null){
        return self::$_path->get($pos);
    }
    public static function get_lower_pathinfo($pos=null){
        return self::$_path->get_lower($pos);
    }
    public static function lower_pathinfo($pos=null){
        return self::$_path->get_lower($pos);
    }
    
    public static function update_pathinfo($path=null){
        return self::$_path->update($path);
    }
    


    public static function query_string($name=null)
    {
        return request()->query($name);
    }
    //file 

    public static function data($dir = null)
    {
        $data = new files();
        $data->setDir($dir?$dir:DATADIR);
        return $data;
    }
    
    public static function file($dir=null)
    {
        $file = self::$_files;
        if($dir){
            $file->setDir($dir);
        }
        return $file;
    }

    /**
     * @param mixed
     * @param String
     * @param String
     */ 
    
    public static function make_dir($dir=null){
        return self::$_files->make_dir($dir);
    }
    /**
     * @param String
     * @param String
     * @param String
     */ 
    
    public static function save_file_contents($contents, $filename, $ext=null){
        return self::$_files->save_contents($contents,$filename,$ext);
    }
    /**
     * @param String
     * @param String
     * @param String
     */ 
    
    public static function get_file_contents($filename, $ext=null){
        return self::$_files->get_contents($filename,$ext);
    }
    /**
     * @param String
     * @param String
     * @param String
     */ 
    
    
    public static function upload_files($tmp_name='',$new_file=null){
        return self::$_files->upload_files($tmp_name,$new_file);
    }
    
    public static function delete_tree($dirname=null){
        return self::$_files->delete($dirname);
    }
    
    
    public static function get_file_list($dir=null,$ext=null,$sort = false){
        return self::$_files->get_list($dir,$ext,$sort);
    }
    
    
    public static function set_file_dir($dir=null){
        return self::$_files->setDir($dir);
    }
    
    public static function set_default_extension($ext=null){
        return self::$_files->setDefaultExtension($ext);
    }
    
    public static function set_chmod_code($code=0777){
        return self::$_files->setChmodCode($code);
    }
    
    public static function catch_file_error(){
        return self::$_files->catch_error();
    }
    // end file
    
}


?>