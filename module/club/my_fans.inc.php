<?php 
defined('IN_DESTOON') or exit('Access Denied');
require MD_ROOT.'/fans.class.php';
$do = new fans();
if($action) {
	($itemid && is_array($itemid)) or message($L['my_choose_fans']);
	foreach($itemid as $fid) {
		$F = $db->get_one("SELECT * FROM {$table}_fans WHERE itemid=$fid");
		$GRP = get_group($F['gid']);
		($GRP && $GRP['status'] == 3) or message($L['my_not_group']);
		($GRP['username'] == $_username) or message($L['my_not_admin']);
	}
}
switch($action) {
	case 'delete':
		$do->recycle($itemid);
		dmsg($L['success_delete'], $forward);
	break;
	case 'cancel':
		$do->check($itemid, 2);
		dmsg($L['success_cancel'], $forward);
	break;
	case 'check':
		$do->check($itemid);
		dmsg($L['success_checked'], $forward);
	break;
	case 'reject':
		$do->reject($itemid);
		dmsg($L['success_reject'], $forward);
	break;
	default:
		$ids = '';
		$result = $db->query("SELECT itemid FROM {$table}_group WHERE username='$_username'");
		while($r = $db->fetch_array($result)) {
			$ids .= ','.$r['itemid'];
		}
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(1, 2, 3)) or $status = 3;
		$condition = $ids ? "gid IN (".substr($ids, 1).")" : "gid=0";
		$nums = array();
		for($i = 1; $i < 4; $i++) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_fans WHERE $condition AND status=$i");
			$nums[$i] = $r['num'];
		}		
		$sfields = $L['my_fans_fields'];
		$dfields = array('username', 'reason');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition .= " AND status=$status";
		if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
		$lists = $do->get_list($condition);
	break;
}
$head_title = $L['my_fans_title'];
?>