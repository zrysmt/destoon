<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
switch($action) {
	case 'add':
		if($submit) {
			$num = intval($num);
			$num >= 1 or $num = 1;
			$money = $MOD['deposit']*$num;
			$money <= $_money or dheader('charge.php?action=pay&reason=deposit|'.$num.'&amount='.($money - $_money));
			is_payword($_username, $password) or message($L['error_payword']);
			money_add($_username, -$money);
			money_record($_username, -$money, $L['in_site'], 'system', $L['deposit_title_add']);
			$db->query("INSERT INTO {$DT_PRE}finance_deposit (username,amount,addtime,editor) VALUES ('$_username','$money','$DT_TIME','$_username')");
			$db->query("UPDATE {$DT_PRE}member SET deposit=deposit+$money WHERE userid=$_userid");
			dmsg($L['op_success'], '?action=index');
		} else {
			$amount = $MOD['deposit'];
			if($sum > 1) $amount = $MOD['deposit']*$sum;
			$head_title = $L['deposit_title_add'];
		}
	break;
	default:
		$condition = "username='$_username'";
		isset($fromtime) or $fromtime = '';
		isset($totime) or $totime = '';
		if($fromtime) $condition .= " AND addtime>".(strtotime($fromtime.' 00:00:00'));
		if($totime) $condition .= " AND addtime<".(strtotime($totime.' 23:59:59'));
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}finance_deposit WHERE $condition");
		$pages = pages($r['num'], $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}finance_deposit WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
		$amount = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$amount += $r['amount'];
			$lists[] = $r;
		}
		$amount = number_format($amount, 2, '.', ',');
		$head_title = $L['deposit_title'];
	break;
}
include template('deposit', $module);
?>