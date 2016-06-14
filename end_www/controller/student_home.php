<?php
//检查是否是学生，函数在common中
check_is_student();

include_once("stu_polar_avg.php");
$stu_activities = model("data")->get_list(array('stuid'=>$stuid));

foreach($stu_activities as $k=>$v)
{
	$activity_id = $db->get_one("select activity_id from end_event where event_id=".$v['event_id']." order by create_time desc limit 6");
	$name = model('activity')->get_one(array('select'=>'name','activity_id'=>$activity_id['activity_id']));
	$stu_activities[$k]['name'] = $name['name'];
}
$view_data['activities'] = $stu_activities;
$rank = $db->get_all("select *,count(1),sum(score),sum(score)/count(1) as avg from end_data group by stuid order by avg desc");
if($rank)
{
	$user_data = array();
	$user_rank = 0;
	foreach($rank as $k=>$v)
	{
		if($v['stuid']==$stuid)
		{
			$user_data = $v;
			$user_rank = $k+1;
			break;
		}
	}
	$total_stu_num = count($rank);
	$view_data['total_score'] = round($user_data['avg'],2);
	$rank_rate = round($user_rank/$total_stu_num,2);
	// echo $rank_rate;die;
	if($rank_rate<0.8 && $rank_rate>0)
	{
		$rank_rate = '超越了'.((1-$rank_rate)*100)."%的同学";
	}
	else if($rank_rate==0)
	{
		$rank_rate = '';
	}
	else
	{
		$rank_rate = '较为靠后';
	}
	$view_data['rank_rate'] = $rank_rate;
}