<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
$moduleid = 2;
require 'common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
!$_userid or dheader('my.php?reload='.$DT_TIME);
$could_email = $DT['mail_type'] == 'close' ? 0 : 1;
$could_mobile = $DT['sms'] ? 1 : 0;
require DT_ROOT.'/include/post.func.php';
$session = new dsession();
$head_name = $L['forgot_title'];
$head_title = $head_name.$DT['seo_delimiter'].$head_title;
switch($action) {
	case 'success':
		(isset($_SESSION['f_uid']) && isset($_SESSION['f_key'])) or mobile_msg($L['msg_error']);
		$userid = intval($_SESSION['f_uid']);
		$t = $db->get_one("SELECT email,mobile,vmobile,groupid FROM {$DT_PRE}member WHERE userid='$userid'");
		$t or mobile_msg($L['msg_error']);
		if($t['groupid'] == 2) mobile_msg($L['forgot_msg_2']);
		if($t['groupid'] == 4) mobile_msg($L['forgot_msg_4']);
		if(is_email($_SESSION['f_key'])) {
			$email = $_SESSION['f_key'];
			$email == $t['email'] or mobile_msg($L['msg_error']);
			$type = 'email';
			$head_name = $L['forgot_email_title'];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
		} else {
			$mobile = $_SESSION['f_key'];
			($mobile == $t['mobile'] && $t['vmobile']) or mobile_msg($L['msg_error']);
			$type = 'mobile';
			$head_name = $L['forgot_mobile_title'];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
		}
		$back_link = '?action=user&type='.$type;
	break;
	case 'send':
		(isset($_SESSION['f_uid']) && isset($_SESSION['f_key'])) or exit('ko');
		$userid = intval($_SESSION['f_uid']);
		$t = $db->get_one("SELECT email,mobile,vmobile,groupid FROM {$DT_PRE}member WHERE userid='$userid'");
		$t or exit('ko');
		if($t['groupid'] == 2 || $t['groupid'] == 4) exit('ko');
		if(is_email($_SESSION['f_key'])) {
			$email = $_SESSION['f_key'];
			$email == $t['email'] or  exit('ko');
			isset($_SESSION['email_send']) or $_SESSION['email_send'] = 0;
			isset($_SESSION['email_time']) or $_SESSION['email_time'] = 0;
			if($_SESSION['email_time'] && (($DT_TIME - $_SESSION['email_time']) < 60)) exit('ko'.($DT_TIME - $_SESSION['email_time']));
			if($_SESSION['email_send'] > 9) exit('max');
			$emailcode = random(6, '0123456789');
			$_SESSION['email_save'] = $email;
			$_SESSION['email_code'] = md5($email.'|'.$emailcode.'|FE');
			$_SESSION['email_time'] = $DT_TIME;
			$_SESSION['email_send'] = $_SESSION['email_send'] + 1;
			$title = $L['register_msg_emailcode'];
			$content = ob_template('emailcode', 'mail');
			send_mail($email, $title, stripslashes($content));
			exit('ok');
		} else {
			$mobile = $_SESSION['f_key'];
			($mobile == $t['mobile'] && $t['vmobile']) or  exit('ko');
			isset($_SESSION['mobile_send']) or $_SESSION['mobile_send'] = 0;
			isset($_SESSION['mobile_time']) or $_SESSION['mobile_time'] = 0;
			if($_SESSION['mobile_time'] && (($DT_TIME - $_SESSION['mobile_time']) < 180)) exit('ko');
			if($_SESSION['mobile_send'] > 4) exit('max');
			if(max_sms($mobile)) exit('max');
			$mobilecode = random(6, '0123456789');
			$_SESSION['mobile_save'] = $mobile;
			$_SESSION['mobile_code'] = md5($mobile.'|'.$mobilecode.'|FM');
			$_SESSION['mobile_time'] = $DT_TIME;
			$_SESSION['mobile_send'] = $_SESSION['mobile_send'] + 1;
			$content = lang('sms->sms_code', array($mobilecode, $MOD['auth_days']*10)).$DT['sms_sign'];
			send_sms($mobile, $content);
			exit('ok');
		}
		exit('ko');
	break;
	case 'verify':
		isset($code) or $code = '';
		preg_match("/^[0-9a-z]{6,}$/i", $code) or exit('ko');
		isset($password) or $password = '';
		(strlen($password) >= $MOD['minpassword'] && strlen($password) <= $MOD['maxpassword']) or exit('ko');
		(isset($_SESSION['f_uid']) && isset($_SESSION['f_key'])) or exit('ko');
		$userid = intval($_SESSION['f_uid']);
		$t = $db->get_one("SELECT email,vemail,mobile,vmobile,groupid FROM {$DT_PRE}member WHERE userid='$userid'");
		$t or exit('ko');
		if($t['groupid'] == 2 || $t['groupid'] == 4) exit('ko');
		$vemail = $t['vemail'];
		if(is_email($_SESSION['f_key'])) {
			$vemail = 1;
			$email = $_SESSION['f_key'];
			$email == $t['email'] or exit('ko');
			$_SESSION['email_code'] == md5($t['email'].'|'.$code.'|FE') or exit('ko');
			set_cookie('username', $email);
			unset($_SESSION['email_save']);
			unset($_SESSION['email_code']);
			unset($_SESSION['email_time']);
			unset($_SESSION['email_send']);
		} else {
			$mobile = $_SESSION['f_key'];
			($mobile == $t['mobile'] && $t['vmobile']) or exit('ko');
			$_SESSION['mobile_code'] == md5($t['mobile'].'|'.$code.'|FM') or exit('ko');
			set_cookie('username', $mobile);
			unset($_SESSION['mobile_save']);
			unset($_SESSION['mobile_code']);
			unset($_SESSION['mobile_time']);
			unset($_SESSION['mobile_send']);
		}
		$salt = random(8);
		$pass = dpassword($password, $salt);
		$db->query("UPDATE {$DT_PRE}member SET password='$pass',passsalt='$salt',vemail='$vemail' WHERE userid='$userid'");
		exit('ok');
	break;
	case 'check':
		isset($type) or exit('ko');
		$captcha = isset($captcha) ? convert(input_trim($captcha), 'UTF-8', DT_CHARSET) : '';
		$msg = captcha($captcha, 1, true);
		if($msg) exit('captcha');
		if($type == 'mobile') {
			$could_mobile or exit('ko');
			is_mobile($mobile) or exit('ko');
			$t = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE mobile='$mobile' AND vmobile=1");
			if($t) {
				$_SESSION['f_uid'] = $t['userid'];
				$_SESSION['f_key'] = $mobile;
				exit('ok');
			}
			exit('no');
		} else if($type == 'email') {
			$could_email or exit('ko');
			is_email($email) or exit('ko');
			$t = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE email='$email'");
			if($t) {
				$_SESSION['f_uid'] = $t['userid'];
				$_SESSION['f_key'] = $email;
				exit('ok');
			}
			exit('no');
		}
		exit('ko');
	break;
	case 'user':
		isset($type) or dheader('?reload='.$DT_TIME);
		if($type == 'mobile') {
			$could_mobile or dheader('?reload='.$DT_TIME);
			$head_name = $L['forgot_by_sms'];
		} else if($type == 'email') {
			$could_email or dheader('?reload='.$DT_TIME);
			$head_name = $L['forgot_by_email'];
		} else {
			dheader('?reload='.$DT_TIME);
		}
		$back_link = '?reload='.$DT_TIME;
		$head_title = $head_name.$DT['seo_delimiter'].$head_title;
	break;
	case 'contact':
		$back_link = 'forgot.php';
		$head_name = $L['forgot_by_contact'];
		$head_title = $head_name.$DT['seo_delimiter'].$head_title;
	break;
	default:
		$back_link = 'login.php';
	break;
}
$foot = '';
include template('forgot', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>