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
	$CAT = get_cat($catid);
	if(!check_group($_groupid, $MOD['group_show']) || !check_group($_groupid, $CAT['group_show'])) mobile_msg($L['msg_no_right']);
	$GRP = $db->get_one("SELECT * FROM {$table}_group WHERE itemid=$gid");
	$GRP['managers'] = $GRP['manager'] ? explode('|', $GRP['manager']) : array();
	$description = '';
	$user_status = 3;
	$fee = get_fee($item['fee'], $MOD['fee_view']);
	require 'include/'.($action == 'pay' ? 'pay' : 'content').'.inc.php';
	if($page == 1) {
		$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
		$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
		$content = video5($t['content']);
		if($share_icon) $share_icon = share_icon($thumb, $content);
		if($user_status == 2) $description = get_description($content, $MOD['pre_view']);
	}
	$pages = '';
	$editdate = timetodate($addtime, 5);
	$update = '';
	$F = explode('|', $MOD['floor']);
	$replys = array();
	if($MOD['reply_pagesize']) {
		$pagesize = $MOD['reply_pagesize'];
		$offset = ($page-1)*$pagesize;
	}
	if($page == 1) {
		$items = $db->count($table.'_reply', "tid=$itemid AND status=3");
		if($items != $reply) $update .= ",reply='$items'";
	} else {
		$items = $reply;
	}
	if($items > 0) {
		$floor = $page == 1 ? 0 : ($page-1)*$pagesize;
		$pages = mobile_pages($items, $page, $pagesize);
		$result = $db->query("SELECT * FROM {$table}_reply WHERE tid=$itemid AND status=3 ORDER BY itemid ASC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			$r['fname'] = isset($F[$floor]) ? $F[$floor] : '';
			$r['floor'] = ++$floor;
			if($r['fid'] != $r['floor']) $db->query("UPDATE {$table}_reply SET fid='$r[floor]' WHERE itemid='$r[itemid]'");
			$replys[] = $r;
		}
	}
	include DT_ROOT.'/include/update.inc.php';
	$seo_file = 'show';
	$head_title = $title.$DT['seo_delimiter'].$MOD['name'].$DT['seo_delimiter'].$head_title;
	$head_name = $GRP['title'].$MOD['seo_name'];
	$back_link = 'javascript:Dback(\''.mobileurl($moduleid, $gid).'\', \''.$DT_REF.'\', \'share|comment|club\');';
	$foot = '';
} else {
	$gid = 0;
	$GRP = array();
	if($catid) {
		if($CAT) {
			//
		} else {
			$GRP = $db->get_one("SELECT * FROM {$table}_group WHERE itemid=$catid");
			if($GRP && $GRP['status'] == 3) {
				$gid = $GRP['itemid'];
			} else {
				$catid = 0;
				$CAT = array();
			}
		}
	}
	if($kw) {
		check_group($_groupid, $MOD['group_search']) or mobile_msg($L['msg_no_search']);
	} else if($catid) {
		/*
		$CAT or mobile_msg($L['msg_not_cate']);
		if(!check_group($_groupid, $MOD['group_list']) || !check_group($_groupid, $CAT['group_list'])) {
			mobile_msg($L['msg_no_right']);
		}
		*/
	} else {
		check_group($_groupid, $MOD['group_index']) or mobile_msg($L['msg_no_right']);
	}
	if($gid) {
		$typeid = isset($typeid) ? intval($typeid) : 0;
		in_array($typeid, array(0, 1, 2)) or $typeid = 0;
		$condition = "status=3 AND gid=$gid";
		if($typeid == 2) $condition .= " AND level>0";
		if($keyword) $condition .= " AND keyword LIKE '%$keyword%'";
		#if($catid) $condition .= $CAT ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
		#if($areaid) $condition .= $ARE['child'] ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition", 'CACHE');
		$items = $r['num'];
		$pages = mobile_pages($items, $page, $pagesize);
		$lists = array();
		$time = strpos($MOD['order'], 'add') !== false ? 'addtime' : 'edittime';
		if($page == 1 && !$keyword && $typeid == 0) {
			$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE status=3 AND ontop=2 ORDER BY addtime DESC LIMIT ".$MOD['maxontop'], 'CACHE');
			while($r = $db->fetch_array($result)) {
				$r['linkurl'] = mobileurl($moduleid, 0, $r['itemid']);
				$r['date'] = timetodate($r[$time], 'Y/m/d H:i');
				$lists[] = $r;
			}
			$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE status=3 AND gid=$gid AND ontop=1 ORDER BY addtime DESC LIMIT ".$MOD['maxontop'], 'CACHE');
			while($r = $db->fetch_array($result)) {
				$r['linkurl'] = mobileurl($moduleid, 0, $r['itemid']);
				$r['date'] = timetodate($r[$time], 'Y/m/d H:i');
				$lists[] = $r;
			}
		}
		if($items) {
			//$order = $MOD['order'];
			$order = $typeid ? 'addtime DESC' : 'replytime DESC';
			$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
			while($r = $db->fetch_array($result)) {
				if($r['ontop']) continue;
				if($kw) $r['title'] = str_replace($kw, '<b class="f_red">'.$kw.'</b>', $r['title']);
				$r['linkurl'] = mobileurl($moduleid, 0, $r['itemid']);
				$r['date'] = timetodate($r[$time], 'Y/m/d H:i');
				$lists[] = $r;
			}
			$db->free_result($result);
		}
		$head_title = $GRP['title'].$MOD['seo_name'].$DT['seo_delimiter'].$MOD['name'].$DT['seo_delimiter'].$head_title;
		$back_link = mobileurl($moduleid);
		if($kw) {
			$back_link = mobileurl($moduleid, $catid);
			$head_name = $L['club_post'];
			$head_title = $kw.$DT['seo_delimiter'].$head_title;
		} else {
			$head_name = $GRP['title'].$MOD['seo_name'];
		}
		$foot = '';
	} else {
		$condition = "status=3";
		if($keyword) $condition .= " AND title LIKE '%$keyword%'";
		if($catid) $condition .= $CAT ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
		if($areaid) $condition .= $ARE['child'] ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_group WHERE $condition", 'CACHE');
		$items = $r['num'];
		$pages = mobile_pages($items, $page, $pagesize);
		$lists = array();
		if($items) {
			$order = $MOD['order'];
			$time = strpos($MOD['order'], 'add') !== false ? 'addtime' : 'edittime';
			$result = $db->query("SELECT * FROM {$table}_group WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
			while($r = $db->fetch_array($result)) {
				if($kw) $r['title'] = str_replace($kw, '<b class="f_red">'.$kw.'</b>', $r['title']);
				$lists[] = $r;
			}
			$db->free_result($result);
		}
		$head_title = $MOD['name'].$DT['seo_delimiter'].$head_title;
		$back_link = mobileurl($moduleid);
		if($kw) {
			$seo_file = 'search';
			$head_name = $MOD['name'].$L['search'];
			$head_title = $kw.$DT['seo_delimiter'].$head_title;
		} else if($catid) {
			$seo_file = 'list';
			$head_name = $CAT['catname'];
			$head_title = $CAT['catname'].$DT['seo_delimiter'].$head_title;
			if($CAT['parentid']) $back_link = mobileurl($moduleid, $CAT['parentid']);
		} else {
			$seo_file = 'index';
			$head_name = $MOD['name'];
		}
	}
}
include DT_ROOT.'/include/seo.inc.php';
include template($module, 'mobile');
?>