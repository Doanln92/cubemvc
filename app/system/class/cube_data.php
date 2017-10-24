<?php
/**
* CubeObject
*/
class cube_data
{
	protected $objectPath = RESDATADIR;
	public function __construct($objectPath=null)
	{
		if($objectPath && count(explode(BASEDIR, $objectPath))==2){
			$this->objectPath = $objectPath;
		}
	}

	public function getFilePath($filename=null)
	{
		if(is_string($filename) || is_numeric($filename)){
			if(count(explode(BASEDIR, $filename)) < 2) $filename = $this->objectPath.ltrim($filename,'/');
			if(!preg_match('/.*\.cd$/si', $filename)) $filename.='.cd';
			return $filename;
		}
		return null;
	}
	public function exists($filename=null)
	{
		if($f = $this->getFilePath($filename))
		{
			if(file_exists($f))
			{
				return true;
			}
		}
		return false;
	}
	public function getPathExists($filename=null)
	{
		if($f = $this->getFilePath($filename))
		{
			if(file_exists($f))
			{
				return $f;
			}
		}
		return null;
	}

	public function get($filename=null)
	{
		if($f = $this->getPathExists($filename)){
			try{
				$ser = App::get_file_contents($f);
				$obj = unserialize($ser);
				return $obj;
			}catch(exception $e){
				$r = null;
			}
		}
		return null;
	}

	public function getObject($filename=null,$method=null,$args=null)
	{
		if($f = $this->get($filename)){
			try{
				if(is_object($f)){
					if(is_string($method)){
						$r = call_user_func_array(array($f,$method), $args);
					}
					return $obj;
				}
			}catch(exception $e){
				$r = null;
			}
		}
		return null;
	}

	public function save($filename=null,$data=null)
	{
		if($f = $this->getFilePath($filename)){
			return App::save_file_contents(serialize($data), $f);
		}
		return false;
	}
}
?>