<?php

/**
 * 极光推送-V2. PHP服务器端
 * @author 夜阑小雨
 * @Email 37217911@qq.com
 * @Website http://www.yelanxiaoyu.com
 * @version 20130118 
 */
	define('DB_HOST', 'localhost');
	define('DB_USER', 'root'); //数据库用户名
	define('DB_PWD', 'root'); //数据库密码
	define('DB_NAME', 'quanbao'); //数据库
	define('DB_TAB', 'end_chat_push');//数据表
	define('DB_CODE','utf8');
	define('appkeys','7c29aafddb64ff5827e7f55c');	//appkey值 极光portal上面提供
	define('masterSecret', 'dabc180e158e3dbf699b6034');    //API MasterSecert值 极光portal上面提供
	define('platform', 'android,ios');    //推送平台
?>