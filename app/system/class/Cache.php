<?php

/**
 * @author Doanln
 * @copyright 2017
 */

/**
* Cache
*/
class Cache
{
	protected static $expire = 300;
	protected static $dir = '';

	protected $expireTime = 0;
	protected $dirname = '';
	protected $filename = '';


	public function __construct($filename=null,$expireTime=0){
		if($fn = self::getFilename($filename)){
			$this->filename = $filename;
			if(is_numeric($expireTime) && $expireTime > 0) $this->expireTime;
			else $this->expireTime = self::$expire;
		}else{
			throw new Exception("Filename must not empty!", 1);
		}
	}


	public function get($time=null)
	{
		if(!$time) $time = $this->expireTime;
		return self::getCache($this->filename,$time);
	}

	public function getData($key=null,$time=null)
	{
		if($content = self::getCacheWthitoutTime($this->filename)){
			if(!$time) $time = $this->expireTime;
			try{
				$arr = self::convert_arr(json_decode($content));
				if(is_null($key)) return $arr;
				if(isset($arr[$key])){
					$d = $arr[$key];
					if(time()-$d['time']<$time) return $d['content'];
				}
			}catch(Exception $e){
				return null;
			}
		}
		return null;
	}

	public function getObject($time=null)
	{
		if(!$time) $time = $this->expireTime;
		if($a = self::getCache($this->filename,$time)){
			return self::convert_arr(json_decode($a));
		}
		return null;
	}

	public function save($content=null)
	{
		return self::saveCache($this->filename,$content);
	}

	public function saveObject($content=null)
	{
		return self::saveCache($this->filename,json_encode($content));
	}

	public function saveData($key=null,$data=null)
	{
		if(is_string($key) || is_numeric($key)){
			if(is_array($dat = $this->getData())){
				$dat[$key] = array(
					'time' => time(),
					'content' => $data
				);
			}else{
				$dat = array(
					$key => array(
						'time' => time(),
						'content' => $data
					)
				);
			}
			$content = json_encode($dat);
			return $this->save($content);
		}elseif(is_array($key)){
			$data = array();
			if(is_array($d = $this->getData())){
				$data = $d;
			}
			foreach($key as $k => $v){
				$data[$k] = array(
					'time' => time(),
					'content' => $v
				);
			}
			$content = json_encode($data);
			return $this->save($content);
		}
		return false;
	}

	public function delete($key=null)
	{
		if(is_null($key)) return self::remove($this->filename);
		if(is_string($key) || is_numeric($key)){
			if(is_array($data = $this->getData())){
				if(isset($data[$key])){
					unset($data[$key]);
					$content = serialize($data);
					return $this->save($content);
				}
				return true;
			}
		}
		return false;
	}

	public static function setCacheDir($dir=null){
		if(is_string($dir)){
			self::$dir = $dir;
			return true;
		}
		return false;
	}

	public static function setExpireTime($timeSeconds=0)
	{
		if(is_numeric($timeSeconds) && $timeSeconds >= 0){
			self::$expire = $timeSeconds;
			return true;
		}
		return false;
	}

	

	public static function getCache($filename=null,$timeSeconds=0)
	{
		if($f = self::getFilename($filename)){
			if(self::checkExpire($f,$timeSeconds)){
				return App::get_file_contents(self::getFilePath($f));
			}
		}
		return null;
	}

	public static function getCacheWthitoutTime($filename=null)
	{
		if($f = self::getCacheFilename($filename)){
			return App::get_file_contents(self::getFilePath($f));
		}
		return null;
	}


	public static function saveCache($filename=null,$data='')
	{
		if($f = self::getFilePath($filename)){
			if(App::save_file_contents($data, $f))
				return true;
		}
		return false;
	}

	public static function remove($filename=null)
	{
		if($f = self::getCacheFilename($filename)){
			return unlink(self::getFilePath($f));
		}
		return false;
	}

	public static function checkExpire($filename=null,$timeSeconds=0)
	{
		if($f = self::getFilePath($filename)){
			if(file_exists($f)){
				if(is_numeric($timeSeconds) && $timeSeconds >= 0) $time = $timeSeconds;
				else $time = self::$expire;
				if(time() - filemtime($f) <= $time) return true;
			}
		}
		return false;
	}

	public static function getCacheFilename($filename=null)
	{
		if(is_string($filename) && strlen($filename) > 0){
			$fs = explode('.', $filename);
			if(strtolower($fs[count($fs)-1])!='cache') $filename.=".cache";
			if(file_exists(rtrim(self::$dir,'/').'/'.ltrim($filename,'/'))) return ltrim($filename,'/');
		}
		return null;
	}
	
	public static function getFilename($filename=null)
	{
		if(is_string($filename) && strlen($filename) > 0){
			$fs = explode('.', $filename);
			if(strtolower($fs[count($fs)-1])!='cache') $filename.=".cache";
			return ltrim($filename,'/');
		}
		return null;
	}

	public static function getFilePath($filename=null)
	{
		if(is_string($filename) && strlen($filename) > 0){
			$fs = explode('.', $filename);
			if(strtolower($fs[count($fs)-1])!='cache') $filename.=".cache";
			return (rtrim(self::$dir,'/').'/'.ltrim($filename,'/'));
		}
		return null;
	}
	/**
	 * convert object to array
	 * @param String
	 */ 
	protected static function convert_arr($d) {
	    if (is_object($d)) {
	        $d = get_object_vars($d);
	    }
	    if (is_array($d)) {
	        return array_map('Cache::convert_arr', $d);
	    }
	    else {
	        return $d;
	    }
	}
}

