<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(!$_userid) dheader($MODULE[2]['linkurl'].$DT['file_my']);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if($action == 'logout' && $admin_user) {
	set_cookie('admin_user', '');
	dmsg($L['index_msg_logout'], $MODULE[2]['linkurl']);
}
require MD_ROOT.'/member.class.php';
require DT_ROOT.'/include/post.func.php';
$do = new member;
if($submit) {
	if(word_count($note) > 5000) message($L['index_msg_note_limit']);
	$note = '<?php exit;?>'.dhtmlspecialchars(stripslashes($note));
	file_put(DT_ROOT.'/file/user/'.dalloc($_userid).'/'.$_userid.'/note.php', $note);
	dmsg($L['op_update_success'], $MODULE[2]['linkurl']);
} else {
	$head_title = '';
	$do->userid = $_userid;
	$user = $do->get_one();
	extract($user);
	$expired = $totime && $totime < $DT_TIME ? true : false;
	$havedays = $expired ? 0 : ceil(($totime-$DT_TIME)/86400);

	$sys = array();
	$i = 0;
	$result = $db->query("SELECT itemid,title,addtime,groupids FROM {$DT_PRE}message WHERE groupids<>'' ORDER BY itemid DESC", 'CACHE');
	while($r = $db->fetch_array($result)) {
		$groupids = explode(',', $r['groupids']);
		if(!in_array($_groupid, $groupids)) continue;
		if($i > 2) continue;
		$i++;
		$sys[] = $r;
	}
	$note = DT_ROOT.'/file/user/'.dalloc($_userid).'/'.$_userid.'/note.php';
	$note = file_get($note);
	if($note) {
		$note = substr($note, 13);
	} else {
		$note = $MOD['usernote'];
	}
	$trade = $db->count("{$DT_PRE}mall_order", "seller='$_username' AND status=0");
	$t = explode('.', $_money);
	$my_money = $t[0].'<span>.'.$t[1].'</span>';
	include template('index', $module);
}
?>