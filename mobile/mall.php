<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
$moduleid = 16;
require 'common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
switch($action) {
	case 'comment':
		$itemid or exit;
		$STARS = $L['star_type'];
		$star = isset($star) ? intval($star) : 0;
		in_array($star, array(0, 1, 2, 3)) or $star = 0;
		$lists = array();
		$pages = '';
		/*
		$pagesize = 2;
		$offset = ($page-1)*$pagesize;
		*/
		$condition = $star ? "mallid=$itemid AND seller_star=$star" : "mallid=$itemid AND seller_star>0";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}mall_comment WHERE $condition");
		$items = $r['num'];
		$pages = mobile_pages($items, $page, $pagesize, 'javascript:load_comment(\'{destoon_page}&star='.$star.'\');');
		$result = $db->query("SELECT * FROM {$DT_PRE}mall_comment WHERE $condition ORDER BY seller_ctime DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['seller_ctime'], 6);
			$r['replytime'] = $r['buyer_ctime'] ? timetodate($r['buyer_ctime'], 6) : '';
			$lists[] = $r;
		}
		$stat = $r = $db->get_one("SELECT * FROM {$DT_PRE}mall_stat WHERE mallid=$itemid");
		if($stat && $stat['scomment']) {
			$stat['pc1'] = dround($stat['s1']*100/$stat['scomment'], 2, true).'%';
			$stat['pc2'] = dround($stat['s2']*100/$stat['scomment'], 2, true).'%';
			$stat['pc3'] = dround($stat['s3']*100/$stat['scomment'], 2, true).'%';
		} else {
			$stat['s1'] = $stat['s2'] = $stat['s3'] = 0;
			$stat['pc1'] = $stat['pc2'] = $stat['pc3'] = '0%';
		}
		include template('mall_comment', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
	case 'order':
		$itemid or exit;
		$lists = array();
		$pages = '';
		/*
		$pagesize = 2;
		$offset = ($page-1)*$pagesize;
		*/
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}mall_order WHERE mallid=$itemid AND status=4");
		$items = $r['num'];
		$pages = mobile_pages($items, $page, $pagesize, 'javascript:load_order({destoon_page});');
		$result = $db->query("SELECT * FROM {$DT_PRE}mall_order WHERE mallid=$itemid AND status=4 ORDER BY itemid DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			$r['updatetime'] = timetodate($r['updatetime'], 6);
			$lists[] = $r;
		}
		include template('mall_order', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
	default:
		dheader(mobileurl($moduleid));
	break;
}
?>