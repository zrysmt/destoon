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
	$description = $best ? get_description($best['content'], $MOD['pre_view']) : '';
	$user_status = 4;
} else {
	$user_status = 3;
}
$answers = $best = $E = array();
if($page == 1) {
	if($aid) $best = $db->get_one("SELECT * FROM {$table}_answer WHERE itemid=$aid");
	if($best && $best['expert']) $E = $db->get_one("SELECT * FROM {$table}_expert WHERE username='$best[username]'");
}
$pages = '';
$pagesize = $MOD['answer_pagesize'];
$items = $answer;
if($aid) $items--;
$total = max(ceil($items/$pagesize), 1);
if(isset($fid) && isset($num)) {
	$page = $fid;
	$topage = $fid + $num - 1;
	$total = $topage < $total ? $topage : $total;
}
$template = $item['template'] ? $item['template'] : ($CAT['show_template'] ? $CAT['show_template'] : 'show');
for(; $page <= $total; $page++) {
	$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid&page=$page";
	if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].mobileurl($moduleid, 0, $itemid, $page);
	$filename = $total == 1 ? DT_ROOT.'/'.$MOD['moduledir'].'/'.$fileurl : DT_ROOT.'/'.$MOD['moduledir'].'/'.itemurl($item, $page);
	$answers = array();
	if(($process == 0 || $process == 3) && $items) {
		$offset = ($page-1)*$pagesize;
		$pages = pages($items, $page, $pagesize, $MOD['linkurl'].itemurl($item, '{destoon_page}'));
		$result = $db->query("SELECT * FROM {$table}_answer WHERE qid=$itemid AND status=3 ORDER BY itemid ASC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			if($r['itemid'] == $aid) continue;
			$answers[] = $r;
		}
	}
	$seo_file = 'show';
	include DT_ROOT.'/include/seo.inc.php';
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