<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$head_title = $L['goto'];
$email = isset($email) && is_email($email) ? $email : '';
if($email) {
	$tmp = explode('@', $email);
	$url = str_replace('vip.', '', $tmp[1]);
	$url = 'http://mail.'.$url;
} else {
	if($action == 'register_success') {
		$url = $DT['file_login'].'?auth='.$auth.'&forward='.urlencode($forward);
	} else {
		$url = 'http://';
	}
}
include template('goto', $module);
?>