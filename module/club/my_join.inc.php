<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
$MG['club_join_limit'] > -1 or dalert(lang('message->without_permission_and_upgrade'), 'goback');
require MD_ROOT.'/join.class.php';
$do = new djoin($moduleid);
$sql = "username='$_username'";
$limit_used = $limit_free = $need_password = $need_captcha = $need_question = $fee_add = 0;
if(in_array($action, array('', 'add')) && $MG['club_join_limit']) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_fans WHERE $sql AND status>1");
	$limit_used = $r['num'];
	$limit_free = $MG['club_join_limit'] > $limit_used ? $MG['club_join_limit'] - $limit_used : 0;
}
switch($action) {
	case 'add':
		if($MG['club_join_limit'] && $limit_used >= $MG['club_join_limit']) dalert(lang($L['info_limit'], array($MG['club_join_limit'], $limit_used)), $MODULE[2]['linkurl'].$DT['file_my'].'?mid='.$mid.'&job='.$job);
		$gid = isset($gid) ? intval($gid) : 0;
		$gid or message($L['my_choose_group'], $MOD['linkurl']);
		$GRP = get_group($gid);
		($GRP && $GRP['status'] == 3) or message($L['my_not_group']);
		$M = $db->get_one("SELECT * FROM {$table}_fans WHERE gid=$gid AND username='$_username'");
		if($M) {
			if($M['status'] == 3) message($L['my_join_repeat'], $MOD['linkurl'].$GRP['linkurl']);
			message($L['my_join_check']);
		}
		if($submit) {
			if($do->pass($post)) {
				$post['gid'] = $gid;
				$post['status'] = get_status(3, $GRP['join_type']);
				$do->add($post);
				$msg = $post['status'] == 2 ? $L['success_check'] : $L['success_add'];
				set_cookie('dmsg', $msg);
				$forward = $MODULE[2]['linkurl'].$DT['file_my'].'?mid='.$mid.'&job='.$job.'&status='.$post['status'];
				dalert('', '', 'window.onload=function(){parent.window.location="'.$forward.'";}');
			} else {
				dalert($do->errmsg);
			}
		} else {
			$reason = '';
			$status = 0;
		}
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$item = $do->get_one();
		if(!$item || $item['username'] != $_username) message();
		$gid = $item['gid'];
		$GRP = $db->get_one("SELECT * FROM {$table}_group WHERE itemid=$gid");
		($GRP && $GRP['status'] == 3) or message($L['my_not_group']);
		if($submit) {
			if($do->pass($post)) {
				$post['status'] = get_status($item['status'], $GRP['join_type']);
				$do->edit($post);
				set_cookie('dmsg', $L['success_edit']);
				dalert('', '', 'parent.window.location="'.$forward.'"');
			} else {
				dalert($do->errmsg);
			}
		} else {
			$reason = $item['reason'];
			$status = $item['status'];
		}
	break;
	case 'delete':
		$itemid or message();
		$do->itemid = $itemid;
		$item = $do->get_one();
		if(!$item || $item['username'] != $_username) message();
		$do->delete($itemid);
		dmsg($L['success_delete'], $forward);
	break;
	default:
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(1, 2, 3)) or $status = 3;
		$lists = $do->get_list("username='$_username' AND status=$status");
	break;
}
$nums = array();
for($i = 1; $i < 4; $i++) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_fans WHERE username='$_username' AND status=$i");
	$nums[$i] = $r['num'];
}
$head_title = $L['my_join_title'];
?>