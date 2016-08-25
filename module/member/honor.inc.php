<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MG['homepage'] && $MG['honor_limit'] > -1 or dalert(lang('message->without_permission_and_upgrade'), 'goback');
require DT_ROOT.'/include/post.func.php';
require MD_ROOT.'/honor.class.php';
$do = new honor();
switch($action) {
	case 'add':
		if($MG['honor_limit']) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}honor WHERE username='$_username' AND status>0");
			if($r['num'] >= $MG['honor_limit']) dalert(lang($L['limit_add'], array($MG['honor_limit'], $r['num'])), 'goback');
		}
		if($submit) {
			if($do->pass($post)) {
				$post['username'] = $_username;
				$post['addtime'] = 0;
				$need_check =  $MOD['credit_check'] == 2 ? $MG['check'] : $MOD['credit_check'];
				$post['status'] = get_status(3, $need_check);
				$do->add($post);
				dmsg($L['op_add_success'], '?status='.$post['status']);
			} else {
				message($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				$$v = '';
			}
			$content = '';	
			$today = timetodate($DT_TIME, 'Ymd');
			$head_title = $L['honor_title_add'];
		}
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['username'] != $_username) message();
		if($submit) {
			if($do->pass($post)) {
				$post['username'] = $_username;
				$need_check =  $MOD['credit_check'] == 2 ? $MG['check'] : $MOD['credit_check'];
				$post['status'] = get_status($r['status'], $need_check);
				$do->edit($post);
				dmsg($L['op_edit_success'], $forward);
			} else {
				message($do->errmsg);
			}
		} else {
			extract($r);
			$addtime = timetodate($addtime);
			$fromtime = timetodate($fromtime, 3);
			$today = timetodate($totime, 'Ymd');
			$totime = $totime ? timetodate($totime, 3) : '';
			$head_title = $L['honor_title_edit'];
		}
	break;
	case 'delete':
		$itemid or message($L['honor_msg_choose']);
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $do->get_one();
			if(!$item || $item['username'] != $_username) message();
			$do->recycle($itemid);
		}
		dmsg($L['op_del_success'], $forward);
	break;
	default:
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(1, 2, 3, 4)) or $status = 3;
		if($status == 3) $do->expire("AND username='$_username'");
		$condition = "username='$_username'";
		$condition .= " AND status=$status";
		if($keyword) $condition .= " AND title LIKE '%$keyword%'";
		$lists = $do->get_list($condition);
		$head_title = $L['honor_title'];
	break;
}
$nums = array();
$limit_used = 0;
for($i = 1; $i < 5; $i++) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}honor WHERE username='$_username' AND status=$i");
	$nums[$i] = $r['num'];
	$limit_used += $r['num'];
}
$limit_free = $MG['honor_limit'] && $MG['honor_limit'] > $limit_used ? $MG['honor_limit'] - $limit_used : 0;
include template('honor', $module);
?>