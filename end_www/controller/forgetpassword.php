<?php


if ($_POST['username'])
{
	$user = model('user')->get_one(array('username'=>$_POST['username'],'email'=>$_POST['email']));
	if ($user)
	{
		$newpassword = base64_encode(rand(1111111,9999999));
		model('user')->update($user['user_id'],array('password'=>end_encode($newpassword)));
		
		
		$tp = template('password_email.html');
		$tp->assign('newpassword',$newpassword);
		$html = $tp->result();
		
		end_mail($user['email'],'Reset password',$html);
		die('ok');
	}
	else
	{
		die('Not found this username');
	}
}