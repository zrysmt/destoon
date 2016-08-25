<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MG['favorite_limit'] > -1 or dalert(lang('message->without_permission_and_upgrade'), 'goback');
require DT_ROOT.'/include/post.func.php';
$TYPE = get_type('favorite-'.$_userid);
require MD_ROOT.'/favorite.class.php';
$do = new favorite();
switch($action) {
	case 'add':
		if($MG['favorite_limit']) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}favorite WHERE userid=$_userid");
			if($r['num'] >= $MG['favorite_limit']) dalert(lang($L['limit_add'], array($MG['favorite_limit'], $r['num'])), 'goback');
		}
		if($submit) {
			if($do->pass($post)) {
				$post['userid'] = $_userid;
				$post['addtime'] = $DT_TIME;
				$do->add($post);
				dmsg($L['op_add_success'], '?action=index');
			} else {
				message($do->errmsg);
			}
		} else {
			$title = isset($title) ? trim($title) : '';
			$url = isset($url) ? trim($url) : '';
			$typeid = 0;
			$type_select = type_select($TYPE, 0, 'post[typeid]', $L['default_type']);
			$head_title = $L['favorite_title_add'];
		}
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['userid'] != $_userid) message();
		if($submit) {
			if($do->pass($post)) {
				$do->edit($post);
				dmsg($L['op_edit_success'], $forward);
			} else {
				message($do->errmsg);
			}
		} else {
			extract($r);
			$type_select = type_select($TYPE, 0, 'post[typeid]', $L['default_type'], $typeid);
			$head_title = $L['favorite_title_edit'];
		}
	break;
	case 'delete':
		$itemid or message($L['favorite_msg_choose']);
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['userid'] != $_userid) message();
		$do->delete($itemid);
		dmsg($L['op_del_success'], $forward);
	break;
	default:
		$sfields = $L['favorite_sfields'];
		$dfields = array('title', 'title', 'url', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$type_select = type_select($TYPE, 0, 'typeid', $L['default_type'], $typeid, '', $L['all_type']);
		$condition = "userid=$_userid";
		if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		$lists = $do->get_list($condition);
		if($MG['favorite_limit']) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}favorite WHERE userid=$_userid");
			$limit_used = $r['num'];
			$limit_free = $MG['favorite_limit'] > $limit_used ? $MG['favorite_limit'] - $limit_used : 0;
		}
		$head_title = $L['favorite_title'];
	break;
}
include template('favorite', $module);
?>