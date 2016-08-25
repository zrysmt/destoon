<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($_userid) dheader($MOD['linkurl']);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if(isset($read)) exit(include template('agreement', $module));
if(!$MOD['enable_register']) message($L['register_msg_close'], DT_PATH);
if($MOD['defend_proxy']) {
	if($_SERVER['HTTP_X_FORWARDED_FOR'] || $_SERVER['HTTP_VIA'] || $_SERVER['HTTP_PROXY_CONNECTION'] || $_SERVER['HTTP_USER_AGENT_VIA'] || $_SERVER['HTTP_CACHE_INFO'] || $_SERVER['HTTP_PROXY_CONNECTION']) {
		message(lang('include->defend_proxy'));
	}
}
if($MOD['banagent']) {
	$banagent = explode('|', $MOD['banagent']);
	foreach($banagent as $v) {
		if(strpos($_SERVER['HTTP_USER_AGENT'], $v) !== false) message($L['register_msg_agent'], DT_PATH, 5);
	}
}
if($MOD['iptimeout']) {
	$timeout = $DT_TIME - $MOD['iptimeout']*3600;
	$r = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE regip='$DT_IP' AND regtime>'$timeout'");
	if($r) message(lang($L['register_msg_ip'], array($MOD['iptimeout'])), DT_PATH);
}
if($DT['mail_type'] == 'close' && $MOD['checkuser'] == 2) $MOD['checkuser'] = 0;
require DT_ROOT.'/include/post.func.php';
require MD_ROOT.'/member.class.php';
$do = new member;
$session = new dsession();

$could_emailcode = ($MOD['emailcode_register'] && $DT['mail_type'] != 'close');
$action_sendcode = crypt_action('sendcode');
if($could_emailcode) {
	if($MOD['checkuser'] == 2) $MOD['checkuser'] = 0;
	if($action == $action_sendcode) {
		$email = isset($value) ? trim($value) : '';
		if(!is_email($email)) exit('2');
		if($do->email_exists($email)) exit('3');
		if(!$do->is_email($email)) exit('4');
		isset($_SESSION['email_send']) or $_SESSION['email_send'] = 0;
		if($_SESSION['email_time'] && (($DT_TIME - $_SESSION['email_time']) < 60)) exit('5');
		if($_SESSION['email_send'] > 9) exit('6');
		$emailcode = random(6, '0123456789');
		$_SESSION['email_save'] = $email;
		$_SESSION['email_code'] = md5($email.'|'.$emailcode);
		$_SESSION['email_time'] = $DT_TIME;
		$_SESSION['email_send'] = $_SESSION['email_send'] + 1;
		$title = $L['register_msg_emailcode'];
		$content = ob_template('emailcode', 'mail');
		send_mail($email, $title, stripslashes($content));
		exit('1');
	}
}

$could_mobilecode = ($MOD['mobilecode_register'] && $DT['sms']);
$action_sendscode = crypt_action('sendscode');
if($could_mobilecode) {
	if($action == $action_sendscode) {
		$mobile = isset($value) ? trim($value) : '';
		if(!is_mobile($mobile)) exit('2');
		isset($_SESSION['mobile_send']) or $_SESSION['mobile_send'] = 0;
		if($do->mobile_exists($mobile)) exit('3');
		if($_SESSION['mobile_time'] && (($DT_TIME - $_SESSION['mobile_time']) < 180)) exit('5');
		if($_SESSION['mobile_send'] > 4) exit('6');
		if(max_sms($mobile)) exit('6');
		$mobilecode = random(6, '0123456789');
		$_SESSION['mobile_save'] = $mobile;
		$_SESSION['mobile_code'] = md5($mobile.'|'.$mobilecode);
		$_SESSION['mobile_time'] = $DT_TIME;
		$_SESSION['mobile_send'] = $_SESSION['mobile_send'] + 1;
		$content = lang('sms->sms_code', array($mobilecode, $MOD['auth_days']*10)).$DT['sms_sign'];
		send_sms($mobile, $content);
		exit('1');
	}
}

$FD = $MFD = cache_read('fields-member.php');
$CFD = cache_read('fields-company.php');
isset($post_fields) or $post_fields = array();
if($MFD || $CFD) require DT_ROOT.'/include/fields.func.php';
$GROUP = cache_read('group.php');
if($submit) {
	if($action != crypt_action('register')) dalert($L['check_sign'].'(1)');
	$post['passport'] = isset($post['passport']) && $post['passport'] ? $post['passport'] : $post['username'];
	if($MOD['passport'] == 'uc') {
		$passport = convert($post['passport'], DT_CHARSET, $MOD['uc_charset']);
		require DT_ROOT.'/api/uc.inc.php';
		list($uid, $rt_username, $rt_password, $rt_email) = uc_user_login($passport, $post['password']);
		if($uid == -2) dalert($L['register_msg_passport'], '', 'parent.Dd("passport").focus();');
	}
	$msg = captcha($captcha, $MOD['captcha_register'], true);
	if($msg) dalert($msg, '', reload_captcha());
	$msg = question($answer, $MOD['question_register'], true);
	if($msg) dalert($msg, '', reload_question());
	$post['email'] = trim($post['email']);
	$RG = array();
	foreach($GROUP as $k=>$v) {
		if($k > 4 && $v['vip'] == 0) $RG[] = $k;
	}	
	$reload_captcha = $MOD['captcha_register'] ? reload_captcha() : '';
	$reload_question = $MOD['question_register'] ? reload_question() : '';
	in_array($post['regid'], $RG) or dalert($L['register_pass_groupid'], '', $reload_captcha.$reload_question);
	if($could_emailcode) {
		if(!preg_match("/^[0-9]{6}$/", $post['emailcode']) || $_SESSION['email_code'] != md5($post['email'].'|'.$post['emailcode'])) dalert($L['register_pass_emailcode'], '', $reload_captcha.$reload_question);
	}
	if($could_mobilecode) {
		if(!preg_match("/^[0-9]{6}$/", $post['mobilecode']) || $_SESSION['mobile_code'] != md5($post['mobile'].'|'.$post['mobilecode'])) dalert($L['register_pass_mobilecode'], '', $reload_captcha.$reload_question);
	}
	if($post['regid'] == 5) $post['company'] = $post['truename'];
	$post['groupid'] = $MOD['checkuser'] ? 4 : $post['regid'];
	$post['content'] = $post['introduce'] = $post['thumb'] = $post['banner'] = $post['catid'] = $post['catids'] = '';
	$post['edittime'] = 0;
	$inviter = get_cookie('inviter');
	$post['inviter'] = $inviter ? decrypt($inviter, DT_KEY.'INVITER') : '';
	check_name($post['inviter']) or $post['inviter'] = '';
	if($do->add($post)) {
		$userid = $do->userid;
		$username = $post['username'];
		$email = $post['email'];
		if($MFD) fields_update($post_fields, $do->table_member, $userid, 'userid', $MFD);
		if($CFD) fields_update($post_fields, $do->table_company, $userid, 'userid', $CFD);
		if($MOD['passport'] == 'uc') {
			$uid = uc_user_register($passport, $post['password'], $post['email']);
			if($uid > 0 && $MOD['uc_bbs']) uc_user_regbbs($uid, $passport, $post['password'], $post['email']);
		}
		//send sms
		if($MOD['welcome_sms'] && $DT['sms'] && is_mobile($post['mobile'])) {
			$message = lang('sms->wel_reg', array($post['truename'], $DT['sitename'], $post['username'], $post['password']));
			$message = strip_sms($message);
			send_sms($post['mobile'], $message);
		}
		//send sms
		if($MOD['checkuser'] == 2) {
			$goto = 'send.php?action=check&auth='.encrypt($email.'|'.$DT_TIME, DT_KEY.'REG');
			dalert('', '', 'parent.window.location="'.$goto.'";');
		} else if($MOD['checkuser'] == 1) {
			$forward = $MOD['linkurl'];
		} else if($MOD['checkuser'] == 0) {
			if($MOD['welcome_message'] || $MOD['welcome_email']) {
				$title = $L['register_msg_welcome'];
				$content = ob_template('welcome', 'mail');
				if($MOD['welcome_message']) send_message($username, $title, $content);
				if($MOD['welcome_email'] && $DT['mail_type'] != 'close') send_mail($email, $title, $content);
			}
		}
		if($could_emailcode) $db->query("UPDATE {$DT_PRE}member SET vemail=1 WHERE username='$username'");
		if($could_mobilecode) $db->query("UPDATE {$DT_PRE}member SET vmobile=1 WHERE username='$username'");
		$forward = 'goto.php?action=register_success&username='.$username.'&auth='.encrypt('LOGIN|'.$username.'|'.$post['password'].'|'.$DT_TIME, DT_KEY.'LOGIN').'&forward='.urlencode($forward);
		dalert('', '', 'parent.window.location="'.$forward.'"');
	} else {
		$reload_captcha = $MOD['captcha_register'] ? reload_captcha() : '';
		$reload_question = $MOD['question_register'] ? reload_question() : '';
		dalert($do->errmsg, '', $reload_captcha.$reload_question);
	}
} else {
	$COM_TYPE = explode('|', $MOD['com_type']);
	$COM_SIZE = explode('|', $MOD['com_size']);
	$COM_MODE = explode('|', $MOD['com_mode']);
	$MONEY_UNIT = explode('|', $MOD['money_unit']);
	$mode_check = dcheckbox($COM_MODE, 'post[mode][]', '', 'onclick="check_mode(this);"', 0);
	isset($auth) or $auth = '';
	$username = $password = $email = $passport = '';
	if($auth) {
		$auth = decrypt($auth, DT_KEY.'UC');
		$auth = explode('|', $auth);
		$passport = $auth[0];
		if(check_name($passport)) $username = $passport;
		$password = $auth[1];
		$email = is_email($auth[2]) ? $auth[2] : '';
		if($email) $_SESSION['regemail'] = md5(md5($email.DT_KEY.$DT_IP));
	}
	$areaid = $cityid;
	set_cookie('forward_url', $forward);
	if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].'register.php?forward='.urlencode($forward);
	$head_title = $L['register_title'];
	include template('register', $module);
}
?>