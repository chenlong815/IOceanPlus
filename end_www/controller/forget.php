<?php

if ($_POST['email'])
{
	$user = model('user')->get_one(array('email'=>$_POST['email']));
	if ($user)
	{
		$s = $user['user_id'].','.time();
		$hash = end_encode($s);
		$s .= ','.$hash;
		
		$s = base64_encode($s);
		
		
		$r_path = dirname($_SERVER['SCRIPT_NAME'].'index.php');
		$r_path = str_replace('\\','/',$r_path);
		if (!$r_path || $r_path == '/') $r_path = '.';
		$_url_base = str_replace('/./','/',str_replace('//','/',$_SERVER['HTTP_HOST'].'/'.$r_path.'/'));
		
		$tp = template('password_email.html');
		$tp->assign('url','http://'.$_url_base.'?p=resetpassword&hash='.$s);
		$html = $tp->result();

		end_mail($user['email'], $config['reset_password_email_title'] ,$html);
		die('ok');
	}
	else
	{
		die('Email not found!');
	}
}