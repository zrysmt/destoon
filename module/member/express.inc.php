<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MG['express_limit'] > -1 or dalert(lang('message->without_permission_and_upgrade'), 'goback');
$head_title = $L['express_title'];
require DT_ROOT.'/include/post.func.php';
require MD_ROOT.'/express.class.php';
$do = new express();
switch($action) {
	case 'add':
		if($MG['express_limit']) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}mall_express WHERE username='$_username' AND parentid=0");
			if($r['num'] >= $MG['express_limit']) dalert(lang($L['limit_add'], array($MG['express_limit'], $r['num'])), 'goback');
		}
		if($submit) {
			if($do->pass($post)) {
				$post['username'] = $_username;
				$do->add($post);
				dmsg($L['op_add_success'], '?action=index');
			} else {
				message($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				$$v = '';
			}
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
				$do->edit($post);
				dmsg($L['op_edit_success'], $forward);
			} else {
				message($do->errmsg);
			}
		} else {
			extract($r);
		}
	break;
	case 'delete':
		$itemid or message($L['express_msg_choose']);
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['username'] != $_username) message();
		$do->delete($itemid);
		dmsg($L['op_del_success'], $forward);
	break;
	case 'area':
		$itemid or message($L['express_msg_choose']);
		$do->itemid = $itemid;
		$I = $r = $do->get_one();
		if(!$r || $r['username'] != $_username) message();
		if($submit) {
			$do->area($post);
			dmsg($L['op_success'], '?action=area&itemid='.$itemid);
		} else {
			$lists = $do->get_list("parentid=$itemid");
			if($r['items'] != $items) $db->query("UPDATE {$DT_PRE}mall_express SET items=$items WHERE itemid=$itemid");
			$area_select = ajax_area_select('post[0][areaid]', $L['choose']);
		}
	break;
	default:
		$condition = "username='$_username' AND parentid=0";
		if($keyword) $condition .= " AND express LIKE '%$keyword%'";
		$lists = $do->get_list($condition);
		$limit_used = $items;
		$limit_free = $MG['express_limit'] && $MG['express_limit'] > $limit_used ? $MG['express_limit'] - $limit_used : 0;	
	break;
}
include template('express', $module);
?>