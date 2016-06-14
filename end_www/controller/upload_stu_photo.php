<?php


$user_id = $_SESSION['user']['user_id'];
if (!$user_id)
{
	show_error('登录超时');
}

$file = $_FILES['image'];

if (!$file || !$file['name'])
{
	show_error('请选择图片文件然后点击上传按钮!');
}
$ftype = getext($file['name']);

//如果是图片，那么只能上传这几种文件类型
if ($attr['type'] == 'image' || $attr['type'] == 'imagelist')
	$attr['filetype'] = array('jpg','jpeg','png','gif');

if ( !preg_match("/\*\.$ftype;/i",'*.jpg;*.gif;*.png;*.jpeg;') )
{
	show_error('只能上传图片文件!');
}
else
{
	$file_url = $file['name'];
	
	$file_url = date('Y_m_d_H_i_s_').rand(1111,9999).'.'.$ftype;


	end_mkdir(END_ROOT.END_UPLOAD_DIR);
	$file_url = END_UPLOAD_DIR.$file_url;

	if (!is_writable(END_ROOT.dirname($file_url)))
	{
		show_error('上传文件目录不可写!');
	}
	
	if (@move_uploaded_file($file["tmp_name"],END_ROOT.$file_url))
	{
		include_once(END_ROOT.'end_system/library/image.php');
		$img = new Image;
		$img->filepath = END_ROOT.$file_url;
		$img->resize_width(200);
	}
	
	if (!file_exists(END_ROOT.$file_url))
	{
		show_error('上传错误!');
	}

	if (!model('user')->update($user_id, array('avatar'=>$file_url)))
	{
		show_error('更新数据库错误');
	}

?>
<script>
parent.upload_photo_callback(<?php echo json_encode($file_url);?>);
</script>
<?php
}


function show_error($s)
{
?>
<script>
alert(<?php echo json_encode($s);?>);
</script>
<?php
	die;
}


function getext($filename)
{
	$filename = trim(strtolower(basename($filename)));
	$arr = explode('.',$filename);
	$type = $arr[count($arr)-1];
	return $type;
}