<?php
/*
* dataBase数据库类，基于medoo
*/
require_once('./class/medoo.php');
class dataBase{
	/*
	* medoo数据库连接方法db()
	*/
	public static function db(){
		try {
			$database = new medoo([
				'server' => DB_HOST, //数据库地址
				'username' => DB_USER, //用户名
				'password' => DB_PASS, //密码
				'database_name' => DB_DATABASE,//数据库名称
				'database_type' => 'mysql', //数据库类型
				'charset' => 'utf8',//数据库编码	
				'port' => DB_PORT,// [可选的] 数据库连接端口	
				//'prefix' => 'PREFIX_',// [可选]表前缀
				'option' => [PDO::ATTR_CASE => PDO::CASE_NATURAL]
			]);	
		}catch(Exception $e){
			return null;
		}
		return $database;
	}
}
?>