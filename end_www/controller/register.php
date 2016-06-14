<?php


if ($_POST['username'])
{
	$data = filter_array($_POST,"htmlspecialchars:username!,
		htmlspecialchars:password!,
		email!");
	
	if ($data)
	{
		// print_r($data);
		// die;
		if (!preg_match('/^[a-z0-9]([a-z0-9\-\+\.])*\@([a-z0-9\-]+\.)+[a-z]{2,}$/',$data['email']))
		{
			die('Email 格式错误!');
		}	

		if (model('vanet_user')->get_one(array('email'=>$data['email'])))
		{
			die($data['email'].' 已经被注册!');
		}
		if (model('vanet_user')->get_one(array('username'=>$data['username'])))
		{
			die($data['username'].' 已经被注册!');
		}
		
		if ($uid = model('vanet_user')->add($data))
		{
			$_SESSION['user'] = model('vanet_user')->get_one($uid);
			die('ok');
		}
		else
		{
			die('Error!');
		}
	}
	else
	{
		die('请填写所有表单项!');
	}
}