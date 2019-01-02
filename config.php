<?php
/*
 * 跨域访问配置
 */
// header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');
header("Content-Type:application/json");
/*
 * 数据库配置
 */
define('DB_HOST','127.0.0.1');   //主机
define('DB_USER','test');   //数据库用户root
define('DB_PASS','123456');   //数据库密码123456
define('DB_DATABASE','ownsite');   //数据库名称
define('DB_PORT',3306);   //数据库端口
/*
 * 错误码
 */
define('STATUS_SUCCESS',200);		//返回成功
define('STATUS_FAILURE',400);		//返回失败/错误
define('STATUS_NOTFOUND',404);		//找不到资源
define('STATUS_FORBIDDEN',403);		//禁止访问
define('STATUS_UNAUTH',401);		//未授权/未登录
define('STATUS_DBERROR',444);		//数据库连接错误
?>