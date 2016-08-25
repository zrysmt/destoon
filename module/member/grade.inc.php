<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if($action == crypt_action('promo')) {
	$code = dhtmlspecialchars(trim($code));
	if($code) {
		$p = $db->get_one("SELECT * FROM {$DT_PRE}finance_promo WHERE number='$code' AND totime>$DT_TIME");
		if($p && ($p['reuse'] || (!$p['reuse'] && !$p['username']))) {
			if($p['type']) {
				exit(lang($L['grade_msg_time_promo'], array($p['amount'])));
			} else {
				exit(lang($L['grade_msg_money_promo'], array($p['amount'])));
			}
		}
	}
	exit($L['grade_msg_bad_promo']);
}
require DT_ROOT.'/include/post.func.php';
$GROUP = cache_read('group.php');
$groupid = isset($groupid) ? intval($groupid) : 0;
isset($GROUP[$groupid]) or $groupid = 0;
$UP = $UG = array();
if($_groupid > 2) {
	foreach($GROUP as $k=>$v) {
		if($v['listorder'] > $MG['listorder']) $UP[$k] = $v;
	}
}
array_key_exists($groupid, $UP) or $groupid = 0;
$fee = 0;
$need_fee = false;
$could_up = $groupid;
if($groupid) {
	$UG = cache_read('group-'.$groupid.'.php');
	$fee = $UG['fee'];
	if($_userid && $fee) $need_fee = true;
}
if($_userid) {
	$r = $db->get_one("SELECT status FROM {$DT_PRE}upgrade WHERE userid=$_userid ORDER BY itemid DESC");
	if($r && $r['status'] == 2) $could_up = false;
	if($_groupid < 5) $could_up = false;
} else {
	$r = $db->get_one("SELECT addtime FROM {$DT_PRE}upgrade WHERE ip='$DT_IP' ORDER BY itemid DESC");
	if($r && $DT_TIME - $r['addtime'] < 86400) $could_up = false;
}
if($submit && $could_up) {
	if(strlen($company) < 4) message($L['grade_pass_company']);
	if(strlen($truename) < 2) message($L['grade_pass_truename']);
	if(strlen($telephone) < 6) message($L['grade_pass_telephone']);
	$amount = $promo_type = $promo_amount = 0;
	if($fee) {
		if($promo_code) {
			$p = $db->get_one("SELECT * FROM {$DT_PRE}finance_promo WHERE number='$promo_code' AND totime>$DT_TIME");
			if($p && ($p['reuse'] || (!$p['reuse'] && !$p['username']))) {
				$promo_type = $p['type'];
				$promo_amount = $p['amount'];
			} else {
				$promo_code = '';
			}
		}
		if($promo_code) {
			if($promo_type) {
				//
			} else {
				if($fee > $promo_amount) {
					$amount = $fee - $promo_amount;
					if($_money > $amount) {
						money_add($_username, -$amount);
						money_record($_username, -$amount, $L['in_site'], 'system', $L['grade_title'], $GROUP[$groupid]['groupname']);
					} else {
						$amount = 0;
					}
				} else {
					$promo_amount = $fee;
					$amount = 0;
				}
			}
			$db->query("UPDATE {$DT_PRE}finance_promo SET username='$_username',ip='$DT_IP',".($p['reuse'] ? "updatetime=updatetime+1" : "updatetime='$DT_TIME'")." WHERE number='$promo_code'");
		} else {
			if($_money > $fee) {
				$amount = $fee;
				money_add($_username, -$amount);
				money_record($_username, -$amount, $L['in_site'], 'system', $L['grade_title'], $GROUP[$groupid]['groupname']);
			}
		}
	}
	$company = dhtmlspecialchars(trim($company));
	$truename = dhtmlspecialchars(trim($truename));
	$telephone = dhtmlspecialchars(trim($telephone));
	$mobile = dhtmlspecialchars(trim($mobile));
	$email = dhtmlspecialchars(trim($email));
	$msn = dhtmlspecialchars(trim($msn));
	$qq = dhtmlspecialchars(trim($qq));
	$ali = dhtmlspecialchars(trim($ali));
	$skype = dhtmlspecialchars(trim($skype));
	$content = dhtmlspecialchars(trim($content));
	$db->query("INSERT INTO {$DT_PRE}upgrade (userid,username,groupid,company,truename,telephone,mobile,email,msn,qq,ali,skype,content,addtime,ip,amount,promo_code,promo_type,promo_amount,status) VALUES ('$_userid','$_username', '$groupid','$company','$truename','$telephone','$mobile','$email','$msn','$qq','$ali','$skype','$content', '$DT_TIME', '$DT_IP','$amount','$promo_code','$promo_type','$promo_amount','2')");
	 message($L['grade_msg_success'], DT_PATH, 5);
} else {
	$GROUPS = array();
	foreach($GROUP as $k=>$v) {
		if($k > 4) {
			$G = cache_read('group-'.$k.'.php');
			$G['moduleids'] = isset($G['moduleids']) ? explode(',', $G['moduleids']) : array();
			if($G['grade']) $GROUPS[$k] = $G;
		}
	}
	
	$cols = count($GROUPS)+1;
	$percent = dround(100/$cols).'%';
	$company = $truename = $email = $mobile = $telephone = $msn = $qq = $ali = $skype = '';
	if($_userid) {
		$user = userinfo($_username);
		$company = $user['company'];
		$truename = $user['truename'];
		$email = $user['email'];
		$mobile = $user['mobile'];
		$telephone = $user['telephone'];
		$msn = $user['msn'];
		$qq = $user['qq'];
		$ali = $user['ali'];
		$skype = $user['skype'];
	}
	$DM = $MODULE;
	$DM[9]['name'] = $L['job_name'];
	$DM[-9]['moduleid'] = -9;
	$DM[-9]['name'] = $L['resume_name'];
	$DM[-9]['linkurl'] = $DM[9]['linkurl'];
	$head_title = $L['grade_title'];
	include template('grade', $module);
}
?>