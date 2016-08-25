<?php
require '../../../common.inc.php';
require 'init.inc.php';
$success = 0;
$DS = array();
if($_SESSION['access_token']) {
	$url = 'https://apis.live.net/v5.0/me?access_token='.$_SESSION['access_token'];
	$rec = dcurl($url);
	$arr = json_decode($rec, true);
	if(isset($arr['id'])) {
		$success = 1;
		$openid = $arr['id'];
		if($arr['first_name']) {
			$nickname = convert($arr['first_name'], 'UTF-8', DT_CHARSET);
		} else {
			$nickname = $arr['emails']['account'];
			$nickname = str_replace(strstr($nickname, '@'), '', $nickname);
		}
		$avatar = '';
		$url = $arr['link'];
		$DS = array('access_token');
	}
}
require '../destoon.inc.php';
?>