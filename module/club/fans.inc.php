<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$gid = isset($gid) ? intval($gid) : 0;
$gid or dheader($MOD['linkurl']);
$GRP = get_group($gid);
($GRP && $GRP['status'] == 3) or dheader($MOD['linkurl']);
$lists = array();
$condition = "gid='$gid' AND status=3";
$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_fans WHERE $condition");
$items = $r['num'];
$pages = pages($items, $page, $pagesize);
$result = $db->query("SELECT * FROM {$table}_fans WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
while($r = $db->fetch_array($result)) {
	$r['adddate'] = timetodate($r['addtime'], 'Y/m/d H:i');
	$lists[] = $r;
}
if($items != $GRP['fans']) $db->query("UPDATE {$table}_group SET fans='$items' WHERE itemid='$gid'");
include DT_ROOT.'/include/seo.inc.php';
$seo_title = $L['fans_title'].$seo_delimiter.$GRP['title'].$MOD['seo_name'].$seo_delimiter.$seo_page.$seo_modulename.$seo_delimiter.$seo_sitename;
include template('fans', $module);
?>