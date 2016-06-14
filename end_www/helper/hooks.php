<?php
function end_on_begin()
{
}

function end_on_after_db()
{
	if ($_GET['p'] == 'setlang' && $_GET['lang'])
	{
		setcookie('lang',$_GET['lang']);
	}
	
	if (!$_SESSION['user'] && $_COOKIE['hash'] && $_COOKIE['uid'])
	{
		if ($_COOKIE['hash'] == end_encode($_COOKIE['uid']))
		{
			$_SESSION['user'] = model('user')->get_one($_COOKIE['uid']);
		}
	}
	
	if ($_SESSION['user'] && $_SESSION['user']['language'])
	{
		define('END_LANGUAGE',$_SESSION['user']['language']);
	}
	else if ($_COOKIE['lang'])
	{
		define('END_LANGUAGE',$_COOKIE['lang']);
	}
	else
	{
		$l = ( strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'],'zh') !== false) ? 'cn' : 'en';
		define('END_LANGUAGE',$l);
	}
}

function end_on_ready()
{
	if ($_GET['p'] != 'login'  
		&& $_GET['p'] != 'register' 
		&& !$_SESSION['user'] 
		&& $_GET['p'] != 'forget' 
		&& $_GET['p'] != 'resetpassword'
		&& $_GET['p'] != 'register2'
	)
	{
		header('Location: ./?p=login');
		die;
	}
}

function end_on_template_begin()
{
	global $view_data,$config,$navigation;
	$view_data['login_user'] = $_SESSION['login_user'];
	$r_path = dirname($_SERVER['REQUEST_URI']);
	if (!$r_path || $r_path == '/') $r_path = '.';
	$_url_base = str_replace('/./','/','http://'.$_SERVER['HTTP_HOST'].'/'.$r_path.'/');
	$view_data['url_base'] = $_url_base;
	$view_data['_get'] = $_GET;
	$view_data['_post'] = $_POST;
	$view_data['_session'] = $_SESSION;
	$view_data['config'] = $config;
	$view_data['debug'] = END_DEBUG;
	$view_data['navigation'] = $navigation;
	//$__temp_rights = model('user_role')->get_one(array('user_role_id'=>$_SESSION['user']['status']));
	//$view_data['__user_rights'] = $__temp_rights['rights'];
	//unset($__temp_rights);
}


function end_on_notfound()
{
	die('404 Not Found');
}