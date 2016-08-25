<?php
require 'common.inc.php';
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/include/module.func.php';
isset($auth) or $auth = '';
$_auth = decrypt($auth, DT_KEY.'PAY');
$_auth or dheader('channel.php?reload='.$DT_TIME);
list($moduleid, $itemid, $currency, $fee, $title) = explode('|', $_auth);
isset($MODULE[$moduleid]) or dheader('channel.php?reload='.$DT_TIME);
$itemid = intval($itemid);
$itemid or dheader('channel.php?reload='.$DT_TIME);
$fee = dround($fee);
$fee > 0 or dheader('channel.php?reload='.$DT_TIME);
$note = $MODULE[$moduleid]['name'].'/'.$itemid;
if($currency == 'money') {
	if(isset($password)) {
		is_payword($_username, $password) or mobile_msg($L['not_payword']);
		$discount = $MG['discount'] > 0 && $MG['discount'] < 100 ? $MG['discount'] : 100;
		$discount = dround($discount/100);
		$_fee = dround($fee*$discount);
		$_money >= $_fee or mobile_msg($L['need_charge']);	
		$db->query("INSERT INTO {$DT_PRE}finance_pay (moduleid,itemid,username,fee,currency,paytime,ip,title) VALUES ('$moduleid','$itemid','$_username','$fee','$currency','$DT_TIME','$DT_IP','".addslashes($title)."')");
		money_add($_username, -$fee);
		money_record($_username, -$fee, $L['pay_by_site'], 'system', $L['pay_info'], $note);
		mobile_msg($L['pay_success'], mobileurl($moduleid, 0, $itemid));
	} else {
		$head_title = $L['pay_title'].$DT['seo_delimiter'].$head_title;
		$foot = 'channel';
		include template('pay', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	}
} else if($currency == 'credit') {
	if($_credit >= $fee) {
		$db->query("INSERT INTO {$DT_PRE}finance_pay (moduleid,itemid,username,fee,currency,paytime,ip,title) VALUES ('$moduleid','$itemid','$_username','$fee','$currency','$DT_TIME','$DT_IP','".addslashes($title)."')");
		credit_add($_username, -$fee);
		credit_record($_username, -$fee, 'system', $L['pay_info'], $note);
		dheader(mobileurl($moduleid, 0, $itemid));
	} else {
		mobile_msg($L['need_credit']);
	}
} else {
	dheader('channel.php?reload='.$DT_TIME);
}
?>