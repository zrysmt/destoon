<?php
define('DT_REWRITE', true);
$_COOKIE = array();
require '../common.inc.php';
require DT_ROOT.'/include/module.func.php';
$url = isset($url) ? fix_link($url) : DT_PATH;
if(isset($username)) {
	if(check_name($username)) {
		$r = $db->get_one("SELECT linkurl FROM {$DT_PRE}company WHERE username='$username'");
		$url = $r ? $r['linkurl'] : userurl($username);
	}
} else if(isset($aid)) {
	$aid = intval($aid);
	if($aid) {
		$r = $db->get_one("SELECT url,key_moduleid,key_id,typeid FROM {$DT_PRE}ad WHERE aid=$aid AND fromtime<$DT_TIME AND totime>$DT_TIME");
		if($r) {
			$url = ($r['key_moduleid'] && $r['typeid'] > 5) ? 'redirect.php?mid='.$r['key_moduleid'].'&itemid='.$r['key_id'] : $r['url'];
			$db->query("UPDATE {$DT_PRE}ad SET hits=hits+1 WHERE aid=$aid");
		}
	}
} else if($mid) {
	if(isset($MODULE[$mid]) && $itemid && $mid > 3) {
		$condition = $mid == 4 ? "userid=$itemid" : "itemid=$itemid";
		$r = $db->get_one("SELECT * FROM ".get_table($mid)." WHERE $condition");
		if($r) {
			$url = strpos($r['linkurl'], '://') === false ? $MODULE[$mid]['linkurl'].$r['linkurl'] : $r['linkurl'];
			if($sum) {
				$MOD = cache_read('module-'.$mid.'.php');
				$item = $r;
				extract($item);
				$fee = get_fee($item['fee'], $MOD['fee_view']);
				$currency = $MOD['fee_currency'];
				$unit = $currency == 'money' ? $DT['money_unit'] : $DT['credit_unit'];
				$name = $currency == 'money' ? $DT['money_name'] : $DT['credit_name'];
				$linkurl = $url;
				$fee_back = $currency == 'money' ? dround($fee*intval($MOD['fee_back'])/100) : ceil($fee*intval($MOD['fee_back'])/100);
				$url = $MODULE[2]['linkurl'].'pay.php?mid='.$mid.'&itemid='.$itemid.'&username='.$username.'&fee_back='.$fee_back.'&fee='.$fee.'&currency='.$currency.'&sign='.crypt_sign($_username.$mid.$itemid.$username.$fee.$fee_back.$currency.$linkurl.$title).'&title='.rawurlencode($title).'&forward='.urlencode($linkurl);
			}
		}
	}
	if($mid == -9 && $itemid) $url = $MODULE[9]['linkurl'].rewrite('resume.php?itemid='.$itemid);
} else {
	check_referer() or $url = DT_PATH;
}
dheader($url);
?>