<?php
$data = $_GET;

//$db_two->select_db('ais_online_info');
$db_three=new DB;
$db_three->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password'],$two_mysql['database']);

$sql_str="SELECT * from ais_basestation_realtime where UTC_Year=2016
 and Longitude>=-180*600000 and Longitude<=180*600000 and Latitude>=-90*600000 and Latitude<=90*600000";

$result=$db_three->get_all($sql_str);
$data_array = array();

if($result){
    for($i=0;$i<count($result);$i++){
        $data_array[$i]['Record_Datetime']=(int)$result[$i]['Record_Datetime'];

        $data_array[$i]['mmsi']=(int)$result[$i]['MMSI'];
        $data_array[$i]['UTC_year']=(int)$result[$i]['UTC_Year'];
        $data_array[$i]['UTC_month']=(int)$result[$i]['UTC_Month'];
        $data_array[$i]['UTC_day']=(int)$result[$i]['UTC_Day'];
        $data_array[$i]['UTC_hour']=(int)$result[$i]['UTC_Hour'];
        $data_array[$i]['UTC_minute']=(int)$result[$i]['UTC_Minute'];
        $data_array[$i]['UTC_second']=(int)$result[$i]['UTC_Second'];

//        $data_array[$i]['accuracy']=$result[$i]['accuracy'];

        $data_array[$i]['Longitude']=(int)$result[$i]['Longitude'];
        $data_array[$i]['Latitude']=(int)$result[$i]['Latitude'];

//        $data_array[$i]['Fixing_Device']=$result[$i]['Fixing_Device'];
//        $data_array[$i]['big_control']=$result[$i]['big_control'];
//        $data_array[$i]['MessageID']=$result[$i]['MessageID'];
//        $data_array[$i]['Rev_Datetime']=$result[$i]['Rev_Datetime'];
//        $data_array[$i]['source_id']=$result[$i]['source_id'];
    }

    json_send($data_array);
}else{
    die_json_msg("访问的表为空",10002);
}

