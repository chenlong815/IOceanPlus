<?php
$data = $_GET;

//$db_two->select_db('ais_online_info');
$db_two=new DB;
$db_two->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password'],$two_mysql['database']);

$sql_str="SELECT * from ship_count_realtime order by updatetime desc";
$result=$db_two->get_one($sql_str);
$data_array = array();

if($result){
    $data_array['recordtime']=(int)$result['updatetime'];

    $da_1h=(int)$result['ship_activity_1h_count'];
    $da_2h=(int)$result['ship_activity_2h_count'];
    $da_6h=(int)$result['ship_activity_6h_count'];
    $da_12h=(int)$result['ship_activity_12h_count'];

    $da_24h=(int)$result['ship_activity_24h_count'];

    $da_48h=(int)$result['ship_activity_48h_count'];

    $da_all=(int)$result['ship_count'];

    $data_array['ship_activity_count']=$da_24h;
    $data_array['ship_noneactivity_count']=$da_all-$da_48h;
    json_send($data_array);
}else{
    die_json_msg("访问的表为空",10002);
}
