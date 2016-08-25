<?php 
defined('IN_DESTOON') or exit('Access Denied');
$itemid or dheader($MOD['linkurl']);
if(!check_group($_groupid, $MOD['group_show'])) include load('403.inc');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
if($item && $item['status'] > 2) {
	if($MOD['show_html'] && $item['open'] == 3 && is_file(DT_ROOT.'/'.$MOD['moduledir'].'/'.$item['linkurl'])) d301($MOD['linkurl'].$item['linkurl']);
	extract($item);
} else {
	include load('404.inc');
}
$CAT = get_cat($catid);
if(!check_group($_groupid, $CAT['group_show'])) include load('403.inc');
if($open < 3) {
	$_key = $open == 2 ? $password : $answer;
	$str = get_cookie('photo_'.$itemid);
	$pass = $str == md5(md5($DT_IP.$open.$_key.DT_KEY));	
	if($_username && $_username == $username) $pass = true;
} else {
	$pass = true;
}
if($page > $items) $page = 1;
$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
$content = $t['content'];
if($lazy) $content = img_lazy($content);
if($MOD['keylink']) $content = keylink($content, $moduleid);
$CP = $MOD['cat_property'] && $CAT['property'];
if($CP) {
	require DT_ROOT.'/include/property.func.php';
	$options = property_option($catid);
	$values = property_value($moduleid, $itemid);
}
$adddate = timetodate($addtime, 3);
$editdate = timetodate($edittime, 3);
$linkurl = $MOD['linkurl'].$linkurl;
$T = array();
$result = $db->query("SELECT itemid,thumb,introduce FROM {$table_item} WHERE item=$itemid ORDER BY listorder ASC,itemid ASC");
while($r = $db->fetch_array($result)) {
	$r['middle'] = str_replace('.thumb.', '.middle.', $r['thumb']);
	$r['big'] = str_replace('.thumb.'.file_ext($r['thumb']), '', $r['thumb']);
	$T[] = $r;
}
$demo_url = $MOD['linkurl'].itemurl($item, '{destoon_page}');
$next_photo = $items > 1 ? next_photo($page, $items, $demo_url) : $linkurl;
$prev_photo = $items > 1 ? prev_photo($page, $items, $demo_url) : $linkurl;
if($T) {
	$S = side_photo($T, $page, $demo_url);
} else {
	$S = array();
	$T[0]['thumb'] = DT_SKIN.'image/spacer.gif';
	$T[0]['introduce'] = $L['no_picture'];
}
$P = $T[$page-1];
$P['src'] = str_replace('.thumb.'.file_ext($P['thumb']), '', $P['thumb']);
$user_status = 3;
$update = '';
$fee = get_fee($item['fee'], $MOD['fee_view']);
if($fee) {
	$user_status = 4;
	$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid&page=$page";
	$description = '';
} else {
	$user_status = 3;
}
include DT_ROOT.'/include/update.inc.php';
$seo_file = 'show';
include DT_ROOT.'/include/seo.inc.php';
if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].mobileurl($moduleid, 0, $itemid, $page);
$template = 'show';
if($MOD['template_show']) $template = $MOD['template_show'];
if($CAT['show_template']) $template = $CAT['show_template'];
if($item['template']) $template = $item['template'];
if($template == 'show-ebook' || $template == 'show-ebookfull') $template = 'show';
include template($template, $module);
?>