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
$maincat = get_maincat(0, $moduleid);
$video_s = $video;
$video_w = $width;
$video_h = $height;
$video_p = $player;
if(in_array(file_ext($video), array('flv', 'mp4')) && strpos($video, '?') === false) $video_p = -1;
$video_a = $MOD['autostart'] ? 'true' : 'false';
$video_i = 1;
$fee = get_fee($item['fee'], $MOD['fee_view']);
if($fee) {
	$description = '';
	$user_status = 4;
} else {
	$user_status = 3;
}
$seo_file = 'show';
include DT_ROOT.'/include/seo.inc.php';
$template = 'show';
if($MOD['template_show']) $template = $MOD['template_show'];
if($CAT['show_template']) $template = $CAT['show_template'];
if($item['template']) $template = $item['template'];
$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid";
if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].mobileurl($moduleid, 0, $itemid, $page);
ob_start();
include template($template, $module);
$data = ob_get_contents();
ob_clean();
$filename = DT_ROOT.'/'.$MOD['moduledir'].'/'.$fileurl;
if($DT['pcharset']) $filename = convert($filename, DT_CHARSET, $DT['pcharset']);
file_put($filename, $data);
return true;
?>