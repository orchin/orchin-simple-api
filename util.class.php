<?php
/*
* 用户公共方法类
*/
//require_once('./class/response.class.php');
//require_once('./class/cache.class.php');
//require_once('./class/database.class.php');
class Util{
	/*
	* 统一获取json参数
	*/
	public static function jsonParam($key,$default=""){
		$jsonStr = strval(file_get_contents("php://input"));
		$json = json_decode($jsonStr, true);
		return (isset($json[$key]) ? $json[$key] : $default);
	}
	/*
	* 连接数据库
	*/
	public static function DB(){
		$db = dataBase::db();
		if(!$db){
			return Response::show(STATUS_DBERROR,'数据库连接失败','');
			exit;
		};
		return $db;
	}
	/*
	* 获取真实token，参数$key 为用户标识（我用"U"+id）
	*/
	public static function realToken($key){
		$cache = new Cache();
		$values = $cache->get($key);//尝试读取缓存token
		if($values){
			return $values;
		}else{											//没有缓存则生成新的token
			$str = md5(uniqid(md5(microtime(true)),true));
			$cache->set($key, $str);
			return $str;
		};
	}
	/*
	* 删除缓存真实token
	*/
	public static function delRealToken($uid){
		$cache = new Cache();
		$values = $cache->del('U'.$uid);
		return true;
	}
	/*
	* 获取用户token，返回：base64(用户id|用户真实token)
	*/
	public static function get_token($uid){
		return base64_encode($uid.'|'.self::realToken('U'.$uid));
	}
	/*
	* 检查用户token，直接检查提交json数据中的token字段
	*/
	public static function check_token(){
		$token_array = explode('|', base64_decode(self::jsonParam('token','')) );
		$check_ok = false;
		if(sizeof($token_array)==2){
			$uid = (int)$token_array[0];
			$utoken = (string)$token_array[1];
			$curtoken = self::realToken('U'.$uid);
			if($utoken == $curtoken) $check_ok=true;
		};
		if($check_ok){
			return true;
		}else{
			return self::unauth();
			exit;
		};
	}
	/*
	* 200操作成功
	*/
	public static function success($data='',$msg='success'){
		return Response::show(STATUS_SUCCESS,$msg,$data);
	}
	/*
	* 400操作失败
	*/
	public static function failure($msg='failure',$data=''){
		return Response::show(STATUS_FAILURE,$msg,$data);
	}
	/*
	* 404找不到资源
	*/
	public static function notfound(){
		return Response::show(STATUS_NOTFOUND,'没有这个接口!');
	}
	/*
	* 401未登录/未授权
	*/
	public static function unauth(){
		return Response::show(STATUS_UNAUTH,'用户未登录!');
	}
}
?>