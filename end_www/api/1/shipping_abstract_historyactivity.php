<?php
$data = $_POST;
if(!isset($data['times'])){
    die_json_msg("参数错误",10100);
}

//$db_two->select_db('ais_online_info');
$db_two=new DB;
$db_two->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password'],$two_mysql['database']);

$all_data=array();

//$once_data=array();
$times=$data['times'];
$now=time();
//$now=1440055920;
$j=0;
$l=0;

while($times>0){
    $end=$now-3600*($j);
    $start=$end-3600;

    $hour_id+=1;

    $sql_str="SELECT * from ship_count_history where updatetime>{$start} and updatetime<={$end} order by updatetime desc limit 1";

    $result=$db_two->get_all($sql_str);
    $data_array = array();

    if($result){
        for($i=0;$i<count($result);$i++){
            $data_array[$i]['hour_id']=(int)$hour_id;
            $data_array[$i]['recordtime']=(int)$result[$i]['updatetime'];

            $data_array[$i]['ship_activity_1h_count']=(int)$result[$i]['ship_activity_1h_count'];
            $data_array[$i]['ship_activity_2h_count']=(int)$result[$i]['ship_activity_2h_count'];
            $data_array[$i]['ship_activity_6h_count']=(int)$result[$i]['ship_activity_6h_count'];
            $data_array[$i]['ship_activity_12h_count']=(int)$result[$i]['ship_activity_12h_count'];
            $data_array[$i]['ship_activity_24h_count']=(int)$result[$i]['ship_activity_24h_count'];
            $data_array[$i]['ship_activity_48h_count']=(int)$result[$i]['ship_activity_48h_count'];

            $data_array[$i]['ship_count']=(int)$result[$i]['ship_count'];

            $data_array[$i]['ship_activity_count']=(int)$data_array[$i]['ship_activity_24h_count'];
            $data_array[$i]['ship_noneactivity_count']=$data_array[$i]['ship_count']-$data_array[$i]['ship_activity_48h_count'];
        }

        $all_data[$l]['once_all_data']=$data_array;

        $times-=1;
        $l+=1;
    }
//    else{
//        //die_json_msg("访问的表为空",10002);
//    }

    $j+=1;
    if($j>48){
        json_send($all_data);
        die;
    }
}
json_send($all_data);



