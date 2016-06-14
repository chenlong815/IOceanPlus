<?php
//暂无用处
$url = $_GET['url'];
if (!$url) die('404');
$url = urldecode($url);

$cat = model('category')->get_one(array('url'=>$url));
if (!$cat || $cat['status'] != 'folder') die('404');


$children = model('category')->get_list(array('parent_id'=>$cat['category_id']));
if (!$children)
{
	$children = model('category')->get_list(array('parent_id'=>$cat['parent_id']));
	$view_data['parent'] = model('category')->get_one($cat['parent_id']);
	if ($view_data['parent']['url'] == 'navigations') $view_data['parent'] = $cat;
	if ($view_data['parent']['category_id'] == 0)
	{
		$view_data['parent'] = $cat;
		$children = get_cats('navigations');
	}
}
else
{
	$view_data['parent'] = $cat;
}
$view_data['children'] = $children;

$view_data['cat'] = $cat;

$view_data['title'] = $cat['page_title']?$cat['page_title']:$cat['name'];
$view_data['description'] = $cat['description'];
$view_data['keywords'] = $cat['keywords'];
