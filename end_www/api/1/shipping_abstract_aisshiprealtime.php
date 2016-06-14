<?php
$data = $_POST;
if(!isset($data['longitude'])||!isset($data['latitude'])){
    die_json_msg("参数错误",10100);
}

if(!isset($data['deg'])){
    $deg=2;
}else{
    $deg=$data['deg'];
}

$db_two=new DB;
$db_two->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password'],$two_mysql['database']);

$longitude=$data['longitude'];
$latitude=$data['latitude'];

$data_array = array();

$sql_str="select * from ais_ship_realtime where Longitude/600000>{$longitude} and Longitude/600000<({$longitude}+{$deg}) and Latitude/600000<{$latitude} and Latitude/600000>({$latitude}-{$deg}) limit 1000";

//echo $sql_str;
//die;

    $result=$db_two->get_all($sql_str);

    if($result){

        for($i=0;$i<count($result);$i++){

            $data_array[$i]['id']=(int)$i+1;

            $data_array[$i]['MMSI']=(int)$result[$i]['MMSI'];
            $data_array[$i]['Longitude']=(int)$result[$i]['Longitude']/600000;
            $data_array[$i]['Latitude']=(int)$result[$i]['Latitude']/600000;
            $data_array[$i]['Direction']=(int)$result[$i]['Direction'];
            $data_array[$i]['Heading']=(int)$result[$i]['Heading'];

//            $data_array[$i]['Speed']=(int)$result[$i]['Speed'];
            $data_array[$i]['Status']=(int)$result[$i]['Status'];
//            $data_array[$i]['ROT']=(int)$result[$i]['ROT'];

            $data_array[$i]['Ship_Name']=(String)$result[$i]['Ship_Name'];

//            $data_array[$i]['Call_Sign']=(String)$result[$i]['Call_Sign'];
//            $data_array[$i]['IMO_Number']=(int)$result[$i]['IMO_Number'];

            $data_array[$i]['Ship_Type']=(int)$result[$i]['Ship_Type'];

//            $data_array[$i]['Length']=(int)$result[$i]['Length'];
//            $data_array[$i]['Width']=(int)$result[$i]['Width'];
//            $data_array[$i]['Destination']=(String)$result[$i]['Destination'];
//            $data_array[$i]['ETA']=(String)$result[$i]['ETA'];
//            $data_array[$i]['Draft']=(int)$result[$i]['Draft'];
//            $data_array[$i]['Record_Datetime']=(int)$result[$i]['Record_Datetime'];
//            $data_array[$i]['Rev_Datetime']=(int)$result[$i]['Rev_Datetime'];
//            $data_array[$i]['Source_ID']=(int)$result[$i]['Source_ID'];
        }

    json_send($data_array);
}else{
    die_json_msg("没有该类型数据",10002);
}

