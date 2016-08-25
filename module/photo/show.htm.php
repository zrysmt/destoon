<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(!$MOD['show_html'] || !$itemid) return false;
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
if(!$item || $item['status'] < 3) return false;
extract($item);
$CAT = get_cat($catid);
$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
$content = $t['content'];
if($lazy) $content = img_lazy($content);
if($MOD['keylink']) $content = keylink($content, $moduleid);
$CP = $MOD['cat_property'] && $CAT['property'];
if($CP) {
	require_once DT_ROOT.'/include/property.func.php';
	$options = property_option($catid);
	$values = property_value($moduleid, $itemid);
}
$adddate = timetodate($addtime, 3);
$editdate = timetodate($edittime, 3);
$fileurl = $linkurl;
$linkurl = $MOD['linkurl'].$linkurl;
$fee = get_fee($item['fee'], $MOD['fee_view']);
if($fee) {
	$description = '';
	$user_status = 4;
} else {
	$user_status = 3;
}
$pass = $open == 3 ? true : false;
$T = array();
$result = $db->query("SELECT itemid,thumb,introduce FROM {$table_item} WHERE item=$itemid ORDER BY listorder ASC,itemid ASC");
while($r = $db->fetch_array($result)) {
	$r['middle'] = str_replace('.thumb.', '.middle.', $r['thumb']);
	$r['big'] = str_replace('.thumb.'.file_ext($r['thumb']), '', $r['thumb']);
	$T[] = $r;
}
$demo_url = $MOD['linkurl'].itemurl($item, '{destoon_page}');
$total = $items = count($T);
$user_status = 3;
$seo_file = 'show';
include DT_ROOT.'/include/seo.inc.php';
$template = 'show';
if($MOD['template_show']) $template = $MOD['template_show'];
if($CAT['show_template']) $template = $CAT['show_template'];
if($item['template']) $template = $item['template'];
if($template == 'show-ebook' || $template == 'show-ebookfull') include DT_ROOT.'/api/flashpageflip/make.inc.php';
for(; $page <= $total; $page++) {
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
	$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid&page=$page";
	if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].mobileurl($moduleid, 0, $itemid, $page);
	$filename = $total == 1 ? DT_ROOT.'/'.$MOD['moduledir'].'/'.$fileurl : DT_ROOT.'/'.$MOD['moduledir'].'/'.itemurl($item, $page);
	ob_start();
	include template($template, $module);
	$data = ob_get_contents();
	ob_clean();
	if($DT['pcharset']) $filename = convert($filename, DT_CHARSET, $DT['pcharset']);
	file_put($filename, $data);
	if($page == 1 && $total > 1) {
		$indexname = DT_ROOT.'/'.$MOD['moduledir'].'/'.itemurl($item, 0);
		if($DT['pcharset']) $indexname = convert($indexname, DT_CHARSET, $DT['pcharset']);
		file_copy($filename, $indexname);
	}
}
return true;
?>