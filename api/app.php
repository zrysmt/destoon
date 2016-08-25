<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2016 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
$_COOKIE = array();
require '../common.inc.php';
$url = $EXT['mobile_url'];
$ip = '';
if(isset($_SERVER['HTTP_CLIENTIP']) && is_ip($_SERVER['HTTP_CLIENTIP']) && $_SERVER['HTTP_CLIENTIP'] != $DT_IP) $ip .= ','.$_SERVER['HTTP_CLIENTIP'];
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip .= ','.$_SERVER['HTTP_X_FORWARDED_FOR'];
if($ip) $ip = substr($ip, 1);
if($DT_MOB['os'] == 'ios') {
	if(preg_match("/^([0-9]{1,})@([a-z0-9]{16,})$/i", $EXT['mobile_ios'])) {
		$t = explode('@', $EXT['mobile_ios']);
		dheader('http://app.destoon.com/get.php?o=ios&u='.$t[0].'&k='.encrypt($url, $t[1]).'&i='.($ip ? encrypt($ip, $t[1]) : ''));
	}
} else if($DT_MOB['os'] == 'android') {
	if(preg_match("/^([0-9]{1,})@([a-z0-9]{16,})$/i", $EXT['mobile_adr'])) {
		$t = explode('@', $EXT['mobile_adr']);
		dheader('http://app.destoon.com/get.php?o=adr&u='.$t[0].'&k='.encrypt($url, $t[1]).'&i='.($ip ? encrypt($ip, $t[1]) : ''));
	}
}
dheader($url);
?>