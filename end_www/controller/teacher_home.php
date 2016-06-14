<?php
//检查是否是非学生，函数在common中
check_is_teacher();

$view_data['hottest'] = model('activity')->get_list(array('total'=>5,'status'=>1,'order'=>'participants desc,create_time desc'));


$view_data['newest'] = model('activity')->get_list(array('total'=>5,'status'=>1));

$view_data['suggested'] = model('activity')->get_list(array('suggested'=>'yes','total'=>5,'status'=>1));