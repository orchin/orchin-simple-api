# Orchin Simple API

简单的php开发的Api接口框架，基于Flight和Medoo，主要用于前后端分离的接口开发。
A simple php Api framework, based on Flight and Medoo, It is mainly used for the rapid development of API interface with front and back end separation.

##Features 主要功能
1. Simple & Lightweight
   简单和轻量。
2. Auto Create Routes - Mapping methods to routing in batches.
   自动创建路由，批量把方法映射成路由。
3. Support three formats of Json/Xml/Jsonp
   支持JSON/XML/JSONP三种返回格式
4. Extensible - Easy to extend functionality
   容易扩展
   
##Requirements 系统要求
- PHP >= 5.4 ---- PHP版本大于5.4
- A HTTP client ---- HTTP客户端，Nginx或者Apache
- PDO extension installed ---- 安装了PDO扩展

##Get Started 使用方法
- 配置 `config.php`
- 主文件中添加和启动路由
```php
require_once('./util.class.php'); //引入公共方法类
require_once('./apis.php'); //引入自定义API方法类
$apis = new apis();
Routers::map('POST','/api/',$apis); //把类里所有publish公有方法映射为路由
Routers::rewrite('notFound',['Util','notfound']); //修改404错误处理方法
Routers::start(); //启动路由
```

##Directory Structure 目录结构
	|-- cache                            // 缓存文件夹
	|-- class                            // 源码目录
	|   |-- flight                       // Flight组件目录
	|   |-- cache.class.php              // 缓存类文件
	|   |-- database.class.php           // 数据库连接类文件
	|   |-- medoo.php                    // Medoo组件文件
	|   |-- response.class.php           // 响应类文件
	|   |-- routers.class.php            // 路由类文件
	|-- .htaccess                        // Apache重定向配置文件
	|-- apis.php                         // 自定义接口代码类
	|-- config.php                       // 数据库配置文件
	|-- crossdomain.xml                  // 跨域cross文件
	|-- index.php                        // 主文件，程序入口
	|-- util.class.php                   // 自定义公共方法类

##Nginx config
```
server {
	server_name localhost;
	listen	8088;
	root	/orchin-simple-api;
	
	location / {
		autoindex on;
		autoindex_exact_size on;
		autoindex_localtime on;
		charset utf-8,gbk;
		index  index.html index.php;
        try_files $uri $uri/ /index.php?$args;
	}
	location ~ ^(.+.php)(.*)$ {
		fastcgi_split_path_info ^(.+.php)(.*)$;
		include fastcgi.conf;
		fastcgi_index index.php;
		include        fastcgi_params;
		fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
		fastcgi_pass  127.0.0.1:9000;
	}
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf|flv|mp4|ico)$ {
        expires 30d;
        access_log off;
    }
    location ~ .*\.(js|css)?$ {
        expires 7d;
        access_log off;
    }
    location ~ /\.ht {
        deny all;
    }
}
```
### End