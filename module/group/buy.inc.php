<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('misc.lang');
include load('member.lang');
include load('order.lang');
if($action == 'show') {
	$forward = isset($auth) ? decrypt($auth, DT_KEY.'TURL') : '';
	$forward = $MODULE[2]['linkurl'].'group.php?'.($forward ? $forward : 'action=order');
	$head_title = $L['buy_title'];
	include template('buy', $module);
	exit;
}
$itemid or dheader($MOD['linkurl']);
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
if($item && $item['status'] > 2) {
	if($item['process'] == 2) message($L['group_expired']);
	if($item['username'] == $_username) message($L['buy_self']);
} else {
	message(lang('message->item_not_exists'), $MOD['linkurl']);
}
$user = userinfo($_username);
if($submit) {
	if($item['logistic']) {
		$add = array_map('trim', $add);
		$add_city = area_pos($add['areaid'], '');
		if($add_city && strpos($add['address'], $add_city) === false) $add['address'] = $add_city.$add['address'];
		$add = dhtmlspecialchars($add);
		$buyer_address = $add['address'];
		if(strlen($buyer_address) < 10) message($L['msg_type_address']);
		$buyer_postcode = $add['postcode'];
		if(strlen($buyer_postcode) < 6) message($L['msg_type_postcode']);
		$buyer_name = $add['truename'];
		if(strlen($buyer_name) < 2) message($L['msg_type_truename']);
		$buyer_phone = $add['telephone'];
	} else {
		$buyer_address = dhtmlspecialchars($user['address']);
		$buyer_postcode = dhtmlspecialchars($user['postcode']);
		$buyer_name = dhtmlspecialchars($user['truename']);
		$buyer_phone = dhtmlspecialchars($user['telephone']);
	}
	$buyer_mobile = dhtmlspecialchars($add['mobile']);
	is_mobile($buyer_mobile) or message($L['msg_type_mobile']);
	$number = intval($number);
	if($number < 1) $number = 1;
	$amount = $number*$item['price'];
	$note = dhtmlspecialchars($note);
	$title = addslashes($item['title']);
	$db->query("INSERT INTO {$DT_PRE}group_order (gid,buyer,seller,title,thumb,price,number,amount,logistic,addtime,updatetime,note, buyer_postcode,buyer_address,buyer_name,buyer_phone,buyer_mobile,status) VALUES ('$itemid','$_username','$item[username]','$title','$item[thumb]','$item[price]','$number','$amount','$item[logistic]','$DT_TIME','$DT_TIME','$note','$buyer_postcode','$buyer_address','$buyer_name','$buyer_phone','$buyer_mobile', 6)");
	$oid = $db->insert_id();
	dheader('?action=show&auth='.encrypt('action=update&step=pay&itemid='.$oid, DT_KEY.'TURL'));
} else {
	$_MOD = cache_read('module-2.php');
	$result = $db->query("SELECT * FROM {$DT_PRE}address WHERE username='$_username' ORDER BY listorder ASC,itemid ASC LIMIT 30");
	$address = array();
	while($r = $db->fetch_array($result)) {
		$r['street'] = $r['address'];
		if($r['areaid']) $r['address'] = area_pos($r['areaid'], '').$r['address'];
		$address[] = $r;
	}
	$send_types = explode('|', trim($_MOD['send_types']));
	$head_title = $L['buy_title'];
	include template('buy', $module);
}
?>