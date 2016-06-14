<?php
$data = $_GET;

//$db_two->select_db('ais_online_info');
$db_two=new DB;
$db_two->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password'],$two_mysql['database']);

$sql_str="SELECT * from ais_quality_statistics_realtime";
$result=$db_two->get_all($sql_str);
$data_array = array();

if($result){
    for($i=0;$i<count($result);$i++){
        $data_array[$i]['source_id']=(int)$result[$i]['source_id'];

        $data_array[$i]['end_recordtime']=(int)$result[$i]['end_recordtime'];

        $data_array[$i]['nmea_total_count']=(int)$result[$i]['nmea_total_count'];
        $data_array[$i]['nmea_avg_count']=(int)$result[$i]['nmea_avg_count'];

        $data_array[$i]['error_percentage']=round((double)$result[$i]['error_percentage']*100,2);
        $data_array[$i]['duplicate_percentage']=round((double)$result[$i]['duplicate_percentage']*100,2);

        $source_id+=1;
        $nmea_count+=$data_array[$i]['nmea_total_count'];
        $message_count+=$data_array[$i]['nmea_avg_count'];
    }

    $total_array=array();
    $total_array['total_source_id']=$source_id;
    $total_array['total_nmea_total_count']=$nmea_count;
    $total_array['total_nmea_avg_count']=$message_count;

    for($k=0;$k<count($data_array);$k++){
        $per_error_percentage+=$data_array[$k]['nmea_total_count']/$total_array['total_nmea_total_count']*$data_array[$k]['error_percentage'];
        $per_duplicate_percentage+=$data_array[$k]['nmea_total_count']/$total_array['total_nmea_total_count']*$data_array[$k]['duplicate_percentage'];
    }

    $total_array['total_error_percentage']=round($per_error_percentage,2);
    $total_array['total_duplicate_percentage']=round($per_duplicate_percentage,2);

    $total_array['content']=$data_array;

    json_send($total_array);
}else{
    die_json_msg("没有查询到符合数据",10002);
}
