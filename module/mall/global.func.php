<?php
defined('IN_DESTOON') or exit('Access Denied');
function get_relate($M) {
	global $db, $table, $MOD;
	$lists = $tags = array();
	if($M['relate_id'] && $M['relate_name']) {
		$ids = $M['relate_id'];
		$result = $db->query("SELECT itemid,title,linkurl,thumb,username,status,relate_id,relate_name,relate_title FROM {$table} WHERE itemid IN ($ids)");
		while($r = $db->fetch_array($result)) {
			if($r['username'] != $M['username']) continue;
			if($r['relate_id'] != $M['relate_id']) continue;
			if($r['relate_name'] != $M['relate_name']) continue;
			if($r['status'] != 3) continue;
			if(!$r['relate_title']) $r['relate_title'] = $r['title'];
			$r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
			$tags[$r['itemid']] = $r;
		}
		foreach(explode(',', $ids) as $v) {
			if(isset($tags[$v])) $lists[] = $tags[$v];
		}
		return count($lists) > 1 ? $lists : array();
	}
}

function get_nv($n, $v) {
	$p = array();
	if($n && $v) $p = explode('|', $v);
	return count($p) > 1 ? $p : array();
}

function get_price($amount, $price, $step) {
	if($step) {
		$s = unserialize($step);
		if($s['a3'] && $amount > $s['a3']) return $s['p3'];
		if($s['a2'] && $amount > $s['a2']) return $s['p2'];
	}
	return $price;
}
?>