<?php

//判断是否登陆
if(!isset($_SESSION['user']))
{
	header("Location: ?p=login",true,302);
	die("Not login!");
}

if ($_POST['name']) {
	// print_r($_POST);
	$data = filter_array($_POST,"
		htmlspecialchars:name!,
		htmlspecialchars:phone!,
		email!");
	
	if ($data)
	{
		//print_r($data);
		//die;
		if (!preg_match('/^[a-z0-9]([a-z0-9\-\+\.])*\@([a-z0-9\-]+\.)+[a-z]{2,}$/',$data['email']))
		{
			die('Email 格式错误!');
		}
		$userid = $_SESSION['user']['user_id'];

		if(model('vanet_user')->update($userid,$data))
		{
			die("ok");
		}
		else
		{
			die("error");
		}
	}
	else
	{
		die('请填写所有表单项!');
	}
}

if ($_POST['newPassword'] && $_POST['password']) {
	// print_r($data);
	// die;
	$userid = $_SESSION['user']['user_id'];

	$id = array();
	$id['user_id'] = $userid;
	$id['password'] = $_POST['password'];

	if (model('vanet_user')->get_one($id)) {
		$data = array();
		$data['password'] = $_POST['newPassword'];
		if(model('vanet_user')->update($userid,$data))
		{
			//$_SESSION['user'] = model("vanet_user")->get_one($userid);
			die("ok");
		}
		else
		{
			die("error");
		}
	}
	else {
		die("原密码输入错误。");
	}
}


$view_data['userinfo'] = model('vanet_user')->get_one($_SESSION['user']['user_id']);