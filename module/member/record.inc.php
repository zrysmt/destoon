<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$condition = "username='$_username'";
switch($action) {
	case 'pay':
		$MODULE[-9]['name'] = $L['resume_name'];
		$MODULE[-9]['islink'] = 0;
		$MODULE[-9]['linkurl'] = $MODULE[9]['linkurl'];
		isset($fromtime) or $fromtime = '';
		isset($totime) or $totime = '';
		isset($dfromtime) or $dfromtime = '';
		isset($dtotime) or $dtotime = '';
		isset($currency) or $currency = '';
		$module_select = module_select('mid', $L['module_name'], $mid);
		if($keyword) $condition .= " AND title LIKE '%$keyword%'";
		if($fromtime) $condition .= " AND paytime>".(strtotime($fromtime.' 00:00:00'));
		if($totime) $condition .= " AND paytime<".(strtotime($totime.' 23:59:59'));
		if($mid) $condition .= " AND moduleid=$mid";
		if($itemid) $condition .= " AND itemid=$itemid";
		if($currency) $condition .= " AND currency='$currency'";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}finance_pay WHERE $condition");
		$pages = pages($r['num'], $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}finance_pay WHERE $condition ORDER BY pid DESC LIMIT $offset,$pagesize");
		$fee = 0;
		while($r = $db->fetch_array($result)) {
			$r['paytime'] = timetodate($r['paytime'], 5);
			$fee += $r['fee'];
			$lists[] = $r;
		}
		$head_title = $L['record_title_pay'];	
	break;
	case 'login':
		$DT['login_log'] == 2 or dheader($MOD['linkurl']);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}login WHERE $condition ORDER BY logid DESC LIMIT 0,15");
		while($r = $db->fetch_array($result)) {
			$r['logintime'] = timetodate($r['logintime'], 5);
			$r['area'] = ip2area($r['loginip']);
			$lists[] = $r;
		}
		$head_title = $L['record_title_login'];	
	break;
	default:
		$BANKS = explode('|', trim($MOD['pay_banks']));
		$sfields = $L['record_sfields'];
		$dfields = array('reason', 'amount', 'bank', 'reason', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($fromtime) or $fromtime = '';
		isset($totime) or $totime = '';
		isset($type) or $type = 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
		if($fromtime) $condition .= " AND addtime>".(strtotime($fromtime.' 00:00:00'));
		if($totime) $condition .= " AND addtime<".(strtotime($totime.' 23:59:59'));
		if($type) $condition .= $type == 1 ? " AND amount>0" : " AND amount<0" ;
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}finance_record WHERE $condition");
		$pages = pages($r['num'], $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}finance_record WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['amount'] > 0 ? $income += $r['amount'] : $expense += $r['amount'];
			$lists[] = $r;
		}
		$head_title = $L['record_title'];	
	break;
}
include template('record', $module);
?>