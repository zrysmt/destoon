<?php
require '../../../common.inc.php';
require 'init.inc.php';
$_REQUEST['code'] or dalert('Error Request.', $MODULE[2]['linkurl'].$DT['file_login'].'?step=callback&site='.$site);
$par = 'client_id='.urlencode(WRAP_CLIENT_ID)
	 . '&redirect_uri='.urlencode(WRAP_CALLBACK)
	 . '&client_secret='.urlencode(WRAP_CLIENT_SECRET)
	 . '&code='.urlencode($_REQUEST['code'])
	 . '&grant_type=authorization_code';
$rec = dcurl(WRAP_ACCESS_URL, $par);
if(strpos($rec, 'access_token') !== false) {
	$arr = json_decode($rec, true);
	$_SESSION['access_token'] = $arr['access_token'];
	dheader('index.php?time='.$DT_TIME);
} else {
	dalert('Error Token.', $MODULE[2]['linkurl'].$DT['file_login'].'?step=token&site='.$site);
}
?>