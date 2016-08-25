<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$step = isset($step) ? intval($step) : 0;
$could_email = $DT['mail_type'] == 'close' ? 0 : 1;
$could_mobile = $DT['sms'] ? 1 : 0;
switch($action) {
	case 'check':
		if($_userid && $_groupid != 4) dheader($MOD['linkurl']);
		if($MOD['checkuser'] != 2) dheader(DT_PATH);
		if(!$could_email) message($L['send_mail_close']);
		(isset($email) && is_email($email)) or $email = '';
		$session = new dsession();
		$verify = 1;
		if(isset($auth)) {
			$auth = decrypt($auth, DT_KEY.'REG');
			if($auth) {
				list($umail, $utime) = explode('|', $auth);
				if(is_email($umail) && $DT_TIME - $utime < $MOD['auth_days']*600) {
					$step = 1;
					$verify = 0;
					$email = $umail;
					$captcha = '';
				}
			}
		}
		if($step == 2) {
			$email = $_SESSION['email_save'];
			is_email($email) or dheader('?action='.$action);
			$code = isset($code) ? trim($code) : '';
			(preg_match("/^[0-9a-z]{6,}$/i", $code) && $_SESSION['email_code'] == md5($email.'|'.$code.'|SEC')) or message($L['register_pass_emailcode']);
			$r = $db->get_one("SELECT userid,username,groupid,regid FROM {$DT_PRE}member WHERE email='$email'");
			if($r && $r['groupid'] == 4) {
				$userid = $r['userid'];
				$username = $r['username'];
				$groupid = $r['regid'];
				$groupid > 4 or $groupid = 5;
				$db->query("UPDATE {$DT_PRE}member SET groupid='$groupid',vemail=1 WHERE userid='$userid'");
				$db->query("UPDATE {$DT_PRE}company SET groupid='$groupid' WHERE userid='$userid'");
				userclean($username);
				if($MOD['welcome_message'] || $MOD['welcome_email']) {
					$title = $L['register_msg_welcome'];
					$content = ob_template('welcome', 'mail');
					if($MOD['welcome_message']) send_message($username, $title, $content);
					if($MOD['welcome_email'] && $DT['mail_type'] != 'close') send_mail($email, $title, $content);
				}
				unset($_SESSION['email_save']);
				unset($_SESSION['email_code']);
			} else {
				message($L['send_check_deny']);
			}
		} else if($step == 1) {
			captcha($captcha, $verify);
			is_email($email) or message($L['member_email_null']);
			$r = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE email='$email'");
			if($r) {
				if($r['groupid'] != 4) message($L['send_check_deny']);
				$emailcode = random(8);
				$_SESSION['email_save'] = $email;
				$_SESSION['email_code'] = md5($email.'|'.$emailcode.'|SEC');
				$title = $L['register_msg_emailcode'];
				$content = ob_template('emailcode', 'mail');
				send_mail($email, $title, stripslashes($content));
			} else {
				message($L['send_bad_email']);
			}
		} else {
			(isset($username) && check_name($username)) or $username = '';
			$head_title = $L['send_email_title'];
		}
	break;
	case 'passport':
		$_username == $_passport or dheader('edit.php');
		if($submit) {
			isset($npassport) or $npassport = '';
			require MD_ROOT.'/member.class.php';
			$do = new member;
			$do->userid = $_userid;
			if($do->edit_passport($_passport, $npassport, $_username)) {
				dmsg($L['op_edit_success'], 'edit.php');
			} else {
				message($do->errmsg);
			}
		} else {			
			$head_title = $L['send_passport_title'];
		}
	break;
	case 'payword':
		login();
		if(!$could_email) message($L['send_mail_close']);
		$username = $_username;
		$email = $_email;
		$session = new dsession();
		if($step == 2) {
			$payword = $_SESSION['email_save'];
			is_md5($payword) or dheader('?action='.$action);
			$code = isset($code) ? trim($code) : '';
			(preg_match("/^[0-9a-z]{6,}$/i", $code) && $_SESSION['email_code'] == md5($payword.'|'.$code.'|'.$username.'|SEP')) or message($L['register_pass_emailcode']);
			$salt = random(8);
			$pass = dpassword($payword, $salt);
			$db->query("UPDATE {$DT_PRE}member SET payword='$pass',paysalt='$salt',vemail=1 WHERE userid=$_userid");
			userclean($username);
			unset($_SESSION['email_save']);
			unset($_SESSION['email_code']);
		} else if($step == 1) {
			captcha($captcha);
			if(strlen($npassword) > $MOD['maxpassword'] || strlen($npassword) < $MOD['minpassword']) message(lang($L['member_password_len'], array($MOD['minpassword'], $MOD['minpassword'])));
			if($npassword != $cpassword) message($L['member_payword_match']);
			if(!is_password($username, $password)) message($L['member_login_password_bad']);
			$emailcode = random(8);
			$_SESSION['email_save'] = md5($npassword);
			$_SESSION['email_code'] = md5(md5($npassword).'|'.$emailcode.'|'.$username.'|SEP');
			$title = $L['register_msg_emailcode'];
			$content = ob_template('emailcode', 'mail');
			send_mail($email, $title, stripslashes($content));
		} else {
			$head_title = $L['send_payword_title'];
		}
	break;
	case 'email':
		login();
		if(!$could_email) message($L['send_mail_close']);
		$username = $_username;
		(isset($email) && is_email($email)) or $email = '';
		$session = new dsession();
		if($step == 2) {
			$email = $_SESSION['email_save'];
			is_email($email) or dheader('?action='.$action);
			$code = isset($code) ? trim($code) : '';
			(preg_match("/^[0-9a-z]{6,}$/i", $code) && $_SESSION['email_code'] == md5($email.'|'.$code.'|'.$username.'|SE')) or message($L['register_pass_emailcode']);
			$db->query("UPDATE {$DT_PRE}member SET email='$email',vemail=1 WHERE userid=$_userid");
			userclean($username);
			unset($_SESSION['email_save']);
			unset($_SESSION['email_code']);
		} else if($step == 1) {
			captcha($captcha);
			is_email($email) or message($L['member_email_null']);
			if($email == $_email) message($L['send_email_exist']);
			if(!is_password($username, $password)) message($L['member_login_password_bad']);
			$r = $db->get_one("SELECT email FROM {$DT_PRE}member WHERE email='$email'");
			if($r) message($L['send_email_exist']);
			$emailcode = random(8);
			$_SESSION['email_save'] = $email;
			$_SESSION['email_code'] = md5($email.'|'.$emailcode.'|'.$username.'|SE');
			$title = $L['register_msg_emailcode'];
			$content = ob_template('emailcode', 'mail');
			send_mail($email, $title, stripslashes($content));
		} else {
			$head_title = $L['send_email_title'];
		}
	break;
	case 'mobile':
		login();
		$could_mobile or message($L['send_sms_close']);
		$username = $_username;
		(isset($mobile) && is_mobile($mobile)) or $mobile = '';
		$session = new dsession();
		$t = $db->get_one("SELECT mobile FROM {$DT_PRE}member WHERE userid=$_userid");
		$_mobile = $t['mobile'];
		if($step == 2) {
			$mobile = $_SESSION['mobile_save'];
			is_mobile($mobile) or dheader('?action='.$action);
			$code = isset($code) ? trim($code) : '';
			(preg_match("/^[0-9a-z]{6,}$/i", $code) && $_SESSION['mobile_code'] == md5($mobile.'|'.$code.'|'.$username.'|SM')) or message($L['register_pass_mobilecode']);
			$db->query("UPDATE {$DT_PRE}member SET mobile='$mobile',vmobile=1 WHERE userid=$_userid");
			userclean($username);
			unset($_SESSION['mobile_save']);
			unset($_SESSION['mobile_code']);
		} else if($step == 1) {
			captcha($captcha);
			if(!is_mobile($mobile)) message($L['member_mobile_null']);
			if($mobile == $_mobile) message($L['send_mobile_exist']);
			if(!is_password($username, $password)) message($L['member_login_password_bad']);
			$r = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE mobile='$mobile' AND vmobile=1 AND userid<>$_userid");
			if($r) message($L['send_mobile_exist']);
			if(max_sms($mobile)) message($L['sms_msg_max']);
			$mobilecode = random(6, '0123456789');
			$_SESSION['mobile_save'] = $mobile;
			$_SESSION['mobile_code'] = md5($mobile.'|'.$mobilecode.'|'.$username.'|SM');
			$content = lang('sms->sms_code', array($mobilecode, $MOD['auth_days']*10)).$DT['sms_sign'];
			send_sms($mobile, $content);
		} else {
			$head_title = $L['send_mobile_title'];
		}
	break;
	case 'contact':
		$url = DT_PATH;
		if(is_file(DT_ROOT.'/about/contact.html')) {
			$url = DT_PATH.'about/contact.html';
		} else if(is_file(DT_ROOT.'/about/index.html')) {			
			$url = DT_PATH.'about/index.html';
		}
		$head_title = $L['send_password_title'];
	break;
	case 'sms':
		if($_userid) dheader($MOD['linkurl']);
		$could_mobile or message($L['send_sms_close']);
		(isset($mobile) && is_mobile($mobile)) or $mobile = '';
		$session = new dsession();
		isset($_SESSION['mobile_send']) or $_SESSION['mobile_send'] = 0;
		isset($_SESSION['mobile_time']) or $_SESSION['mobile_time'] = 0;
		$second = $DT_TIME - $_SESSION['mobile_time'];
		if($step == 2) {
			$mobile = $_SESSION['mobile_save'];
			is_mobile($mobile) or dheader('?action='.$action);
			if(strlen($password) > $MOD['maxpassword'] || strlen($password) < $MOD['minpassword']) message(lang($L['member_password_len'], array($MOD['minpassword'], $MOD['minpassword'])));
			if($password != $cpassword) message($L['member_payword_match']);
			$code = isset($code) ? trim($code) : '';
			(preg_match("/^[0-9a-z]{6,}$/i", $code) && $_SESSION['mobile_code'] == md5($mobile.'|'.$code.'|SMS')) or message($L['register_pass_mobilecode']);
			$r = $db->get_one("SELECT userid,username,groupid,vmobile FROM {$DT_PRE}member WHERE mobile='$mobile' AND vmobile=1");
			if($r) {
				if($r['groupid'] == 2 || $r['groupid'] == 4) message($L['send_password_checking']);
				$userid = $r['userid'];
				$username = $r['username'];
				$salt = random(8);
				$pass = dpassword($password, $salt);
				$db->query("UPDATE {$DT_PRE}member SET password='$pass',passsalt='$salt' WHERE userid='$userid'");
				unset($_SESSION['mobile_save']);
				unset($_SESSION['mobile_code']);
				unset($_SESSION['mobile_time']);
				unset($_SESSION['mobile_send']);
			} else {
				message($L['send_bad_mobile']);
			}
		} else if($step == 1) {
			captcha($captcha);
			if(max_sms($mobile)) message($L['sms_msg_max'], '?action='.$action);
			if($_SESSION['mobile_send'] > 4) message($L['send_too_many'], '?action='.$action);
			if($second < 180) message($L['send_too_quick'], '?action='.$action);
			$mobile = trim($mobile);
			if(!is_mobile($mobile)) message($L['member_mobile_null']);
			$r = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE mobile='$mobile' AND vmobile=1");
			if($r) {
				if($r['groupid'] == 2 || $r['groupid'] == 4) message($L['send_password_checking']);
				$mobilecode = random(6, '0123456789');
				$_SESSION['mobile_save'] = $mobile;
				$_SESSION['mobile_code'] = md5($mobile.'|'.$mobilecode.'|SMS');
				$_SESSION['mobile_time'] = $DT_TIME;
				$_SESSION['mobile_send'] = $_SESSION['mobile_send'] + 1;
				$content = lang('sms->sms_code', array($mobilecode, $MOD['auth_days']*10)).$DT['sms_sign'];
				send_sms($mobile, $content);
			} else {
				message($L['send_bad_mobile']);
			}
		} else {
			$seconds = $second < 180 ? 180 - $second : 0;
		}
		$head_title = $L['send_password_title'];
	break;
	case 'mail':
		if($_userid) dheader($MOD['linkurl']);
		if(!$could_email) message($L['send_mail_close']);
		(isset($email) && is_email($email)) or $email = '';
		$session = new dsession();
		isset($_SESSION['email_send']) or $_SESSION['email_send'] = 0;
		isset($_SESSION['email_time']) or $_SESSION['email_time'] = 0;
		$second = $DT_TIME - $_SESSION['email_time'];
		if($step == 2) {
			$email = $_SESSION['email_save'];
			is_email($email) or dheader('?action='.$action);
			if(strlen($password) > $MOD['maxpassword'] || strlen($password) < $MOD['minpassword']) message(lang($L['member_password_len'], array($MOD['minpassword'], $MOD['minpassword'])));
			if($password != $cpassword) message($L['member_payword_match']);
			$code = isset($code) ? trim($code) : '';
			(preg_match("/^[0-9a-z]{6,}$/i", $code) && $_SESSION['email_code'] == md5($email.'|'.$code.'|SEM')) or message($L['register_pass_emailcode']);
			$r = $db->get_one("SELECT userid,username,groupid FROM {$DT_PRE}member WHERE email='$email'");
			if($r) {
				if($r['groupid'] == 2 || $r['groupid'] == 4) message($L['send_password_checking']);
				$userid = $r['userid'];
				$username = $r['username'];
				$salt = random(8);
				$pass = dpassword($password, $salt);
				$db->query("UPDATE {$DT_PRE}member SET password='$pass',passsalt='$salt',vemail=1 WHERE userid='$userid'");
				unset($_SESSION['email_save']);
				unset($_SESSION['email_code']);
				unset($_SESSION['email_time']);
				unset($_SESSION['email_send']);
			} else {
				message($L['send_bad_email']);
			}
		} else if($step == 1) {
			captcha($captcha);
			if($_SESSION['email_send'] > 9) message($L['send_too_many'], '?action='.$action);
			if($second < 60) message($L['send_too_quick'], '?action='.$action);
			$email = trim($email);
			is_email($email) or message($L['member_email_null']);
			$r = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE email='$email'");
			if($r) {
				if($r['groupid'] == 2 || $r['groupid'] == 4) message($L['send_password_checking']);
				$emailcode = random(8);
				$_SESSION['email_save'] = $email;
				$_SESSION['email_code'] = md5($email.'|'.$emailcode.'|SEM');
				$_SESSION['email_time'] = $DT_TIME;
				$_SESSION['email_send'] = $_SESSION['email_send'] + 1;
				$title = $L['register_msg_emailcode'];
				$content = ob_template('emailcode', 'mail');
				send_mail($email, $title, stripslashes($content));
			} else {
				message($L['send_bad_email']);
			}
		} else {
			$seconds = $second < 60 ? 60 - $second : 0;
		}
		$head_title = $L['send_password_title'];
	break;
	default:
		$head_title = $L['send_password_title'];
	break;
}
include template('send', $module);
?>