<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
SELL_ORDER or dalert(lang('message->without_permission'), 'goback');
require DT_ROOT.'/include/post.func.php';
include load('misc.lang');
include load('member.lang');
include load('order.lang');
if($submit) {
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
			$itemid = intval($k);
			$t = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
			if($t && $t['status'] == 3 && $t['username'] != $_username && $t['price'] > 0 && $t['amount'] > 0 && $t['minamount'] > 0 &&  $t['unit']) {
				$number = intval($v['number']);				
				if($number < $t['minamount']) $number = $t['minamount'];
				if($number > $t['amount']) $number = $t['amount'];
				if($number < 1) $number = 1;
				$price = $t['price'];
				$amount = $number*$price;
				$v['note'] = str_replace('|', '-', $v['note']);
				$note = dhtmlspecialchars($v['note']);
				$title = addslashes($t['title']);
				$linkurl = $MOD['linkurl'].$t['linkurl'];
				$status = $MOD['checkorder'] ? 0 : 1;
				$fee_name = '';
				$fee = $cod = 0;
				$db->query("INSERT INTO {$DT_PRE}mall_order (mid,mallid,buyer,seller,title,thumb,price,number,amount,addtime,updatetime,note, buyer_postcode,buyer_address,buyer_name,buyer_phone,buyer_mobile,status,fee_name,fee,cod) VALUES ('$moduleid','$itemid','$_username','$t[username]','$title','$t[thumb]','$price','$number','$amount','$DT_TIME','$DT_TIME','$note','$buyer_postcode','$buyer_address','$buyer_name','$buyer_phone','$buyer_mobile','$status','$fee_name','$fee','$cod')");
				$oid = $db->insert_id();
				$ids .= ','.$oid;
				//send message
				$touser = $t['username'];
				$_title = $title;
				$title = lang($L['trade_message_t6'], array($oid));
				$url = $MODULE[2]['linkurl'].'trade.php?itemid='.$oid;
				$goods = '<a href="'.$linkurl.'" target="_blank" class="t"><strong>'.$_title.'</strong></a>';
				$content = lang($L['trade_message_c6'], array(userurl($_username), $_username, timetodate($DT_TIME, 3), $goods, $oid, $amount, $url));
				$content = ob_template('messager', 'mail');
				send_message($touser, $title, $content);
			}
		}
	}
	$forward = 'action=order';
	if(!$MOD['checkorder']) {
		if($ids) {
			$ids = substr($ids, 1);
			if(is_numeric($ids)) {
				$forward = 'action=update&step=pay&itemid='.$ids;
			} else {
				$forward = 'action=muti&itemids='.$ids;
			}
		}
	}
	dheader('?action=show&auth='.encrypt($forward, DT_KEY.'TURL'));
} else {
	if($action == 'show') {
		$forward = isset($auth) ? decrypt($auth, DT_KEY.'TURL') : '';
		$forward = $MODULE[2]['linkurl'].'trade.php?'.($forward ? $forward : 'action=order');
	} else {
		$lists = $tags = $data = array();
		$itemids = '';
		if($itemid) {
			if(is_array($itemid)) {
				foreach($itemid as $id) {
					$itemids .= ','.$id;
					$data[$id] = $id;
				}
			} else {
				$itemids .= ','.$itemid;
				$data[$itemid] = $itemid;
			}
		}
		if($itemids) {
			$itemids = substr($itemids, 1);
			$result = $db->query("SELECT * FROM {$table} WHERE itemid IN ($itemids)");
			while($r = $db->fetch_array($result)) {
				if($r['username'] == $_username || $r['status'] != 3 || $r['price'] < 0.01 || $r['amount'] < 1 || $r['minamount'] < 1 ||  !$r['unit']) continue;
				$r['alt'] = $r['title'];
				$r['title'] = dsubstr($r['title'], 40, '..');
				$r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
				$r['key'] = $r['itemid'];
				$tags[$r['itemid']] = $r;
			}
			if($tags) {
				foreach($data as $k=>$v) {
					if(isset($tags[$k])) {
						$lists[] = $tags[$k];
					}
				}
			}
		}
		if($lists) {
			$address = array();
			$result = $db->query("SELECT * FROM {$DT_PRE}address WHERE username='$_username' ORDER BY  listorder ASC,itemid ASC LIMIT 30");
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