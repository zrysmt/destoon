<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
require MD_ROOT.'/member.class.php';
$do = new member;
$do->userid = $_userid;
$user = $do->get_one();
if(!$MG['vip'] || !$MG['fee'] || $user['totime'] < $DT_TIME) dheader($MOD['linkurl']);
if($action == 'renew') {
	if($submit) {
		is_payword($_username, $password) or message($L['error_payword']);
		$year = intval($year);
		in_array($year, array(1, 2, 3)) or $year = 1;
		$fee = dround($MG['fee']*$year);
		$fee > 0 or message($L['vip_msg_fee']);
		$fee <= $_money or message($L['money_not_enough'], 'charge.php?action=pay&reason=vip&amount='.($fee-$_money));
		$totime = $user['totime'] + 365*86400*$year;
		money_add($_username, -$fee);
		money_record($_username, -$fee, $L['in_site'], 'system', $L['vip_title'], lang($L['vip_record'], array($year, timetodate($totime, 3))));
		$db->query("UPDATE {$DT_PRE}company SET totime=$totime WHERE userid=$_userid");
		dmsg($L['vip_msg_success'], '?action=index');
	} else {
		$year = 1;
		if($sum > 1 && $sum < 4) $year = $sum;
		$fee = dround($MG['fee']*$year);
		$head_title = $L['vip_renew'];
	}
} else {
	$fromdate = timetodate($user['fromtime'], 3);
	$head_title = $L['vip_title'];
}
$havedays = ceil(($user['totime']-$DT_TIME)/86400);
$todate = timetodate($user['totime'], 3);
include template('vip', $module);
?>