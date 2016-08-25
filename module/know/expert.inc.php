<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
isset($username) or $username = '';
if($itemid || check_name($username)) {
	$item = $db->get_one("SELECT * FROM {$table}_expert WHERE ".($itemid ? "itemid=$itemid" : "username='$username'"));
	if($item) {
		extract($item);
	} else {
		include load('404.inc');
	}
	$rate = ($answer && $best < $answer) ? dround($best*100/$answer, 2, true).'%' : '100%';
	if($page == 1) $db->query("UPDATE {$table}_expert SET hits=hits+1 WHERE itemid=$itemid");
	include DT_ROOT.'/include/seo.inc.php';
	$seo_title = $title.$seo_delimiter.$L['expert_title'].$seo_delimiter.$seo_page.$seo_modulename.$seo_delimiter.$seo_sitename;
} else {
	include DT_ROOT.'/include/seo.inc.php';
	$seo_title = $L['expert_title'].$seo_delimiter.$seo_page.$seo_modulename.$seo_delimiter.$seo_sitename;
}
include template('expert', $module);
?>