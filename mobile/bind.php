<?php
require 'common.inc.php';
$avatar = '';
if(!$_userid) {
	$auth = decrypt(get_cookie('bind'), DT_KEY.'BIND');
	$openid = decrypt(get_cookie('weixin_openid'), DT_KEY.'WXID');
	if(is_openid($openid) && $DT_MOB['browser'] == 'weixin') {
		$U = $db->get_one("SELECT * FROM {$DT_PRE}weixin_user WHERE openid='$openid'");
		if($U) {
			$OAUTH = cache_read('oauth.php');
			$nohead = DT_PATH.'api/weixin/image/headimg.jpg';
			$avatar = $U['headimgurl'] ? $U['headimgurl'] : $nohead;
			$nickname = $U['nickname'] ? $U['nickname'] : 'USER';
			$site = $OAUTH['wechat']['name'];
			$connect = 'weixin.php?action=connect';
		}
	} else if(strpos($auth, '|') !== false) {
		$t = explode('|', $auth);
		$itemid = intval($t[0]);
		$U = $db->get_one("SELECT * FROM {$DT_PRE}oauth WHERE itemid=$itemid");
		if($U && $U['site'] = $t[1]) {
			$OAUTH = cache_read('oauth.php');
			$nohead = DT_PATH.'api/oauth/avatar.png';
			$avatar = $U['avatar'] ? $U['avatar'] : $nohead;
			$nickname = $U['nickname'] ? $U['nickname'] : 'USER';
			$site = $OAUTH[$U['site']]['name'];
			$connect = DT_PATH.'api/oauth/'.$U['site'].'/connect.php';
		}
	}
}
if($avatar) {
	$head_title = $L['bind_title'].$DT['seo_delimiter'].$head_title;
	$foot = 'my';
	include template('bind', 'mobile');
	if(DT_CHARSET != 'UTF-8') toutf8();
	exit;
}
dheader('my.php?reload='.$DT_TIME);
?>