<?php
$data = $_GET;

//$db_two->select_db('ais_online_info');
$db_two=new DB;
$db_two->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password'],$two_mysql['database']);

$sql_str="SELECT * from ais_quality_realtime";

$result=$db_two->get_all($sql_str);
$data_array = array();

$t=time();

if($result){
    for($i=0;$i<count($result);$i++){
        $data_array[$i]['recordtime']=$result[$i]['recordtime'];

        $data_array[$i]['nmea_count']=$result[$i]['nmea_count'];

        $data_array[$i]['message_count']=$result[$i]['message_count'];
        $data_array[$i]['error_count']=$result[$i]['error_count'];
        $data_array[$i]['duplicate_count']=$result[$i]['duplicate_count'];

        if($result[$i]['recordtime']>($t+60)||$result[$i]['recordtime']<($t-60)){
            $nmea_count+=$data_array[$i]['nmea_count'];
            $message_count+=$data_array[$i]['message_count'];
            $error_count+=$data_array[$i]['error_count'];
            $duplicate_count+=$data_array[$i]['duplicate_count'];
        }
    }
        $total_array=array();
        $total_array['time']=(int)$result[count($result)-1]['recordtime'];
        $total_array['total_nmea_count']=$nmea_count;
        $total_array['total_message_count']=$message_count;
        $total_array['total_error_count']=$error_count;
        $total_array['total_duplicate_count']=$duplicate_count;

        json_send($total_array);
    //json_send($data_array);
}else{
    die_json_msg("访问的表为空",10002);
}
