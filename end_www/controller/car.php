<?php

if ($_GET['do'] == "add") {
	$ret = array();
	$data = filter_array($_POST,"htmlspecialchars:license,
		htmlspecialchars:brand,
		htmlspecialchars:model,
		htmlspecialchars:emissions,
		intval:maintenancemiles,
		intval:initializemiles,
		intval:currentmiles,
		strtotime:roadfeesvalidity,
		strtotime:drivinglicensevalidity,
		strtotime:insurancevalidity,
		htmlspecialchars:comment
		");

	if ($data) {
		$uid = $_SESSION['user']['user_id'];

		//检查该用户是否已经添加了该车辆
		$con = array();
		$con['license'] = $data['license'];
		$con['user_id'] = $uid;
		if (model('vanet_v_ucn')->exists($con)) {
			$ret['r'] = 'undo';
			$ret['msg'] = $data['license'].'已存在，请勿重复添加。';
		}
		else {
			//写入车辆信息
			if ($cid = model('vanet_car')->add($data))
			{
				$usercar_data = array();
				$usercar_data['user_id'] = $uid;
				$usercar_data['car_id'] = $cid;

				//写入用户车辆映射信息
				if (model('vanet_usercar')->add($usercar_data)) {
					$ret['r'] = 'ok';
				}
				else {
					$ret['r'] = 'error';
					$ret['msg'] = 'insert error.';
				}
			}
			else {
				$ret['r'] = 'error';
				$ret['msg'] = 'insert error.';
			}
		}
	}
	else
	{
		$ret['r'] = 'error';
		$ret['msg'] = '所填信息不完整!';
	}
	die(json_encode($ret));
}

if ($_GET['do'] == "edit") {
	$ret = array();
	$cid = $_GET['id'];

	$data = filter_array($_POST,"htmlspecialchars:license,
		htmlspecialchars:brand,
		htmlspecialchars:model,
		htmlspecialchars:emissions,
		intval:maintenancemiles,
		intval:initializemiles,
		intval:currentmiles,
		strtotime:roadfeesvalidity,
		strtotime:drivinglicensevalidity,
		strtotime:insurancevalidity,
		htmlspecialchars:comment
		");

	if ($data) {
		$uid = $_SESSION['user']['user_id'];

		//检查该用户是否已经添加了该车辆
		$con = array();
		$con['license'] = $data['license'];
		$con['user_id'] = $uid;
		$con_ret = model('vanet_v_ucn')->get_one($con);
		if ($con_ret && $con_ret['car_id'] != $cid) {
			$ret['r'] = 'undo';
			$ret['msg'] = $data['license'].'冲突。';
		}
		else {
			//更新车辆信息
			if (model('vanet_car')->update($cid, $data))
			{
				$retcon = array(
					'car_id' => $cid,
					'user_id' => $uid
					);
				$retdata = model('vanet_v_ucn')->get_one($retcon);

				$ret['r'] = 'ok';
				$ret['data'] = filter_array($retdata,"intval:car_id,
					license,
					brand,
					model,
					emissions,
					intval:maintenancemiles,
					intval:initializemiles,
					intval:currentmiles,
					inttodate:roadfeesvalidity,
					inttodate:drivinglicensevalidity,
					inttodate:insurancevalidity,
					inttodate:create_time,
					comment,
					sn
					");
			}
			else {
				$ret['r'] = 'error';
				$ret['msg'] = 'update error.';
			}
		}
	}
	die(json_encode($ret));
}

if ($_GET['do'] == "del") {
	$data = filter_array($_POST,"intval:car_id!");
	
	if ($data) {
		$uid = $_SESSION['user']['user_id'];

		$usercararr = array(
			'user_id' => $uid,
			'car_id' => $data['car_id']
			);
		if (model('vanet_car')->delete($data['car_id']) && 
			model('vanet_usercar')->delete($usercararr)) {
			$ret['r'] = 'ok';
		}
		else {
			$ret['r'] = 'error';
			$ret['msg'] = "delete error.";
		}
	}
	else {
		$ret['r'] = 'undo';
		$ret['msg'] = '信息不完整!';
	}
	die(json_encode($ret));
}

$uid = $_SESSION['user']['user_id'];

$con = array();
$con['where'] = "user_id=".$uid;

$car_data = model('vanet_v_ucn')->get_list($con);

$cars = array();
foreach ($car_data as $val) {
	$cars[] = filter_array($val,"intval:car_id,
		license,
		brand,
		model,
		emissions,
		intval:maintenancemiles,
		intval:initializemiles,
		intval:currentmiles,
		inttodate:roadfeesvalidity,
		inttodate:drivinglicensevalidity,
		inttodate:insurancevalidity,
		inttodate:create_time,
		comment,
		sn
		");
}

$view_data['cars'] = $cars;