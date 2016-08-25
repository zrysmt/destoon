<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MG['homepage'] && $MG['style'] or dalert(lang('message->without_permission_and_upgrade'), 'goback');
require DT_ROOT.'/include/post.func.php';
require MD_ROOT.'/style.class.php';
$do = new style();
$user = userinfo($_username);
$domain = $user['domain'];
if($itemid) {
	$do->itemid = $itemid;
	$r = $do->get_one();
	$r or message($L['style_msg_not_exist']);
	if($r['groupid']) {
		$groupids = explode(',', $r['groupid']);
		if(!in_array($_groupid, $groupids)) message($L['style_msg_group']);
	}
	if($action == 'buy' && $r['fee']) {
		$currency = $r['currency'];
		$months = array(1, 2, 3, 6, 12, 24);
		$unit = $currency == 'money' ? $DT['money_unit'] : $DT['credit_unit'];
		if($submit) {			
			in_array($month, $months) or message($L['style_msg_month']);
			$amount = $r['fee']*$month;
			if($currency == 'money') {
				if($amount > $_money) message($L['money_not_enough'], $MODULE[2]['linkurl'].'charge.php?action=pay&amount='.($amount-$_money));
				is_payword($_username, $password) or message($L['error_payword']);
				money_add($_username, -$amount);
				money_record($_username, -$amount, $L['in_site'], 'system', $L['pay_in_site'], lang($L['style_record_buy'], array($r['title'], $month)));
				$fd = 'money';
			} else {
				if($amount > $_credit) message($L['credit_not_enough'], $MODULE[2]['linkurl'].'credit.php?action=buy&amount='.($amount-$_credit));
				credit_add($_username, -$amount);
				credit_record($_username, -$amount, 'system', lang($L['style_record_buy'], array($r['title'], $month)));
				$fd = 'credit';
			}
			$styletime = $DT_TIME + 86400*30*$month;
			$c = $db->get_one("SELECT skin FROM {$DT_PRE}company WHERE userid=$_userid");
			$c['skin'] or $c['skin'] = 'default';
			$o = $db->get_one("SELECT itemid FROM {$DT_PRE}style WHERE skin='$c[skin]'");
			if($o) $db->query("UPDATE {$DT_PRE}style SET hits=hits-1 WHERE itemid=$o[itemid] AND hits>1");			
			$db->query("UPDATE {$DT_PRE}style SET hits=hits+1,`$fd`=`$fd`+$amount WHERE itemid=$itemid");
			$db->query("UPDATE {$DT_PRE}company SET template='$r[template]',skin='$r[skin]',styletime=$styletime WHERE userid=$_userid");
			dmsg($L['style_msg_buy_success'], '?action=index');
		} else {
			$r['thumb'] = is_file(DT_ROOT.'/'.$MODULE[4]['moduledir'].'/skin/'.$r['skin'].'/thumb.gif') ? $MODULE[4]['linkurl'].'skin/'.$r['skin'].'/thumb.gif' : $MODULE[4]['linkurl'].'image/nothumb.gif';
			extract($r);
			$head_title = $L['style_title_buy'];
		}
	} else {
		if($r['fee']) dheader('?action=buy&itemid='.$itemid);
		$c = $db->get_one("SELECT skin FROM {$DT_PRE}company WHERE userid=$_userid");
		$c['skin'] or $c['skin'] = 'default';
		$o = $db->get_one("SELECT itemid FROM {$DT_PRE}style WHERE skin='$c[skin]'");
		if($o) $db->query("UPDATE {$DT_PRE}style SET hits=hits-1 WHERE itemid=$o[itemid] AND hits>1");
		$db->query("UPDATE {$DT_PRE}style SET hits=hits+1 WHERE itemid=$itemid");
		$db->query("UPDATE {$DT_PRE}company SET template='$r[template]',skin='$r[skin]',styletime=0 WHERE userid=$_userid");
		dmsg($L['style_msg_use_success'], $forward);
	}
} else {
	if($action == 'view') {
		$c = $db->get_one("SELECT skin FROM {$DT_PRE}company WHERE userid=$_userid");
		$c['skin'] or $c['skin'] = 'default';
		$c['thumb'] = is_file(DT_ROOT.'/'.$MODULE[4]['moduledir'].'/skin/'.$c['skin'].'/thumb.gif') ? $MODULE[4]['linkurl'].'skin/'.$c['skin'].'/thumb.gif' : $MODULE[4]['linkurl'].'image/nothumb.gif';
	} else {
		$TYPE = get_type('style', 1);
		$pagesize = 12;
		$offset = ($page-1)*$pagesize;

		$sfields = $L['style_sfields'];
		$dfields = array('title', 'title', 'author');
		$sorder  = $L['style_sorder'];
		$dorder  = array('listorder desc,addtime desc', 'addtime DESC', 'addtime ASC', 'hits DESC', 'hits ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		$all = isset($all) ? intval($all) : 0;
		$typeid = isset($typeid) ? intval($typeid) : 0;
		isset($currency) or $currency = '';
		$minfee = isset($minfee) ? dround($minfee) : '';
		$maxfee = isset($maxfee) ? dround($maxfee) : '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);
		$type_select = type_select($TYPE, 1, 'typeid', $L['choose_type'], $typeid);
		$condition = "1";
		if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
		if(!$all) $condition .= " AND groupid LIKE '%,$_groupid,%'";
		if($typeid) $condition .= " AND typeid=$typeid";
		if($currency) $condition .= $currency == 'free' ? " AND fee=0" : " AND currency='$currency'";
		if($minfee) $condition .= " AND fee>=$minfee";
		if($maxfee) $condition .= " AND fee<=$maxfee";
		$c = $db->get_one("SELECT skin,linkurl,domain,styletime FROM {$DT_PRE}company WHERE userid=$_userid");
		if(!$c['skin']) {
			if($MG['styleid']) {
				$o = $db->get_one("SELECT skin FROM {$DT_PRE}style WHERE itemid='$MG[styleid]'");
				if($o) $c['skin'] = $o['skin'];
			}
		}
		$c['skin'] or $c['skin'] = 'default';
		$havedays = $c['styletime'] ? ceil(($c['styletime']-$DT_TIME)/86400) : 0;
		$lists = $do->get_list($condition, $dorder[$order]);
		$head_title = $L['style_title'];
	}
}
include template('style', $module);
?>