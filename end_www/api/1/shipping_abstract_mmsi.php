<?php
$data = $_POST;
if(!isset($data['mmsi'])){
    die_json_msg("参数错误",10100);
}

$db_two=new DB;
$db_two->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password'],$two_mysql['database']);

$mmsi=$data['mmsi'];

$data_array = array();

$sql_str="select * from ais_ship_realtime where MMSI={$mmsi}";

//echo $sql_str;
//die;

    $result=$db_two->get_one($sql_str);

    if($result){

            $data_array['MMSI']=(int)$result['MMSI'];
            $data_array['Longitude']=round((int)$result['Longitude']/600000,2);
            $data_array['Latitude']=round((int)$result['Latitude']/600000,2);
            $data_array['Direction']=(int)$result['Direction'];
            $data_array['Heading']=(int)$result['Heading'];

            $data_array['Speed']=(int)$result['Speed'];
            $data_array['Status']=(int)$result['Status'];
            $data_array['ROT']=(int)$result['ROT'];

            $data_array['Ship_Name']=(String)$result['Ship_Name'];

            $data_array['Call_Sign']=(String)$result['Call_Sign'];
            $data_array['IMO_Number']=(int)$result['IMO_Number'];

            $data_array['Ship_Type']=(int)$result['Ship_Type'];

            $data_array['Length']=(int)$result['Length'];
            $data_array['Width']=(int)$result['Width'];
            $data_array['Destination']=(String)$result['Destination'];
            $data_array['ETA']=(String)$result['ETA'];
            $data_array['Draft']=(int)$result['Draft'];
            $data_array['Record_Datetime']=(int)$result['Record_Datetime'];
            $data_array['Rev_Datetime']=(int)$result['Rev_Datetime'];
            $data_array['Source_ID']=(int)$result['Source_ID'];

            $data_array['Dynamic_Count']=(int)$result['Dynamic_Count'];
            $data_array['Dynamic_Interval']=(int)$result['Dynamic_Interval'];
            $data_array['Static_Count']=(int)$result['Static_Count'];
            $data_array['Static_Interval']=(int)$result['Static_Interval'];

    json_send($data_array);
}else{
    die_json_msg("没有该类型数据",10002);
}

