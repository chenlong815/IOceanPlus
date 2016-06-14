<?php
if($_GET['do'] == "add") {
	$ret = array();
	$data = filter_array($_POST,"intval:car_id,
		htmlspecialchars:sn!,
		htmlspecialchars:pw!,
		htmlspecialchars:sim_no!
		");

	//检查数据是否有效
	if ($data) {
		$id = array();
		$id['sn'] = $data['sn'];
		$id['pw'] = $data['pw'];

		$nobd = model('vanet_nobd')->get_one($id);
		//判断序列号及密码是否正确
		if (!$nobd) {
			$ret['r'] = 'undo';
			$ret['msg'] = "序列号及密码输入不正确。";
		}
		else {
			$cncon = array(
				'where' => "car_id=".$data['car_id']." && nobd_id is not null"
				);
			//判定该车辆是否存在重复绑定
			if (model('vanet_v_ucn')->get_list($cncon)) {
				$ret['r'] = 'undo';
				$ret['msg'] = '该车辆已经绑定，请勿重复绑定。';
			}
			else {
				//更新数据
				if (model('vanet_car')->update($data['car_id'],array('nobd_id'=>$nobd['nobd_id'])) &&
					model('vanet_nobd')->update($nobd['nobd_id'],array('sim_no'=>$data['sim_no']))) {
					
					$uid = $_SESSION['user']['user_id'];
					$item_con = array(
						'user_id' => $uid,
						'car_id' => $data['car_id']
						);
					$item_data = model('vanet_v_ucn')->get_one($item_con);
					$item = filter_array($item_data,"intval:nobd_id!,
						sn,
						sim_no,
						intval:car_id,
						license,
						inttodate:active_time
						");

					$ret['r'] = 'ok';
					$ret['data'] = $item;
				}
				else {
					$ret['r'] = 'error';
					$ret['msg'] = "insert error.";
				}
			}
		}
	}
	else {
		$ret['r'] = 'undo';
		$ret['msg'] = '所填信息不完整!';
	}
	die(json_encode($ret));
}

if($_GET['do'] == "del") {
	$ret = array();
	$data = filter_array($_POST,"intval:nobd_id!,intval:car_id!");

	if ($data) {
		if (model('vanet_car')->update($data['car_id'],array('nobd_id'=>NULL))) {
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

$con = array('where' => "user_id=".$uid);

$data = model('vanet_v_ucn')->get_list($con);

$nobds = array();
$licenses = array();
foreach ($data as $val) {
	$item = filter_array($val,"intval:nobd_id!,
		sn,
		sim_no,
		intval:car_id,
		license,
		inttodate:active_time
		");
	if ($item["nobd_id"]) {
		$nobds[] = $item;
	}
	$item = filter_array($val,"intval:car_id!,license!,nobd_id");
	//if ($item["car_id"] && !$item['nobd_id']) {
	if ($item["car_id"]) {
		$licenses[] = $item;
	}
}

$view_data["nobds"] = $nobds;
$view_data["licenses"] = $licenses;