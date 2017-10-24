<?php

class Route {
	protected $_pathinfo = '';
	protected $_parcode = array();
	protected $_paramters = array();
	protected $_callback = null;
	protected $_name = '';
	protected $_condition = array();
	protected $_pos = 0;
	protected $_pattern = '';
	protected $_aft = false;
	protected $_methods = array();
	/**
	 * khoi tao 1 route
	 * @param string $pathinfo 
	 * @param string $method
	 * @param string $callback
	 * @param string $name ten row
	 * @param int $pos vi tri route
	 */
	public function __construct($pathinfo,$method="GET",$callback='TestController',$name=null,$pos=null){
		$pathinfo = ltrim($pathinfo,'/');
		if(!$pathinfo) $pathinfo = 'index';
		if(substr($pathinfo, strlen($pathinfo)-2) == '/*'){
			$pathinfo = rtrim($pathinfo,'*');
			$this->_aft = true;
		}
		$this->_pathinfo = $pathinfo;

		$arr_mth = array();
        if(is_string($method)){
            $ms = explode(',', $method);
            foreach ($ms as $mt) {
                $arr_mth[] = strtoupper(trim($mt));
            }
        }elseif(is_array($method)){
            foreach ($method as $mt) {
                $arr_mth[] = strtoupper(trim($mt));
            }
        }elseif($method=='*'){
			$arr_mth = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS');
		}
        if(count($arr_mth)<1){
            $arr_mth[] = 'GET';
        }
        $this->_methods = $arr_mth;

		$type = gettype($callback);
		if($type == 'object' || $type == 'string'){
			$this->_callback = $callback;
		}
		$this->config();
		if(preg_match('/^[A-z]+[A-z0-9_\/]+$/', $name)) $this->_name = $name;
		if(!is_null($pos)) $this->_pos = $pos;
	}

	protected function config($pathinfo=null){
		if(!$pathinfo) $pathinfo = $this->_pathinfo;
		$p = '/\{([A-z0-9_]*)?\}/si';
		if(preg_match_all($p, $pathinfo, $m)){
			$t = count($m[0]);
			for($i=0;$i<$t;$i++){
				$f = $m[0][$i];
				$prt = trim($m[1][$i]);
				$this->_parcode[] = $f;
				$this->_paramters[] = $prt;
				if(!isset($this->_condition[$prt])){
					$pathinfo = str_replace($f, '***', $pathinfo);
				}
				
			}
		}
		$pathinfo = str_replace(array('/','.'), array('\\/','\\.'), $pathinfo);
		$pathinfo = str_replace('***', '([^\/]+)+', $pathinfo);
		if($this->_aft){
			$pathinfo .= '(.+)*';
			$this->_paramters[] = '__ext__';

		}
		foreach($this->_condition as $key => $value){
			$pathinfo = str_replace('{'.$key.'}', '('.$value.')+', $pathinfo);
		}
		$this->_pattern = $pathinfo;
	}
	
	public function addMethod($method='GET')
	{
		$m = trim(strtoupper($method));
		if($m && !in_array($method, $this->_methods)){
			$this->_methods[] = $m;
		}
		return $this;
	}


	public function isMethod($method=null)
	{
		if(is_string($method)){
			$method = explode(',', $method);
		}
		if(is_array($method)){
			foreach ($method as $m) {
				$m = strtoupper(trim($m));
				if($m && in_array($m, $this->_methods)) return true;
			}
		}
		return false;
	}

	/**
	 * Kiem tra khop tren url
	 * @param String $pathinfo
	 * @return Array / null
	 */
	public function match($pathinfo='')
	{
		$pathinfo = ltrim($pathinfo,'/');
		if(!$pathinfo) $pathinfo = 'index';
		$p = '/^'.$this->_pattern;
		if(!$this->_aft) $p.='$';
		$p.='/si';
		if(preg_match($p, $pathinfo, $m)){
			return $m;
		}
		return null;
	}
	/**
	 * thuc thi rount
	 * @param String $pathinfo
	 * @return boolean
	 */
	
	public function execute($pathinfo='')
	{
		
		$stt = true;
		$n = 0;
		$pathinfo = ltrim($pathinfo,'/');
		if(!$pathinfo) $pathinfo = 'index';
		if($m = $this->match($pathinfo)){
			$args = array();
			$params = array();
			$t = count($m);
			for($i = 1; $i < $t; $i++){
				$v = $m[$i];
				$args[] = $v;
				if(isset($this->_paramters[$n])){
					$params[$this->_paramters[$n]] = $v;
				}else{
					$params[$n] = $v;
				}
				$n++;
			}
			foreach($params as $k => $v){
				if(!$this->compareParam($k,$v)) $stt = false;
			}
			if($stt){
				$cb = $this->_callback;
				$type = gettype($cb);
				$a = null;
				if($type == 'object'){
					$stt = true;
					if(is_callable($cb)){
						$a = call_user_func_array($cb, $args);
					}
					
				}elseif($type=='string'){
					if(function_exists($cb)){
						$stt = true;
						$a = call_user_func_array($cb, $args);
					}elseif($ctl = explode('@', $cb)){
						if($c = get_controller($ctl[0])){
							$mth = isset($ctl[1])?$ctl[1]:'index';
							if(method_exists($c, $mth)){
								$stt = true;
								if(function_exists('call_user_func_array'))
									$a = call_user_func_array(array($c, $mth), $args);
								else{
									$query = '$a = $c->'.$mth.'(';
									foreach($args as $ind => $value){
										$query .= '$args['.$ind.'],';
									}
									$query = rtrim($query,',').');';
									eval($query);
								}
							}
						}
					}
				}
				if(is_string($a) || is_numeric($a)){
					echo $a;
				}
			}
		}
		return $stt;
	}
	/**
	 * set dieu kien cho route
	 * @param Array $condition
	 */
	public function where($condition=null){
		$arr = array();
		if(is_array($condition)) $arr = $condition;
		elseif(is_string($condition)){
			try{
				parse_str($condition,$c);
				$arr = $c;
			}catch(exception $e){
				$ar = explode(',', $condition);
				foreach ($ar as $vl) {
					if(count($m = explode('=', $vl))==2){
						if($k = trim($m[0]) && $v = trim($m[0])){
							$arr[$k] = $v;
						}
					}
				}
			}
		}
		if($arr){
			foreach($arr as $key => $value){
				$this->_condition[$key] = $value;
			}
			//$this->config();
		}
		return $this;
	}

	protected function compareParam($paramName,$paramValue=null){
		$stt = true;
		if(isset($this->_condition[$paramName])){
			$pattern = '/'.$this->_condition[$paramName].'/';
			if(!preg_match($pattern, $paramValue)) $stt = false;
		}
		return $stt;
	}
	public function getName()
	{
		return $this->_name;
	}

	public function setName($name=null)
	{
		if($name) $this->_name = $name;
		return $this;

	}

	public function getUrl($params=null)
	{
		$arr_par = array();
		if(is_array($params)){
			foreach ($params as $key => $value) {
				if(is_numeric($key) && isset($this->_paramters[$key])){
					$arr_par[$this->_paramters[$key]] = $value;
				}elseif(in_array($key, $this->_paramters)){
					$arr_par[$key] = $value;
				}
			}
		}
		else{
			$a = func_get_args();
			foreach ($a as $key => $value) {
				if(isset($this->_paramters[$key])){
					$arr_par[$this->_paramters[$key]] = $value;
				}
			}
		}
		$p = $this->_pathinfo;
		foreach ($arr_par as $key => $value) {
			$p = str_replace('{'.$key.'}', $value, $p);
		}
		$p = preg_replace('/\{[^\}]+\}/', 'null', $p);
		return get_home_url($p);

	}
}