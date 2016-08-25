<?php
require '../../../common.inc.php';
require 'init.inc.php';
$OAUTH[$site]['sync'] or exit;
$_token = get_cookie('qq_token');
$_openid = get_cookie('qq_openid');
if($_token && $_openid) {
	$_openid = decrypt($_openid, DT_KEY.'QQID');
	require '../post.inc.php';
	$par = 'access_token='.$_token.'&oauth_consumer_key='.QQ_ID.'&openid='.$_openid;
	$par .= '&format=xml&content='.$content;
	$headers = array();
	$pic = '';
	if($thumb) {
		if(strpos($thumb, DT_PATH) === 0) {
			$pic = str_replace(DT_PATH, DT_ROOT.'/', $thumb);
		} else {
			if($DT['remote_url'] && strpos($thumb, $DT['remote_url']) === 0) {
				$pic = DT_ROOT.'/file/temp/'.date('YmdHis', $DT_TIME).mt_rand(10, 99).$_userid.'.'.$ext;
				file_copy($thumb, $pic);
			}
		}
		if($pic) {
			if(@getimagesize($pic)) {
				if(strpos($pic, '/file/') === false || strpos($pic, '..') !== false) $pic = '';
			} else {
				if(strpos($pic, 'file/temp') !== false) file_del($pic);
				$pic = '';
			}
		}
		if($pic) {
			$headers[] = 'Expect: ';
			$tmp = array();
			foreach(explode('&', $par) as $v) {
				$t = explode('=', $v);
				$tmp[$t[0]] = $t[1];
			}
			$tmp['pic'] = '@'.$pic;
			$par = $tmp;
		}
	}
	$cur = curl_init('https://graph.qq.com/t/'.($pic ? 'add_pic_t ' : 'add_t'));
	curl_setopt($cur, CURLOPT_POST, 1);
	curl_setopt($cur, CURLOPT_POSTFIELDS, $par);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($cur, CURLOPT_HEADER, 0);
	curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, 1);
	if($headers) curl_setopt($cur, CURLOPT_HTTPHEADER, $headers);
	$rec = curl_exec($cur);
	curl_close($cur);
	if($pic && strpos($pic, 'file/temp') !== false) file_del($pic);
	#log_write($rec, 'qq', 1);
	if(strpos($rec, '<msg>ok</msg>') === false) {
		//fail
	} else {
		//success
	}
}
?>