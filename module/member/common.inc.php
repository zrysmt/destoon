<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
define('MD_ROOT', DT_ROOT.'/module/'.$module);
require MD_ROOT.'/global.func.php';
require DT_ROOT.'/include/module.func.php';
if(defined('DT_ADMIN')) {
	$GROUP = cache_read('group.php');
} else {
	if($submit) {
		check_post() or dalert($L['bad_data']);//safe
		$BANWORD = cache_read('banword.php');
		if($BANWORD && isset($post)) {
			$keys = array('title', 'tag', 'introduce', 'content');
			foreach($keys as $v) {
				if(isset($post[$v])) $post[$v] = banword($BANWORD, $post[$v]);
			}
		}
	}
	$group_editor = $MG['editor'];
	in_array($group_editor, array('Default', 'Destoon', 'Simple', 'Basic')) or $group_editor = 'Destoon';
	$show_menu = $MOD['show_menu'] ? true : false;
	$show_oauth = $MOD['oauth'];

	$MYMODS = array();
	if(isset($MG['moduleids']) && $MG['moduleids']) {
		$MYMODS = explode(',', $MG['moduleids']);
	}
	if($MYMODS) {
		foreach($MYMODS as $k=>$v) {
			$v = abs($v);
			if(!isset($MODULE[$v])) unset($MYMODS[$k]);
		}
	}
	$MENUMODS = $MYMODS;
	if($show_menu) {
		$MENUMODS = array();
		foreach($MODULE as $m) {
			if($m['islink']) continue;
			if($m['moduleid'] > 4 && $m['moduleid'] != 11) $MENUMODS[] = $m['moduleid'];
			if($m['moduleid'] == 9 && in_array(-9, $MYMODS)) $MENUMODS[] = -9;
		}
	}	
}
isset($admin_user) or $admin_user = false;
//$AREA = cache_read('area.php');
$table = $DT_PRE.'member';
$table_company = $DT_PRE.'company';
?>