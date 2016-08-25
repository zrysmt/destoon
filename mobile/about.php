<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
require 'common.inc.php';
$table = $DT_PRE.'webpage';
if($itemid) {
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	($item && $item['item'] == 1) or mobile_msg($L['msg_not_exist']);
	$_item = $item['item'];
	unset($item['item']);
	extract($item);
	$content = video5($content);
	$editdate = timetodate($edittime, 5);
	if(!$islink) $db->query("UPDATE {$table} SET hits=hits+1 WHERE itemid=$itemid");
	$head_name = $title;
	$back_link = 'about.php';
	$foot = '';
	$head_title = $title.$DT['seo_delimiter'].$L['about_title'].$DT['seo_delimiter'].$head_title;
} else {
	$lists = array();
	$result = $db->query("SELECT * FROM {$table} WHERE item=1 ORDER BY listorder DESC,itemid DESC LIMIT 50");
	while($r = $db->fetch_array($result)) {
		$lists[] = $r;
	}
	$db->free_result($result);
	$head_name = $L['about_title'];
	$back_link = 'more.php';
	$foot = 'more';
	$head_title = $L['about_title'].$DT['seo_delimiter'].$head_title;
}
include template('about', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>