<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$tb = $DT_PRE.'quote_product';
if($kw) {
	if(strlen($kw) < $DT['min_kw'] || strlen($kw) > $DT['max_kw']) message(lang($L['word_limit'], array($DT['min_kw'], $DT['max_kw'])), $MOD['linkurl'].'product.php');
	if($DT['search_limit'] && $page == 1) {
		if(($DT_TIME - $DT['search_limit']) < get_cookie('last_search')) message(lang($L['time_limit'], array($DT['search_limit'])), $MOD['linkurl'].'product.php');
		set_cookie('last_search', $DT_TIME);
	}
}
$showpage = 1;
$condition = "1";
if($keyword) $condition .= " AND title LIKE '%$keyword%'";
if($catid) $condition .= $CAT['child'] ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
$items = $db->count($tb, $condition, $DT['cache_search']);
$pages = pages($items, $page, $pagesize);
$tags = array();
$result = $db->query("SELECT * FROM {$tb} WHERE $condition ORDER BY addtime DESC LIMIT $offset,$pagesize", $DT['cache_search'] && $page == 1 ? 'CACHE' : '', $DT['cache_search']);
while($r = $db->fetch_array($result)) {
	$r['linkurl'] = $MOD['linkurl'].rewrite('price.php?itemid='.$r['itemid']);
	$tags[] = $r;
}
$head_title = $L['product_title'];
$head_title = $head_title.$DT['seo_delimiter'].$MOD['name'];
if($catid) $head_title = $CAT['catname'].$DT['seo_delimiter'].$head_title;
if($kw) $head_title = $kw.$DT['seo_delimiter'].$head_title;
include template('product', $module);
?>