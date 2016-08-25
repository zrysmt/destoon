<?php
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$destoon_task = rand_task();
$head_title = $L['view_title'];
$pass = $img;
if(strpos($img, DT_DOMAIN ? DT_DOMAIN : DT_PATH) !== false) {
	$pass = true;
} else {
	if($DT['remote_url'] && strpos($img, $DT['remote_url']) !== false) {
		$pass = true;
	} else {
		$pass = false;
	}
}
$pass or dheader($img);
$ext = file_ext($img);
in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp')) or dheader(DT_PATH);
$img = str_replace(array('.thumb.'.$ext, '.middle.'.$ext), array('', ''), $img);
include template('view', $module);
?>