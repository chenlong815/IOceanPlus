<?php

// 接收提交到服务器端的json数据 
function json_receive()
{
	$raw_data = file_get_contents("php://input");
	$data = json_decode($raw_data, true);
	if ($data)
	{
		return $data;
	}
	else
	{
		die_json_msg("post json data error", 10000);
	}
}

// 服务器发送json数据给客户端
function json_send($data=array())
{
	$data = array("ret"=>0, // 0代表成功，其他数字代表失败
		  		  "msg"=>$data);
	$php_ver = (int)substr(PHP_VERSION, 2,1);
	if ($php_ver >= 4)
		$json_data = json_encode($data, JSON_PRETTY_PRINT);
	else
		$json_data = json_encode($data);
	if ($json_data)
	{
		echo $json_data;
	}
	else
	{
		echo json_encode(array("ret"=>10000,
						  	   "msg"=>"json encode error"));
	}
	die();
}

// 服务器发送json数据给客户端
function die_json_msg($message, $error_code=10000)
{
	$json_data =  json_encode(array("ret"=>$error_code,
									"msg"=>$message));
	if ($json_data)
	{
		echo $json_data;
	}
	else
	{
		echo json_encode(array("ret"=>10000,
							   "msg"=>"json encode error"));
	}
	die();


}

// 常规加盐哈希函数
function hash_normal($factor)
{
	$salt = 'fenhe@dreamgram1.0';
	$raw_str = $factor.$salt;
	return sha1(sha1($raw_str));
}

// 随机哈希函数
function hash_random($factor, $hashfunc='sha256')
{
	$salt = '+_)(*&^%$#@!~';
	$raw_str = $factor.time().$salt.rand(2013,65535);
	return hash($hashfunc,$raw_str);
}

// php post 提交数据，返回结果
function do_post_request($url, $data, $method, $optional_headers = null)
{
	$params = array('http' => array(
	             'method' => $method,//'POST',
	          	 'content' => $data
	       ));
	if ($optional_headers !== null) {
	$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if (!$fp) {
	throw new Exception("Problem with $url, $php_errormsg");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
	throw new Exception("Problem reading data from $url, $php_errormsg");
	}
	return $response;
}

// 用户认证函数
function auth_user($data)
{
	if (!isset($data['access_token']))
	{
		die_json_msg('parameter invalid', 10100);
	}

	$item = model('vanet_token')->get_one(array('token_type'=>'user', 
											   'access_token'=>$data['access_token'],
											   'status'=>'valid'));
	if (!$item)
	{
		die_json_msg('parameter value error: access token invalid', 10101);
	}
	return $item;
}

// array数据键筛选
function array_key_filter($data, $keys)
{
	$ret_data = array();
	foreach ($keys as $key)
	{
		if (isset($data[$key]))
			$ret_data[$key] = $data[$key];
	}
	return $ret_data;
}


// 获取查询条目总数
function get_query_item_count($query)
{
	global $db;
	#var_dump($query);
	$query = $db->query($query);
	$count = $db->fetch_array($query);
	#var_dump($count);
	if ($count === false)
		return 0;
	else
		return current($count);
}

define(EARTH_RADIUS, 6371);//地球半径，平均半径为6371km
/*
 *计算某个经纬度的周围某段距离的正方形的四个点
 *
 *@param lng float 经度
 *@param lat float 纬度
 *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.1千米
 *@return array 正方形的四个点的经纬度坐标
 */
function returnSquarePoint($lng, $lat,$distance = 0.1){

    $dlng =  2 * asin(sin($distance / (2 * EARTH_RADIUS)) / cos(deg2rad($lat)));
    $dlng = rad2deg($dlng);

    $dlat = $distance/EARTH_RADIUS;
    $dlat = rad2deg($dlat);

    return array(
        'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
        'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
        'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
        'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
    );
}


//根据用户积分获得用户称号
function getUserTitle($active_points)
{
    $title = "";
    if(is_int($active_points) && $active_points >= 0) {
       if($active_points >= 0 && $active_points < 500) {
           $title = "初出茅庐";
       }
       else if($active_points >= 500 && $active_points < 1000) {
           $title = "略知一二";
       }
       else if($active_points >= 1000 && $active_points < 1500) {
           $title = "半生不熟";
       }
       else if($active_points >= 1500 && $active_points < 2000) {
           $title = "渐入佳境";
       }
       else if($active_points >= 2000 && $active_points < 3000) {
           $title = "登堂入室";
       }
       else if($active_points >= 3000 && $active_points < 4000) {
           $title = "鹤立鸡群";
       }
       else if($active_points >= 4000 && $active_points < 5000) {
           $title = "一代宗师";
       }
       else if($active_points >= 5000 && $active_points < 6000) {
           $title = "震古烁今";
       }
       else if($active_points >= 6000 && $active_points < 7000) {
           $title = "超凡入圣";
       }
       else if($active_points >= 7000 && $active_points < 8000) {
           $title = "天人合一";
       }
       else if($active_points >= 8000 && $active_points < 9000) {
           $title = "返璞归真";
       }
       else if($active_points >= 9000 ) {
           $title = "君临天下";
       }
    }
    return $title;


}