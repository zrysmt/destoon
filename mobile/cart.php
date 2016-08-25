<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
$moduleid = 16;
require 'common.inc.php';
mobile_login();
$addr = array();
$addr_id = intval(get_cookie('addr_id'));
$addr_url = 'address.php?auth='.encrypt($DT_URL, DT_KEY.'ADDR');
if($addr_id) {
	$t = $db->get_one("SELECT * FROM {$DT_PRE}address WHERE itemid=$addr_id");
	if($t && $t['username'] == $_username) $addr = $t;
}
if(!$addr) $addr = $db->get_one("SELECT * FROM {$DT_PRE}address WHERE username='$_username' ORDER BY listorder ASC,itemid ASC");
if(!$addr) mobile_msg($L['msg_no_address'], $addr_url);
if($addr['areaid']) $addr['address'] = area_pos($addr['areaid'], '').$addr['address'];
$need_addr = 1;
$order_name = 'trade';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/module/'.$module.'/cart.class.php';
include load('misc.lang');
include load('member.lang');
include load('order.lang');
$back_link = mobileurl($moduleid, 0, $itemid);
$head_name = $L['cart_title'];
$head_title = $head_name.$DT['seo_delimiter'].$MOD['name'].$DT['seo_delimiter'].$head_title;
$foot = '';
$do = new cart();
$do->max = intval($MOD['max_cart']);
$cart = $do->get();
switch($action) {
	case 'clear':
		$do->clear();
		exit('ok');
	break;
	case 'delete':
		(isset($key) && $key && is_array($key)) or exit('ko');
		foreach($key as $k) {
			if(isset($cart[$k])) {
				unset($cart[$k]);
			}
		}
		$do->set($cart);
		exit('ok');
	break;
	default:
		if(isset($_POST['ok'])) {
			$ids = '';
			$buyer_postcode = addslashes($addr['postcode']);
			$buyer_address = addslashes($addr['address']);
			$buyer_name = addslashes($addr['truename']);
			$buyer_phone = addslashes($addr['telephone']);
			$buyer_mobile = addslashes($addr['mobile']);
			if(!$need_addr) {
				$buyer_mobile = input_trim($mobile);
				is_mobile($buyer_mobile) or exit('mobile');
			}
			foreach($post as $k=>$v) {
				if($v['checked'] == 0) continue;
				$t1 = array_map('intval', explode('-', $k));
				$itemid = $t1[0];
				$s1 = $t1[1];
				$s2 = $t1[2];
				$s3 = $t1[3];
				$t = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
				if($t && $t['status'] == 3 && $t['username'] != $_username && $t['amount'] > 0) {
					if($t['step']) {
						$s = unserialize($t['step']);
						foreach(unserialize($t['step']) as $_k=>$_v) {
							$t[$_k] = $_v;
						}
					} else {
						$t['a1'] = 1;
						$t['p1'] = $t['price'];
						$t['a2'] = $t['a3'] = 0;
						$t['p2'] = $t['p3'] = 0.00;
					}
					$number = intval($v['number']);
					if($number < $t['a1']) $number = $t['a1'];
					if($number > $t['amount']) $number = $t['amount'];
					if($number < 1) $number = 1;
					$price = get_price($number, $t['price'], $t['step']);
					$amount = $number*$price;			
					$_note = convert(input_trim($v['note']), 'UTF-8', DT_CHARSET);
					$note = '';
					$t['P1'] = get_nv($t['n1'], $t['v1']);
					$t['P2'] = get_nv($t['n2'], $t['v2']);
					$t['P3'] = get_nv($t['n3'], $t['v3']);
					$t['s1'] = $s1;
					$t['s2'] = $s2;
					$t['s3'] = $s3;
					$t['m1'] = isset($t['P1'][$t['s1']]) ? $t['P1'][$t['s1']] : '';
					$t['m2'] = isset($t['P2'][$t['s2']]) ? $t['P2'][$t['s2']] : '';
					$t['m3'] = isset($t['P3'][$t['s3']]) ? $t['P3'][$t['s3']] : '';
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
						if(isset($v['cod'])) $cod = 1;
					} else if($t['cod'] == 1) {
						$cod = 1;
					}
					if($cod) $status = 7;
					if($t['express_name_1'] == $L['post_free']) {
						if($t['fee_start_1'] > 0) {
							if($amount >= $t['fee_start_1']) $v['express'] = 0;
						} else {
							$v['express'] = 0;
						}
					}
					$express = intval($v['express']);
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
					if(!$cod) $ids .= ','.$oid;
					$touser = $t['username'];
					$_title = $title;
					$title = lang($L['trade_message_t6'], array($oid));
					$url = $MODULE[2]['linkurl'].'trade.php?itemid='.$oid;
					$goods = '<a href="'.$linkurl.'" target="_blank" class="t"><strong>'.$_title.'</strong></a>';
					$content = lang($L['trade_message_c6'], array(userurl($_username), $_username, timetodate($DT_TIME, 3), $goods, $oid, $amount, $url));
					$content = ob_template('messager', 'mail');
					send_message($touser, $title, $content);
					unset($cart[$k]);
				}
			}
			$do->set($cart);
			if($ids) $ids = substr($ids, 1);
			exit($ids ? 'ok|'.$ids : 'ko');
		} else {
			$lists = $do->get_list($cart);
		}
	break;
}
include template('cart', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>