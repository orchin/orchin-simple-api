<?php
/*
 *  引用
 */
require_once('./class/flight/Flight.php');
require_once('./class/medoo.php');
require_once('./class/database.class.php');
require_once('./class/response.class.php');
require_once('./class/cache.class.php');
require_once('./class/routers.class.php');
require_once('./config.php');
require_once('./util.class.php'); //公共方法类

require_once('./apis.php');					 		//引入自定义方法类
$apis = new apis();
Routers::map('POST','/api/',$apis);	 				//把类里所有publish公有方法映射为路由
Routers::rewrite('notFound',['Util','notfound']); 	//修改404错误处理方法
Routers::start();									//启动路由
?>