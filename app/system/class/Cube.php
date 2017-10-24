<?php
/**
 * @author Le Ngoc Doan
 * @copyright 2017
 * @package Cube.system.class
 */

class Cube{
    /**
     * @var $routes
     */

    protected static $_routes = array();

    public static function set($pathinfo = '/', $method = 'GET', $callback = 'cube_index', $cubeName=null)
    {
        if(is_array($pathinfo)){
            $pi = $pathinfo;
            $pathinfo = '/';
            $arg_names = array('pathinfo', 'method', 'callback', 'cubeName');
            foreach($pi as $key => $val){
                if(is_numeric($key) && isset($arg_names[$key])){
                    $$arg_names[$key] = $val;
                }elseif(is_string($key) && in_array($key, $arg_names)){
                    $$key = $val;
                }
            }
        }
    	$route = new Route($pathinfo,$method,$callback,$cubeName);
        if(is_string($cubeName) && $c = trim($cubeName)){
            if(!isset(self::$_routes[$c])) self::$_routes[$c] = $route;
            else self::$_routes[] = $route;
        }
        else self::$_routes[] = $route;

    	return $route;
    }

    public static function setGet($pathinfo = '/', $callback = 'Controller', $cubeName=null)
    {
    	return self::set($pathinfo,'GET',$callback,$cubeName);
    }
    public static function setPost($pathinfo = '/', $callback = 'Controller', $cubeName=null)
    {
    	return self::set($pathinfo,'POST',$callback,$cubeName);
    }
    public static function setPut($pathinfo = '/', $callback = 'Controller', $cubeName=null)
    {
    	return self::set($pathinfo,'PUT',$callback,$cubeName);
    }
    public static function setDelete($pathinfo = '/', $callback = 'Controller', $cubeName=null)
    {
    	return self::set($pathinfo,'DELETE',$callback,$cubeName);
    }
    public static function setHead($pathinfo = '/', $callback = 'Controller', $cubeName=null)
    {
    	return self::set($pathinfo,'HEAD',$callback,$cubeName);
    }
	public static function setOptions($pathinfo = '/', $callback = 'Controller', $cubeName=null)
    {
    	return self::set($pathinfo,'OPTIONS',$callback,$cubeName);
    }

    public static function run()
    {
        $pathinfo = App::pathinfo();
        if(!$pathinfo) $pathinfo = 'index';
        $request = new HTTPRequest();
        $method = strtoupper($request->getMethod());
        if(self::$_routes){
            foreach (self::$_routes as $route) {
                if($route->match($pathinfo) && $route->isMethod($method)){
                    $a = $route->execute($pathinfo);
                    if($a) return true;
                }
            }
        }
        return false;
    }

    public static function getUrl()
    {
        $arg = func_get_args();
        $routeName = array_shift($arg);
        if(is_string($routeName) && isset(self::$_routes[trim($routeName)])){
            $route = self::$_routes[trim($routeName)];
            $a = call_user_func_array(array($route, 'getUrl'), $arg);
            return $a;

        }
        return null;
    }

    public static function getRoute($name=null)
    {
        if(!is_null($name) && (is_string($name) || is_numeric($name)) && isset(self::$_routes[$name])){
            return self::$_routes[$name];
        }
        return null;
    }

}
?>