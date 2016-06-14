<?php


function get_options($url,$cond=array())
{
	//判断是否是id
	$s = array('url'=>$url);
	//获得对应栏目
	$cat = model('category')->get_one($s);
	//获得栏目对应内容类型
	$item_type = preg_replace('/\_list$/','',$cat['status']);
	return model($item_type)->get_list($cond);
}

function get_all_items($s,$cond=array())
{
	//判断是否是id
	$s = (is_numeric($s))?intval($s):array('url'=>$s);
	//获得对应栏目
	$cat = model('category')->get_one($s);
	//获得栏目对应内容类型
	$item_type = preg_replace('/\_list$/','',$cat['status']);
	$children = model('category')->get_list(array('parent_id'=>$cat['category_id']));
	if (!$cond['where']) $cond['where'] = '1=1';
	if ($children && count($children) > 0)
	{
		$ids = array($cat['category_id']);
		foreach($children as $_c)
		{
			$ids[] = $_c['category_id'];
		}
		$cond['where'] .= ' AND category_id IN ('.join(',',$ids).')';
	}
	else
	{
		$cond['category_id'] = $cat['category_id'];
	}
	return model($item_type)->get_list($cond);
}

function fragment($s)
{
	$c = model('category')->get_one(array('url'=>$s));
	if ($c && $c['content']) return $c['content'];
}

/**
 * 执行sql，并返回数组
 *
 * @param string $sql 
 * @return array(array,....)
 * 2010-04-20
 */
function sql($sql)
{
	global $db;
	return $db->get_all($sql);
}

/**
 * 根据category的id或者alias获得其下的内容列表
 *
 * @param string $s category_id(int)或者alias(string)
 * @param array $cond 条件数组
 * @return array(array,...)
 * 2010-04-20
 */
function get_items($s,$cond=array())
{
	//判断是否是id
	$s = (is_numeric($s))?intval($s):array('url'=>$s);
	//获得对应栏目
	$cat = model('category')->get_one($s);
	//获得栏目对应内容类型
	$item_type = preg_replace('/\_list$/','',$cat['status']);
	
	if (!is_array($cond))
	{
		$_tmp = $cond;
		$cond = array('where'=>$_tmp);
		unset($_tmp);
	}
	$cond['category_id'] = $cat['category_id'];
	return model($item_type)->get_list($cond);
}

/**
 * 根据category的id或者 url获得其下的所有栏目
 *
 * @param string $s category_id(int)或者url(string)
 * @return array(array,...)
 * 2010-04-20
 */
function get_cats($s)
{
	return model('category')->get_cats($s);
}

/**
 * 通过category对象数组获得栏目链接
 *
 * @param array $o 栏目对象数组，比如:array('category_id'=>1,'name'=>'栏目名' ... )
 * @return string 
 * 2010-04-20
 */
function category_link($o)
{
	if ($o['status'] == 'link')
		return $o['url'];
	else if (  $o['status'] == 'page')
		return '/page/'.$o['url'].'/';
	else
	{
		if (preg_match('/^\//',$o['url']))
			return $o['url'];
		else if (preg_match('/^javascript\:/i',$o['url']))
			return $o['url'];
		else
			return '/cat/'.$o['url'].'/';
	}
}

/**
 * 通过item对象数组获得其链接
 *
 * @param array $o item对象数组，比如:array('item_id'=>1,'name'=>'xxx' ... )
 * @return string 
 * 2010-04-20
 */
function item_link($o)
{
	if ($o['product_id'])
	{
		return '/product/'.$o['product_id'].'/';
	}
}



/*
获得过去多久
比如 3秒  5小时  7天
*/
function get_past_time($t,$second='秒',$minite='分',$hour='小时',$day='天',$month='月',$year='年')
{
	$d = time()-$t;
	if ($d < 60)
	{
		return $d.$second;
	}
	$d = intval($d/60);
	if ($d < 60)
	{
		return $d.$minite;
	}
	$d = intval($d/60);
	if ($d < 24)
	{
		return $d.$hour;
	}
	$d = intval($d/24);
	if ($d < 30)
	{
		return $d.$day;
	}
	$d = intval($d/30);
	if ($d < 12)
	{
		return $d.$month;
	}
	return intval($d/12).$year;
}



function show_plaint($s)
{
	$s = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$s);
	$s = str_replace(" ","&nbsp;",$s);
	$s = str_replace("\n","<br>",$s);
	return $s;
}

function show_position($cid)
{
	$html = '<a href="/">首页</a> &gt; ';
	foreach(model('category')->position_category($cid) as $c)
	{
		$html.= '<a href="'.category_link($c).'">'.$c['name'].'</a> &gt; ';
	}
	return $html;
}


function thumb($orig_path,$mw=100,$mh=100,$method='cut',$thumb=false,$png=false)
{
	if ($method != 'box' && $method != 'cut') $method = 'cut';
	if (!$orig_path) return 'about:blank';
	
	if (preg_match('/^http\:\/\//i',$orig_path)) return $orig_path;
	
	$path = END_ROOT.$orig_path;
	
	$ftype = array_pop(explode('.',$path));
	$etag = basename($path).$method.$mw.'x'.$mh;
	$etag.= $png ? '.png':'.jpg';

	if (!file_exists($path)) return '';
	
	$thumb = dirname($path).'/'.$etag;

	if (file_exists($thumb)) return dirname($orig_path).'/'.$etag;

	if (!$imgarr=@getimagesize($path)) return ''; 
	$width_orig=$imgarr[0];
	$height_orig=$imgarr[1];

	$mime_orig=$imgarr["mime"];
	$mime=str_replace("image/","",$mime_orig);
	$mime=($mime=="bmp")?"wbmp":$mime;
	if (!function_exists("imagecreatefrom$mime")) return false;

	$p = $mw/$width_orig;
	$_p = $mh/$height_orig;
	
	if ($method == 'cut' && $_p>$p)
	{
		$p = $_p;
		$width = $mw;
		$height = $mh;
		$cut_height = 0;
		$cut_width = intval(($width_orig - $mw/$p)/2);
	}
	else if ($method == 'cut')
	{
		$width = $mw;
		$height = $mh;
		$cut_height = intval(($height_orig - $mh/$p)/2);
		$cut_width = 0;
	}
	else if ($method == 'box' && $_p<$p)
	{
		$p = $_p;
		if ($p > 1) $p = 1;
		$width = $p*$width_orig;
		$height = $p*$height_orig;
		$cut_height = 0;
		$cut_width = 0;
	}
	else if ($method == 'box')
	{
		if ($p > 1) $p = 1;
		$width = $p*$width_orig;
		$height = $p*$height_orig;
		$cut_height = 0;
		$cut_width = 0;
	}
	
	$image_p = @imagecreatetruecolor($width, $height);
	$_func = 'imagecreatefrom'.$mime;
	$image = @$_func($path);
	if ($png)
	{
		imagealphablending($image_p,true);
		$tcolor = imagecolortransparent($image_p, imagecolorallocatealpha($image_p, 0, 0, 0,127));
		imagefill($image_p, 0, 0, $tcolor);
		imagesavealpha($image_p, true);
	}
	@imagecopyresampled($image_p, $image, 0, 0, $cut_width, $cut_height, $width, $height, $width/$p, $height/$p);
	$_func = $png?'imagepng':'imagejpeg';
	$_func($image_p,$thumb,90);
	return (file_exists($thumb))?dirname($orig_path).'/'.$etag:false;
}





function get_time_by_name($name)
{
	$t = strtotime('+1 day');
	$cache = array(
		'母亲节'=>array('2012-05-13','2013-05-12','2014-05-11'),
		'父亲节'=>array('2012-06-17','2013-06-16','2014-06-15'),
		'七夕节'=>array('2012-08-23','2013-08-13','2014-08-02'),
		'春节'=>array('2012-01-23','2013-02-10','2014-01-31')
	);
	
	foreach($cache as $_j=>$arr)
	{
		if ($_j == $name)
		{
			for($i=0;$i<count($arr);$i++)
			{
				$nt = strtotime($arr[$i]);
				if ($t < $nt)
				{
					return date('Y年m月d日',$nt);
				}
			}
		}
	}
	
	$d = '';
	if ($name == '元旦节') $d = '01-01';
	if ($name == '情人节') $d = '02-14';
	if ($name == '国庆节') $d = '10-01';
	if ($name == '圣诞节') $d = '12-25';
	if ($name == '教师节') $d = '09-10';
	if ($d)
	{
		$nd = date('m-d',strtotime('+2 days'));
		$year = ($nd > $d) ? date('Y',strtotime('+1 year')) : date('Y');
		return date('Y年m月d日',strtotime($year.'-'.$d));
	}
	else
	{
		return '';
	}
}


function strtotime_cn($s)
{
	return strtotime( str_replace(array('年','月','日'),array('-','-',''),$s) );
}


function bootstrap_pager()
{
	$prev = pager_prev('&laquo;');
	$next = pager_next('&raquo;');
	$numbers = pager_numbers();

	if (!$prev)
		$prev = '<li class="disabled"><a href="javascript:;">&laquo;</a></li>';
	else
		$prev = '<li>'.$prev.'</li>';
	if (!$next)
		$next = '<li class="disabled"><a href="javascript:;">&raquo;</a></li>';
	else
		$next = '<li>'.$next.'</li>';
	$numbers = str_replace(array('<a','</a>'),array('<li><a','</a></li>'),$numbers);
	$numbers = preg_replace('/\s*\[(\d+)\]\s*/','<li class="active"><a href="javascript:;">$1</a></li>',$numbers);
	$numbers = str_replace('...','<li class="disabled"><a href="javascript:;">...</a></li>',$numbers);
	return '<div class="pagination"><ul>'.$prev.$numbers.$next.'</ul></div>';
}


function check_login()
{
	if (!$_SESSION['user'])
	{
		$url = urlencode($_SERVER['REQUEST_URI']);
		header('Location: /login/?back='.$url);
		die;
	}
}

function clear_richtext($s)
{
	$s = str_replace('</p>','</p><br>',$s);
	$s = str_replace('&nbsp;',' ',$s);
	$s = strip_tags($s,'<br>');
	$s = str_replace(array("\n","\r","\t"),'',$s);
	$s = preg_replace('/\s{2,}/',' ',$s);
	$s = preg_replace('/<br>\s*<br>/i','<br>',$s);
	$s = preg_replace('/^(\s*<br>)+/i','',$s);
	return $s;
}

function check_is_teacher()
{
	if(!$_SESSION['is_teacher'])
	{
		header("Location: index.php?p=error");
		die;
	}
}

function check_is_student()
{
	if(!$_SESSION['is_student'])
	{
		header("Location: index.php?p=error");
		die;
	}
}

function check_user_rights($right)
{
	$__user_role = model("user_role")->get_one(array('user_role_id'=>$_SESSION['user']['status']));
	if(!preg_match("/".$right."/",$__user_role['rights']))
	{
		header('Location: index.php?p=error');
	}
}

/*
* function: generate breadcrumbs html
* author: lidongxu
*/
function get_crumbs_item() {
	global $navigation;
	$crumbshtml = '';
	$crumbshtml .= "<li><a href='".$navigation['url']."'>首页</a><span class='divider'>》</span></li>";
	if ($navigation['children'][$_GET['c']]['children'][$_GET['p']]) {
		$crumbshtml .= "<li><a href='".$navigation['children'][$_GET['c']]['url']."'>".$navigation['children'][$_GET['c']]['name']."</a><span class='divider'>》</span></li>";
		$crumbshtml .= "<li clas='active'>".$navigation['children'][$_GET['c']]['children'][$_GET['p']]['name']."</li>";
	}
	else {
		$crumbshtml .= "<li clas='active'>".$navigation['children'][$_GET['c']]['name']."</li>";
	}
	return $crumbshtml;
}

/*
* function: generate head navi-item html
* author: lidongxu
*/
function get_navi_item() {
	global $navigation;
	$navihtml = '';
	foreach ($navigation['children'] as $id => $item) {
		$navihtml .= "<li class='";
		$navihtml .= ($_GET['c']==$id)?"active":"";
		$navihtml .= "'><a href='".$item['url']."'>".$item['name']."</a></li>";
	}
	return $navihtml;
}

/*
* function: generate head second navi-item html
* author: lidongxu
*/
function get_sidebar_item() {
	global $navigation;
	$itemhtml = '';
	if ($_GET['p'] != 'home') {
		if ($navigation['children'][$_GET['c']]['children']) {
			foreach ($navigation['children'][$_GET['c']]['children'] as $id => $item) {
				$itemhtml .= "<li class='";
				if ($item['type'] == 'header') {
					$itemhtml .= "nav-header'>".$item['name']."</li>";
				}
				else {
					$itemhtml .= ($_GET['p']==$id)?"active":"";
					$itemhtml .= "'><a href='".$item['url']."'>".$item['name']."</a></li>";
				}

			}
		}
	}
	else {
		if ($navigation['extension']) {
			foreach ($navigation['extension'] as $id => $item) {
				$itemhtml .= "<li class='";
				if ($item['type'] == 'header') {
					$itemhtml .= "nav-header'>".$item['name']."</li>";
				}
				else {
					$itemhtml .= ($_GET['p']==$id)?"active":"";
					$itemhtml .= "'><a href='".$item['url']."'>".$item['name']."</a></li>";
				}
			}
		}
	}
	if (!$itemhtml) {
		$itemhtml .= "<li>&nbsp;</li>";
	}
	return $itemhtml;
}

/*
* function: change timestamp to date("Y-m-d")
* author: lidongxu
*/
function inttodate($int, $format="Y-m-d") {
	return date($format, $int);
}

/*
* function: php operator '.' function
* author: lidongxu
*/
function catenate($str1, $str2) {
	return $str1.$str2;
}