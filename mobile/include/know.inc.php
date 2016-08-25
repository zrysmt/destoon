<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2016 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
if($itemid) {
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	($item && $item['status'] > 2) or mobile_msg($L['msg_not_exist']);
	extract($item);	
	$could_answer = check_group($_groupid, $MOD['group_answer']);
	if($item['process'] != 1 || ($_username && $_username == $item['username'])) $could_answer = false;
	if($could_answer) {
		if($_username) {
			$r = $db->get_one("SELECT itemid FROM {$table_answer} WHERE username='$_username' AND qid=$itemid");
		} else {
			$r = $db->get_one("SELECT itemid FROM {$table_answer} WHERE ip='$DT_IP' AND qid=$itemid AND addtime>$DT_TIME-86400");
		}
		if($r) $could_answer = false;
	}
	$CAT = get_cat($catid);
	if(!check_group($_groupid, $MOD['group_show']) || !check_group($_groupid, $CAT['group_show'])) mobile_msg($L['msg_no_right']);
	$description = '';
	$user_status = 3;
	$fee = get_fee($item['fee'], $MOD['fee_view']);
	include 'content.inc.php';
	if($page == 1) {
		$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
		$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
		$content = video5($t['content']);
		if($share_icon) $share_icon = share_icon($thumb, $content);
		$best = $aid ? $db->get_one("SELECT * FROM {$DT_PRE}know_answer WHERE itemid=$aid") : array();
	}
	$editdate = timetodate($addtime, 5);
	$update = '';
	$answers = array();
	if($MOD['answer_pagesize']) {
		$pagesize = $MOD['answer_pagesize'];
		$offset = ($page-1)*$pagesize;
	}
	if($page == 1) {
		$items = $db->count($table.'_answer', "qid=$itemid AND status=3");
		if($items != $answer) $update .= ",answer='$items'";
	} else {
		$items = $answer;
	}
	if($items > 0) {
		$floor = $page == 1 ? 0 : ($page-1)*$pagesize;
		$pages = mobile_pages($items, $page, $pagesize);
		$result = $db->query("SELECT * FROM {$table}_answer WHERE qid=$itemid AND status=3 ORDER BY itemid DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			if($r['itemid'] == $aid) continue;
			$r['floor'] = ++$floor;
			$answers[] = $r;
		}
	}
	include DT_ROOT.'/include/update.inc.php';
	$seo_file = 'show';
	$head_title = $title.$DT['seo_delimiter'].$MOD['name'].$DT['seo_delimiter'].$head_title;
	$head_name = $CAT['catname'];
	$back_link = 'javascript:Dback(\''.mobileurl($moduleid, $catid).'\', \''.$DT_REF.'\', \'share|comment|know\');';
	$foot = '';
} else {
	$typeid = isset($typeid) ? intval($typeid) : 0;
	in_array($typeid, array(0, 1, 2)) or $typeid = 0;
	if($kw) {
		check_group($_groupid, $MOD['group_search']) or mobile_msg($L['msg_no_search']);
	} else if($catid) {
		$CAT or mobile_msg($L['msg_not_cate']);
		if(!check_group($_groupid, $MOD['group_list']) || !check_group($_groupid, $CAT['group_list'])) {
			mobile_msg($L['msg_no_right']);
		}
	} else {
		check_group($_groupid, $MOD['group_index']) or mobile_msg($L['msg_no_right']);
	}
	$head_title = $MOD['name'].$DT['seo_delimiter'].$head_title;
	if($kw) $head_title = $kw.$DT['seo_delimiter'].$head_title;
	$condition = "status=3";
	if($keyword) $condition .= " AND keyword LIKE '%$keyword%'";
	if($catid) $condition .= $CAT ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
	if($areaid) $condition .= $ARE['child'] ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
	if($typeid == 1) $condition .= " AND process=1";
	if($typeid == 2) $condition .= " AND process=3";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition", 'CACHE');
	$items = $r['num'];
	$pages = mobile_pages($items, $page, $pagesize);
	$lists = array();
	if($items) {
		$order = $MOD['order'];
		$time = strpos($MOD['order'], 'add') !== false ? 'addtime' : 'edittime';
		$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			if($kw) $r['title'] = str_replace($kw, '<b class="f_red">'.$kw.'</b>', $r['title']);
			$r['linkurl'] = mobileurl($moduleid, 0, $r['itemid']);
			$r['date'] = timetodate($r[$time], 'Y/m/d H:i');
			$lists[] = $r;
		}
		$db->free_result($result);
	}
	$back_link = mobileurl($moduleid);
	if($kw) {
		$seo_file = 'search';
		$head_name = $MOD['name'].$L['search'];
	} else if($catid) {
		$seo_file = 'list';
		$head_title = $CAT['catname'].$DT['seo_delimiter'].$head_title;
		$head_name = $CAT['catname'];
		if($CAT['parentid']) $back_link = mobileurl($moduleid, $CAT['parentid']);
	} else {
		$seo_file = 'index';
		$head_name = $MOD['name'];
	}
}
include DT_ROOT.'/include/seo.inc.php';
include template($module, 'mobile');
?>