<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if($action == 'master') {
	$name = isset($name) ? urldecode($name) : '';
	if($name && $catid) {
		$T = $db->get_one("SELECT manager FROM {$table}_group WHERE itemid=$catid");
		if($T && $T['manager'] && strpos($T['manager'], $name) !== false) {
			$username = get_user($name, 'passport', 'username');
			if($username) dheader(userurl($username));
		}
	}
	dheader($MOD['linkurl']);
} else {
	$itemid or dheader($MOD['linkurl']);
	$R = $db->get_one("SELECT * FROM {$table}_reply WHERE itemid=$itemid");
	$R or dheader($MOD['linkurl']);
	$tid = $R['tid'];
	$T = $db->get_one("SELECT * FROM {$table} WHERE itemid=$tid");
	$T or dheader($MOD['linkurl']);
	if($MOD['reply_pagesize']) $pagesize = $MOD['reply_pagesize'];
	if($R['fid']) {
		$page = ceil($R['fid']/$pagesize);
	} else {
		$page = ceil(($T['reply']+1)/$pagesize);
	}
	if($page == 1) {
		$linkurl = $T['linkurl'];
	} else {
		$linkurl = itemurl($T, $page);
	}
	dheader($MOD['linkurl'].$linkurl.'#R'.$itemid);
}
?>