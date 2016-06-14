<?php

if ($_SESSION['user']) {
	header('Location: ?p=home');
}
else {
	header('Location: ?p=login');
}

// if ($_SESSION['is_teacher'])
// {
// 	header('Location: ?p=teacher_home');
// 	die;
// }
// if($_SESSION['is_student'])
// {
// 	header("Location: ?p=student_home");
// 	die;
// }
// print_r($_SESSION['user']);die;