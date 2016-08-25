<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
require 'common.inc.php';
$itemid or dheader(mobileurl($moduleid));
mobile_login();
$addr = array();
$addr_id = intval(get_cookie('addr_id'));
$addr_url = 'address.php?auth='.encrypt($DT_URL, DT_KEY.'ADDR');
if($addr_id) {
	$t = $db->get_one("SELECT * FROM {$DT_PRE}address WHERE itemid=$addr_id");
	if($t && $t['username'] == $_username) $addr = $t;
}
if(!$addr) $addr = $db->get_one("SELECT * FROM {$DT_PRE}address WHERE username='$_username' ORDER BY listorder ASC,itemid ASC");
if(!$addr) mobile_msg($L['purchase_msg_address'], $addr_url);
if($addr['areaid']) {
	$addr_city = area_pos($addr['areaid'], '');
	if($addr_city && strpos($addr['address'], $addr_city) === false) $addr['address'] = $addr_city.$add['address'];
}

$need_addr = 1;
$order_name = 'trade';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('misc.lang');
include load('member.lang');
include load('order.lang');
$back_link = mobileurl($moduleid, 0, $itemid);
$head_name = $L['purchase_title'];
$head_title = $head_name.$DT['seo_delimiter'].$MOD['name'].$DT['seo_delimiter'].$head_title;
$foot = '';
switch($module) {
	case 'mall':
		$itemid or dheader(mobileurl($moduleid));
		$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		if(!$item || $item['status'] != 3) mobile_msg($L['purchase_msg_goods']);
		if($item['username'] == $_username) mobile_msg($L['purchase_msg_self']);
		
		$s1 = isset($s1) ? intval($s1) : 0;
		$s2 = isset($s2) ? intval($s2) : 0;
		$s3 = isset($s3) ? intval($s3) : 0;
		$a = isset($a) ? intval($a) : 1;
		$item['P1'] = get_nv($item['n1'], $item['v1']);
		$item['P2'] = get_nv($item['n2'], $item['v2']);
		$item['P3'] = get_nv($item['n3'], $item['v3']);
		if($item['step']) {
			$s = unserialize($item['step']);
			foreach(unserialize($item['step']) as $k=>$v) {
				$item[$k] = $v;
			}
		} else {
			$item['a1'] = 1;
			$item['p1'] = $item['price'];
			$item['a2'] = $item['a3'] = 0;
			$item['p2'] = $item['p3'] = 0.00;
		}
		$item['s1'] = $s1;
		$item['s2'] = $s2;
		$item['s3'] = $s3;
		$item['a'] = $a;
		if($item['a'] > $item['amount']) $item['a'] = $item['amount'];
		if($item['a'] < $item['a1']) $item['a'] = $item['a1'];
		$item['price'] = get_price($item['a'],$item['price'], $item['step']);
		$item['m1'] = isset($item['P1'][$item['s1']]) ? $item['P1'][$item['s1']] : '';
		$item['m2'] = isset($item['P2'][$item['s2']]) ? $item['P2'][$item['s2']] : '';
		$item['m3'] = isset($item['P3'][$item['s3']]) ? $item['P3'][$item['s3']] : '';
		$item['minamount'] = $item['a1'];
		$item['unit'] or $item['unit'] = $L['unit'];
		$t = $item;
		if(isset($_POST['ok'])) {
			$buyer_postcode = addslashes($addr['postcode']);
			$buyer_address = addslashes($addr['address']);
			$buyer_name = addslashes($addr['truename']);
			$buyer_phone = addslashes($addr['telephone']);
			$buyer_mobile = addslashes($addr['mobile']);
			if(!$need_addr) {
				$buyer_mobile = input_trim($mobile);
				is_mobile($buyer_mobile) or exit('mobile');
			}
			$number = intval($number);
			if($number < $t['a1']) $number = $t['a1'];
			if($number > $t['amount']) $number = $t['amount'];
			if($number < 1) $number = 1;
			$price = get_price($number, $t['price'], $t['step']);
			$amount = $number*$price;			
			$_note = convert(input_trim($note), 'UTF-8', DT_CHARSET);
			$note = '';
			$t['m1'] = isset($t['P1'][$t['s1']]) ? $t['P1'][$t['s1']] : '';
			$t['m2'] = isset($t['P2'][$t['s2']]) ? $t['P2'][$t['s2']] : '';
			$t['m3'] = isset($t['P3'][$t['s3']]) ? $t['P3'][$t['s3']] : '';
			if($t['m1']) $note .= $t['n1'].':'.$t['m1'].' ';
			if($t['m2']) $note .= $t['n2'].':'.$t['m2'].' ';
			if($t['m3']) $note .= $t['n3'].':'.$t['m3'].' ';

			$_note = str_replace('|', '-', $_note);
			$note = dhtmlspecialchars($_note.'|'.$note);
			$title = addslashes($t['title']);
			$linkurl = $MOD['linkurl'].$t['linkurl'];
			$status = $MOD['checkorder'] ? 0 : 1;
			$cod = 0;
			if($t['cod'] == 2) {
				$cod = $cod == 0 ? 0 : 1;
			} else if($t['cod'] == 1) {
				$cod = 1;
			}
			if($cod) $status = 7;
			if($t['express_name_1'] == $L['post_free']) {
				if($t['fee_start_1'] > 0) {
					if($amount >= $t['fee_start_1']) $express = 0;
				} else {
					$express = 0;
				}
			}
			$express = intval($express);
			if($express && in_array($express, array(1,2,3))) {
				$i = $express;
				$fee_name = $t['express_name_'.$i];
				$fee = dround($t['fee_start_'.$i] + $t['fee_step_'.$i]*($number-1));
				$express_id = $t['express_'.$i];
				$area_id = isset($addr['areaid']) ? $addr['areaid'] : 0;
				if($express_id && $area_id) {
					$E = $db->get_one("SELECT * FROM {$DT_PRE}mall_express WHERE itemid=$express_id");
					if($E && $E['items'] > 0) {
						$AREA = cache_read('area.php');
						$aid = $area_id;
						$ii = 0;
						do {
							$E = $db->get_one("SELECT * FROM {$DT_PRE}mall_express WHERE parentid=$express_id AND areaid=$aid");
							if($E) {
								$fee = dround($E['fee_start'] + $E['fee_step']*($number-1));
								break;
							} else {
								$aid = $AREA[$aid]['parentid'];
							}
							if($ii++ > 5) break;//safe
						} while($aid > 0);
					}
				}
			} else {
				$fee_name = '';
				$fee = 0;
			}
			$db->query("INSERT INTO {$DT_PRE}mall_order (mid,mallid,buyer,seller,title,thumb,price,number,amount,addtime,updatetime,note, buyer_postcode,buyer_address,buyer_name,buyer_phone,buyer_mobile,status,fee_name,fee,cod) VALUES ('$moduleid','$itemid','$_username','$t[username]','$title','$t[thumb]','$price','$number','$amount','$DT_TIME','$DT_TIME','$note','$buyer_postcode','$buyer_address','$buyer_name','$buyer_phone','$buyer_mobile','$status','$fee_name','$fee','$cod')");
			$oid = $db->insert_id();
			$db->query("REPLACE INTO {$DT_PRE}mall_comment (itemid,mallid,buyer,seller) VALUES ('$oid','$itemid','$_username','$t[username]')");
			$tmp = $db->get_one("SELECT mallid FROM {$DT_PRE}mall_stat WHERE mallid=$itemid");
			if(!$tmp) $db->query("REPLACE INTO {$DT_PRE}mall_stat (mallid,buyer,seller) VALUES ('$itemid','$_username','$t[username]')");
			$touser = $t['username'];
			$_title = $title;
			$title = lang($L['trade_message_t6'], array($oid));
			$url = $MODULE[2]['linkurl'].'trade.php?itemid='.$oid;
			$goods = '<a href="'.$linkurl.'" target="_blank" class="t"><strong>'.$_title.'</strong></a>';
			$content = lang($L['trade_message_c6'], array(userurl($_username), $_username, timetodate($DT_TIME, 3), $goods, $oid, $amount, $url));
			$content = ob_template('messager', 'mail');
			send_message($touser, $title, $content);
			exit('ok|'.$oid);
		}
	break;
	case 'group':
		$itemid or dheader(mobileurl($moduleid));
		$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		if(!$item || $item['status'] != 3) mobile_msg($L['purchase_msg_goods']);
		if($item['username'] == $_username) mobile_msg($L['purchase_msg_self']);
		if($item['process'] == 2) mobile_msg($L['purchase_msg_group_finish']);
		if(!$item['logistic']) $need_addr = 0;
		$item['minamount'] = $item['amount'] = 0;
		$t = $item;
		$order_name = 'group';
		if(isset($_POST['ok'])) {
			$buyer_postcode = addslashes($addr['postcode']);
			$buyer_address = addslashes($addr['address']);
			$buyer_name = addslashes($addr['truename']);
			$buyer_phone = addslashes($addr['telephone']);
			$buyer_mobile = addslashes($addr['mobile']);
			if(!$need_addr) {
				$buyer_mobile = input_trim($mobile);
				is_mobile($buyer_mobile) or exit('mobile');
			}
			$number = intval($number);
			if($number < 1) $number = 1;
			$price = $item['price'];
			$amount = $number*$price;
			$note = convert(input_trim($note), 'UTF-8', DT_CHARSET);
			$note = dhtmlspecialchars($note);
			$title = addslashes($item['title']);			
			$db->query("INSERT INTO {$DT_PRE}group_order (gid,buyer,seller,title,thumb,price,number,amount,logistic,addtime,updatetime,note, buyer_postcode,buyer_address,buyer_name,buyer_phone,buyer_mobile,status) VALUES ('$itemid','$_username','$item[username]','$title','$item[thumb]','$item[price]','$number','$amount','$item[logistic]','$DT_TIME','$DT_TIME','$note','$buyer_postcode','$buyer_address','$buyer_name','$buyer_phone','$buyer_mobile', 6)");
			$oid = $db->insert_id();
			exit('ok|'.$oid);
		}
	break;
	case 'sell':
		$itemid or dheader(mobileurl($moduleid));
		SELL_ORDER or dheader(mobileurl($moduleid, 0, $itemid));
		$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		if(!$item || $item['status'] != 3 || $item['price'] < 0.01 || $item['amount'] < 1 || $item['minamount'] < 1 ||  !$item['unit']) mobile_msg($L['purchase_msg_online_buy']);
		if($item['username'] == $_username) mobile_msg($L['purchase_msg_self']);
		$t = $item;
		if(isset($_POST['ok'])) {
			$buyer_postcode = addslashes($addr['postcode']);
			$buyer_address = addslashes($addr['address']);
			$buyer_name = addslashes($addr['truename']);
			$buyer_phone = addslashes($addr['telephone']);
			$buyer_mobile = addslashes($addr['mobile']);
			$number = intval($number);
			if($number < $item['minamount']) $number = $item['minamount'];
			if($number > $item['amount']) $number = $item['amount'];
			if($number < 1) $number = 1;
			$price = $item['price'];
			$amount = $number*$price;
			$note = convert(input_trim($note), 'UTF-8', DT_CHARSET);
			$note = dhtmlspecialchars($note);
			$title = addslashes($item['title']);
			$linkurl = $MOD['linkurl'].$item['linkurl'];
			$status = $MOD['checkorder'] ? 0 : 1;
			$fee_name = '';
			$fee = $cod = 0;
			$db->query("INSERT INTO {$DT_PRE}mall_order (mid,mallid,buyer,seller,title,thumb,price,number,amount,addtime,updatetime,note, buyer_postcode,buyer_address,buyer_name,buyer_phone,buyer_mobile,status,fee_name,fee,cod) VALUES ('$moduleid','$itemid','$_username','$item[username]','$title','$item[thumb]','$price','$number','$amount','$DT_TIME','$DT_TIME','$note','$buyer_postcode','$buyer_address','$buyer_name','$buyer_phone','$buyer_mobile','$status','$fee_name','$fee','$cod')");
			$oid = $db->insert_id();
			//send message
			$touser = $item['username'];
			$_title = $title;
			$title = lang($L['trade_message_t6'], array($oid));
			$url = $MODULE[2]['linkurl'].'trade.php?itemid='.$oid;
			$goods = '<a href="'.$linkurl.'" target="_blank" class="t"><strong>'.$_title.'</strong></a>';
			$content = lang($L['trade_message_c6'], array(userurl($_username), $_username, timetodate($DT_TIME, 3), $goods, $oid, $amount, $url));
			$content = ob_template('messager', 'mail');
			send_message($touser, $title, $content);
			exit('ok|'.$oid);
		}
	break;
	default:
		dheader('index.php?reload='.$DT_TIME);
	break;
}
include template('purchase', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>