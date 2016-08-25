<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('misc.lang');
include load('member.lang');
include load('order.lang');
if($submit) {
	require DT_ROOT.'/module/'.$module.'/cart.class.php';
	$do = new cart();
	$cart = $do->get();
	$ids = '';
	if($post) {
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
		$buyer_mobile = $add['mobile'];
		if(!is_mobile($buyer_mobile)) message($L['msg_type_mobile']);
		$buyer_phone = $add['telephone'];
		foreach($post as $k=>$v) {
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
				if($t['m1']) $note .= $t['n1'].':'.$t['m1'].' ';
				if($t['m2']) $note .= $t['n2'].':'.$t['m2'].' ';
				if($t['m3']) $note .= $t['n3'].':'.$t['m3'].' ';
				$v['note'] = str_replace('|', '-', $v['note']);
				$note = dhtmlspecialchars($v['note'].'|'.$note);
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
					$area_id = $add['areaid'];
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
				//send message
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
	}
	$do->set($cart);
	$forward = 'action=order';
	if(!$MOD['checkorder']) {
		if($ids) {
			$ids = substr($ids, 1);
			if(is_numeric($ids)) {
				$forward = 'action=update&step=pay&itemid='.$ids;
			} else {
				$forward = 'action=muti&ids='.$ids;
			}
		}
	}
	dheader('?action=show&auth='.encrypt($forward, DT_KEY.'TURL'));
} else {
	if($action == 'show') {
		$forward = isset($auth) ? decrypt($auth, DT_KEY.'TURL') : '';
		$forward = $MODULE[2]['linkurl'].'trade.php?'.($forward ? $forward : 'action=order');
	} else {
		isset($cart) or $cart = array();
		$lists = $tags = $data = array();
		$itemids = '';
		if($itemid) {
			if(is_array($itemid)) {
				foreach($itemid as $id) {
					$itemids .= ','.$id;
					$k = $id.'-0-0-0';
					$r = array();
					$r['itemid'] = $id;
					$r['s1'] = $r['s2'] = $r['s3'] = $r['a'] = 0;
					$data[$k] = $r;
				}
			} else {
				$s1 = isset($s1) ? intval($s1) : 0;
				$s2 = isset($s2) ? intval($s2) : 0;
				$s3 = isset($s3) ? intval($s3) : 0;
				$a = isset($a) ? intval($a) : 1;
				$itemids .= ','.$itemid;
				$k = $itemid.'-'.$s1.'-'.$s2.'-'.$s3;
				$r = array();
				$r['itemid'] = $itemid;
				$r['s1'] = $s1;
				$r['s2'] = $s2;
				$r['s3'] = $s3;
				$r['a'] = $a;
				$data[$k] = $r;
			}
		} else if($cart) {
			isset($amounts) or $amounts = array();
			foreach($cart as $v) {
				$t = array_map('intval', explode('-', $v));
				$itemids .= ','.$t[0];
				$r = array();
				$r['itemid'] = $t[0];
				$r['s1'] = $t[1];
				$r['s2'] = $t[2];
				$r['s3'] = $t[3];
				$r['a'] = isset($amounts[$v]) ? $amounts[$v] : 1;
				$data[$v] = $r;
			}
		}
		if($itemids) {
			$itemids = substr($itemids, 1);
			$result = $db->query("SELECT * FROM {$table} WHERE itemid IN ($itemids)");
			while($r = $db->fetch_array($result)) {
				if($r['username'] == $_username || $r['status'] != 3) continue;
				$r['alt'] = $r['title'];
				$r['title'] = dsubstr($r['title'], 40, '..');
				$r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
				$r['P1'] = get_nv($r['n1'], $r['v1']);
				$r['P2'] = get_nv($r['n2'], $r['v2']);
				$r['P3'] = get_nv($r['n3'], $r['v3']);
				if($r['step']) {
					$s = unserialize($r['step']);
					foreach(unserialize($r['step']) as $k=>$v) {
						$r[$k] = $v;
					}
				} else {
					$r['a1'] = 1;
					$r['p1'] = $r['price'];
					$r['a2'] = $r['a3'] = 0;
					$r['p2'] = $r['p3'] = 0.00;
				}			
				$tags[$r['itemid']] = $r;
			}
			if($tags) {
				foreach($data as $k=>$v) {
					if(isset($tags[$v['itemid']])) {
						$r = $tags[$v['itemid']];
						$r['key'] = $k;
						$r['s1'] = $v['s1'];
						$r['s2'] = $v['s2'];
						$r['s3'] = $v['s3'];
						$r['a'] = $v['a'];
						if($r['a'] > $r['amount']) $r['a'] = $r['amount'];
						if($r['a'] < $r['a1']) $r['a'] = $r['a1'];
						$r['price'] = get_price($r['a'],$r['price'], $r['step']);
						$r['m1'] = isset($r['P1'][$r['s1']]) ? $r['P1'][$r['s1']] : '';
						$r['m2'] = isset($r['P2'][$r['s2']]) ? $r['P2'][$r['s2']] : '';
						$r['m3'] = isset($r['P3'][$r['s3']]) ? $r['P3'][$r['s3']] : '';
						$lists[] = $r;
					}
				}
			}
		}
		if($lists) {
			$address = array();
			$result = $db->query("SELECT * FROM {$DT_PRE}address WHERE username='$_username' ORDER BY listorder ASC,itemid ASC LIMIT 30");
			while($r = $db->fetch_array($result)) {
				$r['street'] = $r['address'];
				if($r['areaid']) $r['address'] = area_pos($r['areaid'], '').$r['address'];
				$address[] = $r;
			}
			$user = userinfo($_username);
		}
	}
	$head_title = $L['buy_title'];
	include template('buy', $module);
}
?>