<?php
defined('IN_DESTOON') or exit('Access Denied');
if(!$MOD['list_html'] || !$catid) return false;
$CAT or $CAT = get_cat($catid);
if(!$CAT) return false;
unset($CAT['moduleid']);
extract($CAT);
$maincat = get_maincat($child ? $catid : $parentid, $moduleid);
$condition = "groupid>5 and catids like '%,".$catid.",%'";
if($page == 1) {
	$items = $db->count($table, $condition);
	if($items != $CAT['item']) {
		$CAT['item'] = $items;
		$db->query("UPDATE {$DT_PRE}category SET item=$items WHERE catid=$catid");
	}
} else {
	$items = $CAT['item'];
}
$pagesize = $MOD['pagesize'];
$showpage = 1;
$template = $CAT['template'] ? $CAT['template'] : 'list';
$total = max(ceil($items/$MOD['pagesize']), 1);
if(isset($fid) && isset($num)) {
	$page = $fid;
	$topage = $fid + $num - 1;
	$total = $topage < $total ? $topage : $total;
}
for(; $page <= $total; $page++) {
	$offset = ($page-1)*$pagesize;
	$pages = listpages($CAT, $items, $page, $pagesize);
	$tags = $_tags = $ids = array();
	if($items) {
		$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE {$condition} ORDER BY ".$MOD['order']." LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			if($lazy && isset($r['thumb']) && $r['thumb']) $r['thumb'] = DT_SKIN.'image/lazy.gif" original="'.$r['thumb'];
			$tags[] = $r;
		}
	}
	$seo_file = 'list';
	include DT_ROOT.'/include/seo.inc.php';
	$destoon_task = "moduleid=$moduleid&html=list&catid=$catid&page=$page";
	if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].mobileurl($moduleid, $catid, 0, $page);
	$filename = DT_ROOT.'/'.$MOD['moduledir'].'/'.listurl($CAT, $page);
	ob_start();
	include template($template, $module);
	$data = ob_get_contents();
	ob_clean();
	if($DT['pcharset']) $filename = convert($filename, DT_CHARSET, $DT['pcharset']);
	file_put($filename, $data);
	if($page == 1) {
		$indexname = DT_ROOT.'/'.$MOD['moduledir'].'/'.listurl($CAT, 0);
		if($DT['pcharset']) $indexname = convert($indexname, DT_CHARSET, $DT['pcharset']);
		file_copy($filename, $indexname);
	}
}
return true;
?>