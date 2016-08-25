<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
$MG['club_group_limit'] > -1 or dalert(lang('message->without_permission_and_upgrade'), 'goback');
require MD_ROOT.'/group.class.php';
$do = new group($moduleid);
$sql = "username='$_username'";
$limit_used = $limit_free = $need_password = $need_captcha = $need_question = $fee_add = 0;
if(in_array($action, array('', 'add')) && $MG['club_group_limit']) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_group WHERE $sql AND status>1");
	$limit_used = $r['num'];
	$limit_free = $MG['club_group_limit'] > $limit_used ? $MG['club_group_limit'] - $limit_used : 0;
}

switch($action) {
	case 'add':
		if($MG['club_group_limit'] && $limit_used >= $MG['club_group_limit']) dalert(lang($L['info_limit'], array($MG['club_group_limit'], $limit_used)), $MODULE[2]['linkurl'].$DT['file_my'].'?mid='.$mid.'&job='.$job);

		$need_captcha = $MOD['captcha_group'] == 2 ? $MG['captcha'] : $MOD['captcha_group'];
		$need_question = $MOD['question_group'] == 2 ? $MG['question'] : $MOD['question_group'];

		if($submit) {
			$msg = captcha($captcha, $need_captcha, true);
			if($msg) dalert($msg);
			$msg = question($answer, $need_question, true);
			if($msg) dalert($msg);
			$post['username'] = $_username;
			if($do->pass($post)) {
				$CAT = get_cat($post['catid']);
				if(!$CAT) dalert(lang($L['group'], array($CAT['catname'])));
				$post['addtime'] = $post['level'] = $post['fee'] = 0;
				$post['style'] = $post['template'] = $post['note'] = $post['filepath'] = '';
				$need_check =  $MOD['check_group'] == 2 ? $MG['check'] : $MOD['check_group'];
				$post['status'] = get_status(3, $need_check);
				$post['hits'] = 0;
				$post['areaid'] = $cityid;
				$post['filepath'] = '';
				$do->add($post);
				$msg = $post['status'] == 2 ? $L['success_check'] : $L['success_add'];
				$js = '';
				set_cookie('dmsg', $msg);
				$forward = $MODULE[2]['linkurl'].$DT['file_my'].'?mid='.$mid.'&job='.$job.'&status='.$post['status'];
				$msg = '';
				$js .= 'window.onload=function(){parent.window.location="'.$forward.'";}';
				dalert($msg, '', $js);
			} else {
				dalert($do->errmsg, '', ($need_captcha ? reload_captcha() : '').($need_question ? reload_question() : ''));
			}
		} else {
			foreach($do->fields as $v) {
				$$v = '';
			}
			$content = '';
			$catid = intval($catid);
			$areaid = $cityid;
			$item = array();
		}
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$item = $do->get_one();
		if(!$item || $item['username'] != $_username) message();
		if($submit) {
			$post['username'] = $_username;
			if($do->pass($post)) {
				$post['catid'] = $item['catid'];
				$post['areaid'] = $item['areaid'];
				$post['title'] = addslashes($item['title']);
				$post['level'] = $item['level'];
				$post['fee'] = $item['fee'];
				$post['style'] = addslashes($item['style']);
				$post['template'] = addslashes($item['template']);
				$post['filepath'] = addslashes($item['filepath']);
				$post['status'] = $item['status'];
				$post['hits'] = $item['hits'];
				$do->edit($post);
				set_cookie('dmsg', $L['success_edit']);
				dalert('', '', 'parent.window.location="'.$forward.'"');
			} else {
				dalert($do->errmsg);
			}
		} else {
			extract($item);
		}
	break;
	default:
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(1, 2, 3)) or $status = 3;
		$condition = "username='$_username'";
		$condition .= " AND status=$status";
		if($keyword) $condition .= " AND title LIKE '%$keyword%'";
		if($catid) $condition .= ($CAT['child']) ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
		$lists = $do->get_list($condition, 'addtime desc');
		break;
}
if($_userid) {
	$nums = array();
	for($i = 1; $i < 4; $i++) {
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_group WHERE username='$_username' AND status=$i");
		$nums[$i] = $r['num'];
	}
}
$head_title = $L['my_group_title'];
?>