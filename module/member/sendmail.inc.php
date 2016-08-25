<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MG['sendmail'] or dalert(lang('message->without_permission_and_upgrade'), 'goback');
require DT_ROOT.'/include/post.func.php';
if(isset($preview)) {
	$title = isset($title) ? trim(stripslashes($title)) : '';
	$content = isset($content) ? trim(stripslashes($content)) : '';
	include template('send', 'mail');
	exit;
}
if($submit) {
	captcha($captcha);
	$email = trim($email);
	if(!is_email($email)) message($L['sendmail_pass_mailto']);
	$title = trim(stripslashes($title));
	if(strlen($title) < 5) message($L['pass_title']);
	$content = trim(stripslashes($content));
	if(strlen($content) < 10) message($L['pass_content']);
	clear_upload($content);
	$content = dsafe(save_local($content));
	$content = ob_template('send', 'mail');
	$DT['mail_name'] = $_company;
	if(send_mail($email, $title, $content, '', false)) {//$_email
		message(lang($L['sendmail_success'], array($email)), '?action=index');
	} else {
		message($L['sendmail_fail']);
	}
} else {
	$head_title = $L['sendmail_title'];
	$email = isset($email) ? trim(stripslashes($email)) : '';
	$title = isset($title) ? trim(stripslashes($title)) : '';
	$content = isset($content) ? trim(stripslashes($content)) : '';
	if($action == 'page' && isset($title) && isset($linkurl)) {
		$content = lang($L['sendmail_content'], array(userurl($_username), $_username, $title, $linkurl));
		$title = lang($L['sendmail_title_new'], array($title));
	}
	include template('sendmail', $module);
}
?>