<?php
$data = $_POST;
if(!isset($data['times'])){
    die_json_msg("参数错误",10100);
}

if(!isset($data['show_source'])){
    $show=1;
}else{
    if($data['show_source']==0||$data['show_source']==1){
        $show=$data['show_source'];
    }else{
        die_json_msg("参数错误",10100);
    }
}

//$tosecond=$times*3600;

//$db_two->select_db('ais_online_info');
$db_two=new DB;
$db_two->connect($two_mysql['server'],$two_mysql['username'],$two_mysql['password'],$two_mysql['database']);

$all_data=array();

//$once_data=array();
$times=$data['times'];
$now=time();
$j=0;
$l=0;

while($times>0){
//for($j=0;$j<$times;$j++){
    $end=$now-3600*($j)*24;
    $start=$end-3600*24;

    $hour_id+=1;

//    $sql_str="SELECT * from ais_quality_statistics_history where end_recordtime>{$start} and end_recordtime<{$end}";
    $sql_str="SELECT * from ais_quality_statistics_history where end_recordtime>{$start} and end_recordtime<={$end}";
//    var_dump($sql_str);
    $result=$db_two->get_all($sql_str);
    $data_array = array();

    if($result){
        for($i=0;$i<count($result);$i++){
            $data_array[$i]['source_id']=$result[$i]['source_id'];
            $data_array[$i]['end_recordtime']=$result[$i]['end_recordtime'];
            $data_array[$i]['nmea_total_count']=$result[$i]['nmea_total_count'];
            $data_array[$i]['nmea_avg_count']=$result[$i]['nmea_avg_count'];
            $data_array[$i]['error_percentage']=$result[$i]['error_percentage'];
            $data_array[$i]['duplicate_percentage']=$result[$i]['duplicate_percentage'];

            $total_source_id+=1;
            $nmea_count+=$data_array[$i]['nmea_total_count'];
            $message_count+=$data_array[$i]['nmea_avg_count'];

        }

        $total_array=array();
        $total_array['hour_source_id']=$hour_id;
        $total_array['total_source_id']=$total_source_id;
        $total_array['total_nmea_total_count']=$nmea_count;
        $total_array['total_nmea_avg_count']=$message_count/24;

        for($k=0;$k<count($data_array);$k++){
            $per_error_percentage+=$data_array[$k]['nmea_total_count']/$total_array['total_nmea_total_count']*$data_array[$k]['error_percentage'];
            $per_duplicate_percentage+=$data_array[$k]['nmea_total_count']/$total_array['total_nmea_total_count']*$data_array[$k]['duplicate_percentage'];
        }
        $total_array['total_error_percentage']=$per_error_percentage;
        $total_array['total_duplicate_percentage']=$per_duplicate_percentage;

        $all_data[$l]['once_all_data']=$total_array;

        if($show==0){
            $all_data[$l]['once_id_data']=$data_array;
        }

        $l+=1;
        $times-=1;
        $nmea_count=0;
        $message_count=0;
        $total_source_id=0;
        $per_error_percentage=0;
        $per_duplicate_percentage=0;
    }
//    else{
//
//    }

    $j+=1;
    if($j>48){
        json_send($all_data);
        die;
    }
}
json_send($all_data);
