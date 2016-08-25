<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('生成优惠码', '?moduleid='.$moduleid.'&file='.$file.'&action=add'),
    array('优惠码管理', '?moduleid='.$moduleid.'&file='.$file),
);
$table = $DT_PRE.'finance_promo';
switch($action) {
	case 'add':
		if($submit) {
			$amount = dround($amount);
			if($amount <= 0) msg('优惠额度格式错误');
			$prefix_length = strlen($prefix);
			$number_length = intval($number_length);
			if($number_length < 8) msg('优惠码不能少于8位');
			$rand_length = $number_length - $prefix_length;
			if($rand_length < 4)  msg('优惠码长度和前缀长度差不能少于4位');
			$number_part = trim($number_part);
			if(!preg_match("/^[0-9a-zA-z]{6,}$/", $number_part)) msg('优惠码只能由6位以上数字和字母组成');
			$totime = strtotime($totime);
			if($totime < $DT_TIME) msg('过期时间必须在当前时间之后');
			$total = intval($total);
			$total or $total = 100;
			$reuse = $reuse ? 1 : 0;
			$t = 0;
			for($i = 0; $i < $total; $i++) {
				$number = $prefix.random($rand_length, $number_part);
				if($db->get_one("SELECT itemid FROM {$table} WHERE number='$number'")) {
					$i--;
				} else {
					$t++;
					$db->query("INSERT INTO {$table} (number,type,amount,reuse,editor,addtime,totime) VALUES('$number','$type','$amount','$reuse','$_username','$DT_TIME','$totime')");
				}
			}
			msg('成功生成 '.$t.' 个', '?moduleid='.$moduleid.'&file='.$file);
		} else {
			$prefix = strtoupper(random(4));
			$totime = (timetodate($DT_TIME, "Y") + 3).timetodate($DT_TIME, '-m-d');
			include tpl('promo_add', $module);
		}
	break;
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	default:
		$print = isset($print) ? 1 : 0;
		$sfields = array('按条件', '优惠码', '密码', '面额', '会员', 'IP', '操作人');
		$dfields = array('number', 'number', 'password', 'amount', 'username', 'ip', 'editor');
		$sorder  = array('排序方式', '面额降序', '面额升序', '使用时间降序', '使用时间升序', '到期时间降序', '到期时间升序', '制作时间降序', '制作时间升序');
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'updatetime DESC', 'updatetime ASC', 'totime DESC', 'totime ASC', 'addtime DESC', 'addtime ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($username) or $username = '';
		isset($number) or $number = '';
		isset($fromtime) or $fromtime = '';
		isset($totime) or $totime = '';
		isset($dfromtime) or $dfromtime = '';
		isset($dtotime) or $dtotime = '';
		isset($type) or $type = 0;
		isset($minamount) or $minamount = '';
		isset($maxamount) or $maxamount = '';
		isset($status) or $status = 0;
		isset($timetype) or $timetype = 'updatetime';
		isset($order) && isset($dorder[$order]) or $order = 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$condition = '1';
		if($keyword) $condition .= " AND $dfields[$fields]='$keyword'";
		if($print) $condition .= " AND updatetime=0  AND totime>$DT_TIME";

		if($fromtime) $condition .= " AND $timetype>".(strtotime($fromtime.' 00:00:00'));
		if($totime) $condition .= " AND $timetype<".(strtotime($totime.' 23:59:59'));
		if($username) $condition .= " AND username='$username'";
		if($number) $condition .= " AND number='$number'";
		if($minamount != '') $condition .= " AND amount>=$minamount";
		if($maxamount != '') $condition .= " AND amount<=$maxamount";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY $dorder[$order] LIMIT $offset,$pagesize");
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['totime'] = timetodate($r['totime'], 3);
			$r['updatetime'] = $r['updatetime'] ? ($r['updatetime'] > 1260000000 ? timetodate($r['updatetime'], 5) : $r['updatetime']) : '';			
			$lists[] = $r;
		}
		include tpl('promo', $module);
	break;
}
?>