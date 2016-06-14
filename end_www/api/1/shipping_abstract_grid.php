<?php
$data = $_GET;

//$db_two->select_db('ais_online_info');
$db_two=new DB;
$db_two->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password'],$two_mysql['database']);

$sql_str="SELECT * from ship_grid_count_realtime where all_count>0";

$result=$db_two->get_all($sql_str);
$data_array = array();
$country_count=0;

if($result){
    for($i=0;$i<count($result);$i++){

        $data_array[$i]['longitude']=(int)$result[$i]['longitude'];
        $data_array[$i]['latitude']=(int)$result[$i]['latitude'];

        $data_array[$i]['recordtime']=(int)$result[$i]['updatetime'];

        $data_array[$i]['all_count']=(int)$result[$i]['all_count'];

        $data_array[$i]['fishing_count']=(int)$result[$i]['fishing_count'];
        $data_array[$i]['passenger_count']=(int)$result[$i]['passenger_count'];
        $data_array[$i]['cargo_count']=(int)$result[$i]['cargo_count'];
        $data_array[$i]['tanker_count']=(int)$result[$i]['tanker_count'];
        $data_array[$i]['port_vessel_count']=(int)$result[$i]['port_vessel_count'];

//        $country_count+=1;
//        $total_count+=$data_array[$i]['count'];
    }

//    $total_array=array();
//    $total_array['country_count']=$country_count;
//    $total_array['total_count']=$total_count;

    json_send($data_array);
    //json_send(array('type_count'=>$type_count,'total_count'=>$total_count,$data_array));

    //json_send($data_array);
}else{
    die_json_msg("访问的表为空",10002);
}
