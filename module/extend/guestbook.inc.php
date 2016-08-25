<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MOD['guestbook_enable'] or dheader(DT_PATH);
$TYPE = explode('|', trim($MOD['guestbook_type']));
require DT_ROOT.'/include/post.func.php';
require MD_ROOT.'/guestbook.class.php';
$do = new guestbook();
$destoon_task = rand_task();
if($submit) {
	captcha($captcha, $MOD['guestbook_captcha']);
	if($do->pass($post)) {
		$post['areaid'] = $cityid;
		$do->add($post);
		message($L['gbook_success'], $EXT['guestbook_url']);
	} else {
		message($do->errmsg);
	}
} else {
	$type = '';
	$condition = "status=3 AND reply<>''";
	if($keyword) $condition .= " AND content LIKE '%$keyword%'";
	if($cityid) $condition .= ($AREA[$cityid]['child']) ? " AND areaid IN (".$AREA[$cityid]['arrchildid'].")" : " AND areaid=$cityid";
	$lists = $do->get_list($condition);
	$head_title = $L['gbook_title'];
	$content = isset($content) ? dhtmlspecialchars(stripslashes($content)) : '';
	$truename = $telephone = $email = $qq = $msn = $ali = $skype = '';
	if($_userid) {
		$user = userinfo($_username);
		$truename = $user['truename'];
		$telephone = $user['telephone'] ? $user['telephone'] : $user['mobile'];
		$email = $user['mail'] ? $user['mail'] : $user['email'];
		$qq = $user['qq'];
		$msn = $user['msn'];
		$ali = $user['ali'];
		$skype = $user['skype'];
	}
	include template('guestbook', $module);
}
?>