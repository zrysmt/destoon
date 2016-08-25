<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MOD['vmember'] or dheader($MOD['linkurl']);
require DT_ROOT.'/include/post.func.php';
$username = $_username;
$user = userinfo($username);
$step = isset($step) ? intval($step) : 0;
$could_email = $DT['mail_type'] == 'close' ? 0 : 1;
$could_mobile = $DT['sms'] ? 1 : 0;
switch($action) {
	case 'email':
		$MOD['vemail'] or dheader($MOD['linkurl']);
		$could_email or message($L['send_mail_close']);
		$head_title = $L['validate_email_title'];
		if($user['vemail']) {
			$action = 'v'.$action;
			include template('validate', $module);
			exit;
		}
		(isset($email) && is_email($email)) or $email = '';
		$session = new dsession();
		if($step == 2) {
			$email = $_SESSION['email_save'];
			is_email($email) or dheader('?action='.$action);
			$code = isset($code) ? trim($code) : '';
			(preg_match("/^[0-9a-z]{6,}$/i", $code) && $_SESSION['email_code'] == md5($email.'|'.$code.'|'.$username.'|VE')) or message($L['register_pass_emailcode']);
			$db->query("UPDATE {$DT_PRE}member SET email='$email',vemail=1 WHERE userid=$_userid");
			userclean($username);
			$db->query("INSERT INTO {$DT_PRE}validate (type,username,ip,addtime,status,title,editor,edittime) VALUES ('email','$username','$DT_IP','$DT_TIME','3','$email','system','$DT_TIME')");
			unset($_SESSION['email_save']);
			unset($_SESSION['email_code']);
		} else if($step == 1) {
			captcha($captcha);
			is_email($email) or message($L['member_email_null']);
			if($email != $_email) {
				$r = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE email='$email'");
				if($r) message($L['send_email_exist']);
			}
			$emailcode = random(8);
			$_SESSION['email_save'] = $email;
			$_SESSION['email_code'] = md5($email.'|'.$emailcode.'|'.$username.'|VE');
			$title = $L['register_msg_emailcode'];
			$content = ob_template('emailcode', 'mail');
			send_mail($email, $title, stripslashes($content));
		} else {
			$email or $email = $_email;
		}
		include template('validate', $module);
	break;
	case 'mobile':
		$MOD['vmobile'] or dheader($MOD['linkurl']);
		$could_mobile or message($L['send_sms_close']);
		$head_title = $L['validate_mobile_title'];
		if($user['vmobile']) {
			$action = 'v'.$action;
			include template('validate', $module);
			exit;
		}
		(isset($mobile) && is_mobile($mobile)) or $mobile = '';
		$_mobile = $user['mobile'];
		$session = new dsession();
		if($step == 2) {
			$mobile = $_SESSION['mobile_save'];
			is_mobile($mobile) or dheader('?action='.$action);
			$code = isset($code) ? trim($code) : '';
			(preg_match("/^[0-9a-z]{6,}$/i", $code) && $_SESSION['mobile_code'] == md5($mobile.'|'.$code.'|'.$username.'|VM')) or message($L['register_pass_mobilecode']);
			$db->query("UPDATE {$DT_PRE}member SET mobile='$mobile',vmobile=1 WHERE userid=$_userid");
			userclean($username);
			$db->query("INSERT INTO {$DT_PRE}validate (type,username,ip,addtime,status,title,editor,edittime) VALUES ('mobile','$username','$DT_IP','$DT_TIME','3','$mobile','system','$DT_TIME')");
			unset($_SESSION['mobile_save']);
			unset($_SESSION['mobile_code']);
		} else if($step == 1) {
			captcha($captcha);
			if(!is_mobile($mobile)) message($L['member_mobile_null']);	
			$r = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE mobile='$mobile' AND vmobile=1 AND userid<>$_userid");
			if($r) message($L['send_mobile_exist']);
			if(max_sms($mobile)) message($L['sms_msg_max']);
			$mobilecode = random(6, '0123456789');
			$_SESSION['mobile_save'] = $mobile;
			$_SESSION['mobile_code'] = md5($mobile.'|'.$mobilecode.'|'.$username.'|VM');
			$content = lang('sms->sms_code', array($mobilecode, $MOD['auth_days']*10)).$DT['sms_sign'];
			send_sms($mobile, $content);
		} else {
			$mobile or $mobile = $_mobile;
		}		
		include template('validate', $module);
	break;
	case 'truename':
		$MOD['vtruename'] or dheader($MOD['linkurl']);
		$head_title = $L['validate_truename_title'];
		$va = $db->get_one("SELECT * FROM {$DT_PRE}validate WHERE type='$action' AND username='$username'");
		if($user['vtruename'] || $va) {
			$action = 'v'.$action;
			include template('validate', $module);
			exit;
		}
		if($submit) {
			captcha($captcha);
			if(!$truename) message($L['validate_truename_name']);
			if(!$thumb) message($L['validate_truename_image']);
			clear_upload($thumb.$thumb1.$thumb2);
			$truename = dhtmlspecialchars($truename);
			$thumb = dhtmlspecialchars($thumb);
			$thumb1 = dhtmlspecialchars($thumb1);
			$thumb2 = dhtmlspecialchars($thumb2);
			$db->query("INSERT INTO {$DT_PRE}validate (type,username,ip,addtime,status,editor,edittime,title,thumb,thumb1,thumb2) VALUES ('$action','$username','$DT_IP','$DT_TIME','2','system','$DT_TIME','$truename','$thumb','$thumb1','$thumb2')");
			dmsg($L['validate_truename_success'], '?action='.$action);
		} else {
			include template('validate', $module);
		}
	break;
	case 'company':
		$MOD['vcompany'] or dheader($MOD['linkurl']);
		$head_title = $L['validate_company_title'];
		$va = $db->get_one("SELECT * FROM {$DT_PRE}validate WHERE type='$action' AND username='$username'");
		if($user['vcompany'] || $va) {
			$action = 'v'.$action;
			include template('validate', $module);
			exit;
		}
		if($submit) {
			captcha($captcha);
			if(!$company) message($L['validate_company_name']);
			if(!$thumb) message($L['validate_company_image']);
			clear_upload($thumb.$thumb1.$thumb2);
			$company = dhtmlspecialchars($company);
			$thumb = dhtmlspecialchars($thumb);
			$thumb1 = dhtmlspecialchars($thumb1);
			$thumb2 = dhtmlspecialchars($thumb2);
			$db->query("INSERT INTO {$DT_PRE}validate (type,username,ip,addtime,status,editor,edittime,title,thumb,thumb1,thumb2) VALUES ('$action','$username','$DT_IP','$DT_TIME','2','system','$DT_TIME','$company','$thumb','$thumb1','$thumb2')");
			dmsg($L['validate_company_success'], '?action='.$action);
		} else {
			include template('validate', $module);
		}
	break;
	case 'bank':
		$head_title = $L['validate_bank_title'];
		include template('validate', $module);
	break;
	default:
		dheader($MOD['linkurl']);
	break;
}
?>