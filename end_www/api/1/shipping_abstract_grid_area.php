<?php
$data = $_GET;
//$db_two->select_db('ais_online_info');
$db_two=new DB;
$db_two->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password']
    ,$two_mysql['database']);

$sql_str="SELECT * from ship_grid_count_realtime where all_count>0 and longitude>95 and longitude<130 and latitude>5 and latitude<45 ORDER BY longitude desc,latitude DESC";
$result=$db_two->get_all($sql_str);

$value_array=array();
$all_array=array();

if($result){

    $data_array = array();
    $record_time=(int)$result[0]['updatetime'];

    for($i=0;$i<count($result);$i++){
            $al_cou=(int)$result[$i]['all_count'];
//        if($al_cou>0){
            $data_array[$i]['lo']=(int)$result[$i]['longitude'];
            $data_array[$i]['la']=(int)$result[$i]['latitude'];

            $data_array[$i]['v']=(int)$result[$i]['all_count'];

//            $data_array[$i]['fishing_count']=(int)$result[$i]['fishing_count'];
//            $data_array[$i]['passenger_count']=(int)$result[$i]['passenger_count'];
//            $data_array[$i]['cargo_count']=(int)$result[$i]['cargo_count'];
//            $data_array[$i]['tanker_count']=(int)$result[$i]['tanker_count'];
//            $data_array[$i]['port_vessel_count']=(int)$result[$i]['port_vessel_count'];

//        }else{

//        }
    }

//    foreach($data_array as $value){
//        array_push($value_array,$value);
//    }

    $all_array['record_time']=$record_time;

//    $all_array['content']=$value_array;
    $all_array['content']=$data_array;

//    var_dump($value_array);
    json_send($all_array);

}else{
    die_json_msg("访问的表为空",10002);
}