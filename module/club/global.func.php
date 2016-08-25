<?php
defined('IN_DESTOON') or exit('Access Denied');
function get_group($gid) {
	global $db, $table;
	return $gid > 0 ? $db->get_one("SELECT * FROM {$table}_group WHERE itemid=$gid") : array();
}

function is_fans($GRP) {
	global $db, $table, $_username;
	if($_username) {
		if($GRP['username'] == $_username) return true;
		if($GRP['manager'] && in_array($_username, explode('|', $GRP['manager']))) return true;
		$t = $db->get_one("SELECT * FROM {$table}_fans WHERE gid=$GRP[itemid] AND username='$_username' AND status=3");
		if($t) return true;
	}
	return false;
}

function is_admin($GRP) {
	global $_username, $_admin, $_passport;
	if($_username) {
		if($_admin == 1) return 'admin';
		if($GRP['username'] == $_username) return 'founder';
		if($GRP['manager'] && in_array($_passport, explode('|', $GRP['manager']))) return 'manager';
	}
	return '';
}
?>