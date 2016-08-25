<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$MG['spread'] or dalert(lang('message->without_permission_and_upgrade'), 'goback');
include load('extend.lang');
if($action == 'add') {
	if($kw) {
		$word = $kw;
	} else {
		$word = isset($word) ? dhtmlspecialchars(trim($word)) : '';
	}
	if($word && in_array($mid, array(4, 5, 6))) {
		$word = dhtmlspecialchars(trim($word));
		$this_month = date('n', $DT_TIME);
		$this_year  = date('Y', $DT_TIME);
		$next_month = $this_month == 12 ? 1 : $this_month + 1;
		$next_year  = $this_month == 12 ? $this_year + 1 : $this_year;
		$next_time = strtotime($next_year.'-'.$next_month.'-1');
		$spread_max = $EXT['spread_max'] ? $EXT['spread_max'] : 10;
		$currency = $EXT['spread_currency'];
		$unit = $currency == 'money' ? $DT['money_unit'] : $DT['credit_unit'];
		$r = $db->get_one("SELECT * FROM {$DT_PRE}spread WHERE username='$_username' AND mid=$mid AND word='$word' AND fromtime>=$next_time");
		if($r) message($L['spread_msg_buy'], $EXT['spread_url']);
		$mid or $mid = 5;
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}spread WHERE mid=$mid AND status=3 AND word='$word' AND fromtime>=$next_time");
		if($r['num'] > $spread_max) message(lang($L['spread_msg_over'], array($word)), $EXT['spread_url']);
		$p = $db->get_one("SELECT * FROM {$DT_PRE}spread_price WHERE word='$word'");
		if($mid == 4) {
			$price = $p['company_price'] ? $p['company_price'] : $EXT['spread_company_price'];
		} else if($mid == 5) {
			$price = $p['sell_price'] ? $p['sell_price'] : $EXT['spread_sell_price'];
		} else if($mid == 6) {
			$price = $p['buy_price'] ? $p['buy_price'] : $EXT['spread_buy_price'];
		} else {
			dheader($EXT['spread_url']);
		}
		$step = $EXT['spread_step'];
		$month = $EXT['spread_month'] ? $EXT['spread_month'] : 1;
		if($submit) {
			$buy_price = dround($buy_price);
			if($buy_price < $price) message($L['spread_msg_price_min']);
			if(($buy_price-$price)%$step != 0) message($L['spread_msg_step']);
			$buy_month = intval($buy_month);
			if($buy_month < 1 || $buy_month > $month) message($L['spread_msg_month']);
			$amount = $buy_price*$buy_month;
			if($currency == 'money') {
				if($amount > $_money) message($L['money_not_enough'], $MODULE[2]['linkurl'].'charge.php?action=pay&amount='.($amount-$_money));
				is_payword($_username, $password) or message($L['error_payword']);
			} else {
				if($amount > $_credit) message($L['credit_not_enough'], $MODULE[2]['linkurl'].'trade.php?action=credit');
			}
			$buy_tid = $mid == 4 ? $_userid : intval($buy_tid);
			if(!$buy_tid) message($L['spread_msg_itemid']);
			if($mid == 5 || $mid == 6) {
				$table = get_table($mid);
				$item = $db->get_one("SELECT itemid FROM {$table} WHERE itemid='$buy_tid' AND status=3 AND username='$_username'");
				if(!$item) message($L['spread_msg_yours']);
			}
			$months = $next_month + $buy_month;
			$year = floor($months/12);
			if($months%12 == 0) {
				$to_month = 12;
				$to_year = $next_year + $year - 1;
			} else {
				$to_month = $months%12;
				$to_year = $next_year + $year;
			}
			$totime = strtotime($to_year.'-'.$to_month.'-1');
			$status = $EXT['spread_check'] ? 2 : 3;
			if($currency == 'money') {
				money_add($_username, -$amount);
				money_record($_username, -$amount, $L['in_site'], 'system', $MODULE[$mid]['name'].$L['spread_title'], $word.'('.$L['spread_infoid'].$buy_tid.')');
			} else {
				credit_add($_username, -$amount);
				credit_record($_username, -$amount, 'system', $MODULE[$mid]['name'].$L['spread_title'], $word.'(ID:'.$buy_tid.')');
			}
			$db->query("INSERT INTO {$DT_PRE}spread (mid,tid,word,price,currency,company,username,addtime,fromtime,totime,status) VALUES ('$mid','$buy_tid','$word','$buy_price','$currency','$_company','$_username','$DT_TIME','$next_time','$totime','$status')");
			dmsg($L['spread_msg_success'], '?status='.$status);
		} else {
			//
		}
	} else {
		dheader($EXT['spread_url']);
	}
} else {
	$status = isset($status) ? intval($status) : 3;
	in_array($status, array(2, 3)) or $status = 3;
	$condition = "username='$_username' AND status=$status";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}spread WHERE $condition");
	$pages = pages($r['num'], $page, $pagesize);
	$lists = array();
	$result = $db->query("SELECT * FROM {$DT_PRE}spread WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
	while($r = $db->fetch_array($result)) {
		if($r['totime'] < $DT_TIME) {
			$r['process'] = $L['status_expired'];
		} else if($r['fromtime'] > $DT_TIME) {
			$r['process'] = $L['status_not_start'];
		} else {
			$r['process'] = $L['status_displaying'];
		}
			$r['days'] = $r['totime'] > $DT_TIME ? intval(($r['totime']-$DT_TIME)/86400) : 0;
		$lists[] = $r;
	}
}
$head_title = $L['spread_title'];
$nums = array();
for($i = 2; $i < 4; $i++) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}spread WHERE username='$_username' AND status=$i");
	$nums[$i] = $r['num'];
}
include template('spread', $module);
?>