<?php
/*
* Routers路由类，基于Flight
* map方法批量把自定义类里公有方法映射为接口，方法名为接口路由名
*/
//require_once('./class/flight/Flight.php');
class Routers{
	/*
	* 路由表
	*/
	public static $route_map = array();
	/*
	* 建立路由表 ， 把传入的class类实例里publish公有方法映射以方法名映射作为路由
	* $request请求方法，参考值""/"POST"/"GET"/"GET|POST" , 
	* $path接口路径，默认/ , 
	* $class类实例，先new实例后传入
	*/
	public static function map($request="POST",$path="/",$class=null){
		if(!is_object($class)) return false;
		$class_methods = get_class_methods($class);
		foreach ($class_methods as $method_name) {
			$key = $request . ' ' . $path . $method_name;
			$val = [get_class($class),$method_name];
			self::$route_map[$key]=$val;
		};
		return true;
	}
	/*
	* 重写方法 nofound等
	*/
	public static function rewrite($key=null,$val=null){
		if($key && $val){
			Flight::map($key, $val);
		}
	}
	/*
	* 开启session
	*/
	public static function use_session($expire=3600){
		session_set_cookie_params($expire);
		session_start();
	}
	/*
	* 启动路由
	*/
	public static function start(){
		//遍历路由表
		foreach(self::$route_map as $k=>$v){
			Flight::route($k, $v);
		};
		// 启动路由
		Flight::start();
	}
	
	
}
?>