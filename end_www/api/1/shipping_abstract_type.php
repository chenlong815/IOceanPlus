<?php
$data = $_GET;

//$db_two->select_db('ais_online_info');
$db_two=new DB;
$db_two->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password'],$two_mysql['database']);

$sql_str="SELECT * from ship_type_realtime";

$result=$db_two->get_all($sql_str);
$data_array = array();
$type_count=0;
if($result){
    for($i=0;$i<count($result);$i++){
        $data_array[$i]['ship_type']=(int)$result[$i]['ship_type'];
        $data_array[$i]['recordtime']=(int)$result[$i]['updatetime'];
        $data_array[$i]['count']=(int)$result[$i]['count'];

        $type_count+=1;
        $total_count+=$data_array[$i]['count'];
    }

    $total_array=array();
    $total_array['type_count']=$type_count;
    $total_array['total_count']=$total_count;
    $total_array['content']=$data_array;

    json_send($total_array);
    //json_send(array('type_count'=>$type_count,'total_count'=>$total_count,$data_array));

    //json_send($data_array);
}else{
    die_json_msg("访问的表为空",10002);
}
