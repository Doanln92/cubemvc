<?php
/**
 * @author Le Ngoc Doan
 * @copyright 2017
 * @package Cube.system.class
 */
// namespace Cube\System;
	
class Controller{
	protected $_controllerName = 'Controller';
	private $_controllerID = '';
	private $_controllerCacheName = 'Controllers';
	protected $_cache = null;

	protected $_cacheExpireTime = 300;

	protected $_currentCacheKey = null;

	protected $viewPath = null;
	
	public function __construct($name=null)
	{
		$this->init($name);
	}

	public function init($name=null)
	{
		if($name) $this->_controllerName = $name;
	}

	public function setViewPath($path = null)
	{
		if(is_string($path) || is_null($path)) $this->viewPath = $path;
	}

	public function render($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false)
	{
		$View = new View();
		$View->setPath($this->viewPath);
		$View->render($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
	}

	public function view($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false)
	{
		$View = new View();
		$View->setPath($this->viewPath);
		$View->render($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
	}
	
	public function getView($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false)
	{
		$View = new View();
		$View->setPath($this->viewPath);
		return $View->get($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
	}

	public function parseFilePath($filename=null)
	{
		if(!$this->viewPath) return $filename;
		if(!$filename) return $this->viewPath;
		$path = rtrim($this->viewPath,'/').'/'.ltrim($filename);
		return $path;

	}

	public function share($name=null,$variable=null)
	{
		View::share($name,$variable);
	}
	public function redirect()
	{
		$url = call_user_func_array('url',func_get_args());
		return redirect($url);
	}
	public function alert($message="Hello World!", $alert_type='success')
	{
		$this->view('alert',array('message'=>$message,'alert_type'=>$alert_type));
	}

	public function confirmDelete($id = 0, $message="bạn có chắc chắn muốn xóa không")
	{
		$this->view('confirm-delete',array('id' => $id, 'message'=>$message));
	}

	public function index(){
		echo 'Hello, I am '.$this->_controllerName;
	}

	public function form($filename,$data = null, $fieldList = '*', $formJSON='form',$btnSaveText = 'Save')
	{
		$inputs = $this->getInputList($formJSON,$fieldList);
		$this->view($filename,compact('data','inputs','btnSaveText'));
	}

	public function getInputList($filename,$fieldList='*')
	{
		$s=null;
		if(is_string($fieldList)){
			if($fieldList!="*"){
				$s = explode(',', str_replace(' ', '', $fieldList));
			}
		}elseif(is_array($fieldList)){
			$s = $fieldList;
		}
		$inputs = array();
		if($inps = App::data()->getJSON($filename)){
			if(is_array($s)){
				foreach ($s as $name) {
					if(isset($inps->{$name})){
						$inp = $inps->{$name};
						$inp->name = $name;
						$inputs[] = $inp;
					}
				}
			}else{
				foreach ($inps as $name => $inp) {
					if(!isset($inp->name)){
						$inp->name = $name;
						$inputs[] = $inp;
					}
				}
			}
		}
		return $inputs;
	}

















	public function getControllerName(){
		return $this->_controllerName;
	}
	
	public final function setControllerID($id)
	{
		$this->_controllerID = $id;
	}

	public final function setControllerCacheName($name)
	{
		$this->_controllerCacheName = 'Controllers/'.$name;
	}

	public final function getControllerID($name=null)
	{
		$a = $this->_controllerID.($name?'-'.$name:'');
		return $a;
	}

	public final function getControllerCacheName($name=null)
	{
		$a = $this->_controllerCacheName.($name?'-'.$name:'');
		return $a;
	}

	public final function useCache()
	{
		$this->_cache = new Cache($this->getControllerCacheName(),$this->_cacheExpireTime);
	}

	public final function setControllerCacheExpireTime($time = 0)
	{
		if(is_numeric($time) && $time>0){
			$this->_cacheExpireTime;
		}
		return $this;
	}

	public final function getControllerCache($key=null,$time=null)
	{
		if($this->_cache){
			return $this->_cache->getData($key,$time);
		}
		return null;
	}

	public final function caching($key=null,$time=null)
	{
		if(!$this->_cache) return null;
		if(!$time) $time = $this->_cacheExpireTime;
		if(is_string($key) || is_numeric($key)){
			if($data = $this->getControllerCache($key,$time)){
				echo $data;
				return true;
			}else{
				$this->_currentCacheKey = $key;
				ob_start();
				return false;
			}
		}elseif(is_null($key) && $this->_currentCacheKey){
			$content = ob_get_clean();
			$key = $this->_currentCacheKey;
			$this->_currentCacheKey = null;
			echo $content;
			if($this->_cache->saveData($key,$content)) return true;
			return false;
		}
		return null;
	}

	public final function readCache($key=null)
	{
		if($this->_cache && is_string($key) || is_numeric($key)){
			$this->_currentCacheKey = $key;
			ob_start();
			return true;
		}
		return false;
	}

	public final function saveCache()
	{
		if($this->_cache && $this->_currentCacheKey){
			$content = ob_get_clean();
			$key = $this->_currentCacheKey;
			$this->_currentCacheKey = null;
			echo $content;
			if($this->_cache->saveData($key,$content)) return true;
			return false;
		}
	}

	public final function saveControllerCache($content = null)
	{
		if(!$this->_cache) return null;
		if($content) return $this->_cache->save($content);
	}

	public final function saveControllerCacheData($key=null,$content=null)
	{
		if(!$this->_cache) return null;
		if(is_string($key) || is_numeric($key) || is_array($key)){
			return $this->_cache->saveData($key,$content);
		}
		return null;
	}
}
?>