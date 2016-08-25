<?php
require '../common.inc.php';
$DT_MOB['browser'] == 'weixin' or exit('Not IN WeiXin');
if($action == 'login') {
	$openid = get_cookie('weixin_openid');
	if($openid) {
		$openid = decrypt($openid, DT_KEY.'WXID');
		if(is_openid($openid)) {
			$r = $db->get_one("SELECT username FROM {$DT_PRE}weixin_user WHERE openid='$openid'");
			if($r) {
				if($r['username']) {
					include load('member.lang');
					$MOD = cache_read('module-2.php');
					include DT_ROOT.'/include/module.func.php';
					include DT_ROOT.'/module/member/member.class.php';
					$do = new member;
					$user = $do->login($r['username'], '', 0, true);
				} else {
					dheader('bind.php');
				}
			}
		}
	}
	$url = get_cookie('weixin_url');
	dheader($url ? $url : 'my.php');
} else if($action == 'member') {
	isset($auth) or $auth = '';
	if($auth) {
		$openid = decrypt($auth, DT_KEY.'WXID');
		if(is_openid($openid)) {
			set_cookie('weixin_openid', $auth);
			set_cookie('weixin_url', 'my.php');
			dheader('weixin.php?action=login&reload='.$DT_TIME);
		}
	}
} else if($action == 'callback') {
	if($code) {
		include DT_ROOT.'/api/weixin/config.inc.php';
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.WX_APPID.'&secret='.WX_APPSECRET.'&code='.$code.'&grant_type=authorization_code';
		$rec = dcurl($url);
		$arr = json_decode($rec, true);
		if($arr['openid'] && is_openid($arr['openid'])) {
			$openid = $arr['openid'];
			$r = $db->get_one("SELECT * FROM {$DT_PRE}weixin_user WHERE openid='$openid'");
			if($r) {
				$itemid = $r['itemid'];
				if($r['nickname']) $arr['access_token'] = '';
			} else {
				$db->query("INSERT INTO {$DT_PRE}weixin_user (openid,subscribe,addtime,edittime) VALUES ('$openid','2','$DT_TIME',,'$DT_TIME')");
				$itemid = $db->insert_id();
			}
			if($arr['access_token']) {
				$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$openid.'&lang=zh_CN';
				$rec = dcurl($url);
				$info = json_decode($rec, true);
				$sql = "edittime=$DT_TIME";
				if(isset($info['nickname'])) {
					foreach(array('nickname', 'sex', 'city', 'province', 'country', 'language', 'headimgurl') as $v) {
						if(isset($info[$v])) $sql .= ",".$v."='".addslashes($info[$v])."'";
					}
				}
				$db->query("UPDATE {$DT_PRE}weixin_user SET $sql WHERE itemid=$itemid");
			}
			set_cookie('weixin_openid', encrypt($openid, DT_KEY.'WXID'));
			dheader('weixin.php?action=login&reload='.$DT_TIME);
		}
	}
} else {
	isset($url) or $url = 'my.php';
	if($moduleid > 2) $url = mobileurl($moduleid);
	if($_userid) dheader($url);
	set_cookie('weixin_url', $url);
	if(get_cookie('weixin_openid')) dheader('weixin.php?action=login&reload='.$DT_TIME);
	include DT_ROOT.'/api/weixin/config.inc.php';
	$scope = $action == 'connect' ? 'snsapi_userinfo' : 'snsapi_base';
	dheader('https://open.weixin.qq.com/connect/oauth2/authorize?appid='.WX_APPID.'&redirect_uri='.urlencode($EXT['mobile_url'].'weixin.php?action=callback').'&response_type=code&scope='.$scope.'&state=1#wechat_redirect');
}
dheader('index.php?reload='.$DT_TIME);
?>