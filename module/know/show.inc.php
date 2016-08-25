<?php 
defined('IN_DESTOON') or exit('Access Denied');
$itemid or dheader($MOD['linkurl']);
if(!check_group($_groupid, $MOD['group_show'])) include load('403.inc');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
if($item && $item['status'] > 2) {
	if($MOD['show_html'] && is_file(DT_ROOT.'/'.$MOD['moduledir'].'/'.$item['linkurl'])) d301($MOD['linkurl'].$item['linkurl']);
	extract($item);
} else {
	include load('404.inc');
}
$CAT = get_cat($catid);
if(!check_group($_groupid, $CAT['group_show'])) include load('403.inc');
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
$update = '';
$fee = get_fee($item['fee'], $MOD['fee_view']);
if($fee) {
	$user_status = 4;
	$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid&page=$page";
	$description = get_description($content, $MOD['pre_view']);
} else {
	$user_status = 3;
}
$answers = $best = $E = array();
if($page == 1) {
	if($aid) $best = $db->get_one("SELECT * FROM {$table}_answer WHERE itemid=$aid");
	if($best && $best['expert']) $E = $db->get_one("SELECT * FROM {$table}_expert WHERE username='$best[username]'");
}
$pages = '';
if($process == 0 || $process == 3) {
	if($MOD['answer_pagesize']) {
		$pagesize = $MOD['answer_pagesize'];
		$offset = ($page-1)*$pagesize;
	}
	$items = $answer;
	if($aid) $items--;
	if($items > 0) {
		$pages =  pages($items, $page, $pagesize, $MOD['linkurl'].itemurl($item, '{destoon_page}'));
		$result = $db->query("SELECT * FROM {$table}_answer WHERE qid=$itemid AND status=3 ORDER BY itemid ASC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			if($r['itemid'] == $aid) continue;
			$answers[] = $r;
		}
	}
}
include DT_ROOT.'/include/update.inc.php';
if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].mobileurl($moduleid, 0, $itemid, $page);
$seo_file = 'show';
include DT_ROOT.'/include/seo.inc.php';
$template = $item['template'] ? $item['template'] : ($CAT['show_template'] ? $CAT['show_template'] : 'show');
include template($template, $module);
?>