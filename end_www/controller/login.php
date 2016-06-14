<?php
// echo $_SESSION['helloweba_num'];die;
if ($_POST['username'])
{
	$data = array();
	// if($_POST['check_code']!=$_SESSION['helloweba_num'])
	// {
	// 	die("验证码错误");
	// }
	$data['username'] = $_POST['username'];
	//$password = end_encode($_POST['password']);
	$data['password'] = $_POST['password'];

	$u = model('vanet_user')->get_one($data);

	if ($u)
	{
		$_SESSION['user'] = $u;
		die('ok');
	}
	// 	if ($_POST['remember'] == 'yes')
	// 	{
	// 		$t = time() + 86400*365;
	// 		setcookie('hash',end_encode($u['user_id']),$t);
	// 		setcookie('uid',$u['user_id'],$t);
	// 	}
	// 	die('ok');
	// }
	else
	{
		die('用户名或密码错误!');
	}
}
