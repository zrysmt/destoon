<?php
defined('IN_DESTOON') or exit('Access Denied');
header("Content-Type:text/html;charset=UTF-8");
if($EXT['mobile_enable']) {
	$str = $EXT['mobile_sitename'] ? $EXT['mobile_sitename'] : $DT['sitename'];
	$str .= '#DESTOON#';
	$str .= $EXT['mobile_url'];
	echo convert($str, DT_CHARSET, 'UTF-8');
} else {
	echo '0';
}
?>