<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2016 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$userid = isset($userid) ? intval($userid) : 0;
$username = isset($username) ? trim($username) : '';
check_name($username) or $username = '';
if($userid || $username) {
	if($userid) $username = get_user($userid, 'userid', 'username');
	$COM = userinfo($username);
	if(!$COM || ($COM['groupid'] < 5 && $COM['groupid'] > 1)) {
		userclean($username);
		mobile_msg($L['msg_not_corp']);
	}
	if(!$COM['edittime'] && !$MOD['openall']) mobile_msg($L['com_opening']);
	$COM['year'] = vip_year($COM['fromtime']);
	$COMGROUP = cache_read('group-'.$COM['groupid'].'.php');
	if(!isset($COMGROUP['homepage']) || !$COMGROUP['homepage']) mobile_msg($L['com_no_home']);
	require_once DT_ROOT.'/module/member/global.func.php';
	$userid = $COM['userid'];
	$company = $COM['company'];
	$HURL = 'index.php?moduleid=4&username='.$username;
	include load('homepage.lang');
	if($COMGROUP['menu_d']) {
		$_menu_show = array();
		foreach($HMENU as $k=>$v) {
			$_menu_show[$k] = strpos(','.$COMGROUP['menu_d'].',', ','.$k.',') !== false ? 1 : 0;
		}
		$_menu_show = implode(',', $_menu_show);
	} else {
		$_menu_show = '1,1,1,1,1,1,1,1,0,0,0,0,0,0';
	}
	$_menu_order = '0,10,20,30,40,50,60,70,80,90,100,110,120,130';
	$_menu_num = '1,16,30,30,10,30,1,12,12,12,12,30,12,1';
	$_menu_file = implode(',' , $MFILE);
	$_menu_name = implode(',' , $HMENU);

	$HOME = get_company_setting($COM['userid'], '', 'CACHE');
	
	$menu_show = explode(',', isset($HOME['menu_show']) ? $HOME['menu_show'] : $_menu_show);
	$menu_order = explode(',', isset($HOME['menu_order']) ? $HOME['menu_order'] : $_menu_order);
	$menu_num = explode(',', isset($HOME['menu_num']) ? $HOME['menu_num'] : $_menu_num);
	$menu_file = explode(',', isset($HOME['menu_file']) ? $HOME['menu_file'] : $_menu_file);
	$menu_name = explode(',', isset($HOME['menu_name']) ? $HOME['menu_name'] : $_menu_name);
	$_HMENU = array();
	asort($menu_order);
	foreach($menu_order as $k=>$v) {
		$_HMENU[$k] = $HMENU[$k];
	}
	$HMENU = $_HMENU;

	$MENU = $_MENU = array();
	$menuid = 0;
	foreach($HMENU as $k=>$v) {
		if($menu_show[$k] && in_array($menu_file[$k], $MFILE)) {
			$MENU[$k]['name'] = $menu_name[$k];
			$MENU[$k]['file'] = $menu_file[$k];
			$_MENU[$menu_file[$k]] = $menu_name[$k];
		}
	}

	isset($_MENU['introduce']) or $_MENU['introduce'] = $L['com_introduce'];
	isset($_MENU['news']) or $_MENU['news'] = $L['com_news'];
	isset($_MENU['credit']) or $_MENU['credit'] = $L['com_credit'];
	isset($_MENU['contact']) or $_MENU['contact'] = $L['com_contact'];

	$head_title = $company;
	$foot = '';
	switch($action) {
		case 'introduce':
			$table = $DT_PRE.'page';
			$table_data = $DT_PRE.'page_data';
			$head_name = $_MENU[$action];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
			if($itemid) {
				$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
				($item && $item['status'] > 2 && $item['username'] == $username) or mobile_msg($L['msg_not_exist']);
				extract($item);
				$t = $db->get_one("SELECT content FROM {$table_data} WHERE itemid=$itemid");
				$content = video5($t['content']);
				if($share_icon) $share_icon = share_icon('', $content);
				$db->query("UPDATE {$table} SET hits=hits+1 WHERE itemid=$itemid");
				$date = timetodate($addtime, 3);
				$back_link = $HURL.'&action='.$action;
				$head_title = $title.$DT['seo_delimiter'].$head_title;
			} else {
				$content_table = content_table(4, $userid, is_file(DT_CACHE.'/4.part'), $DT_PRE.'company_data');
				$t = $db->get_one("SELECT content FROM {$content_table} WHERE userid=$userid");
				$content = video5($t['content']);
				if($share_icon) $share_icon = share_icon($thumb, $content);
				$video = isset($HOME['video']) ? $HOME['video'] : '';
				$thumb = $COM['thumb'];
				$lists = array();
				$result = $db->query("SELECT itemid,title,style FROM {$table} WHERE status=3 AND username='$username' ORDER BY listorder DESC,addtime DESC");
				while($r = $db->fetch_array($result)) {
					$lists[] = $r;
				}
				$back_link = $HURL;
			}
			include template('homepage-'.$action, 'mobile');
		break;
		case 'credit':
			$head_name = $_MENU[$action];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
			$back_link = $HURL;
			$typeid = isset($typeid) ? intval($typeid) : 0;
			in_array($typeid, array(0, 1, 2)) or $typeid = 0;
			$tab = isset($MODULE[16]) ? 1 : 0;
			if($typeid && $tab) {
				$table = $DT_PRE.'mall_comment';
				$comment = 1;
				$STARS = $L['star_type'];
				if($typeid == 2) {
					$condition = "buyer='$username' AND buyer_star>0";
				} else {
					$condition = "seller='$username' AND seller_star>0";
				}
				$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition", 'CACHE');
				$items = $r['num'];
				$pages = mobile_pages($items, $page, $pagesize);
				$lists = array();
				if($items) {
					$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
					while($r = $db->fetch_array($result)) {
						$lists[] = $r;
					}
				}
			}
			include template('homepage-'.$action, 'mobile');
		break;
		case 'contact':
			$could_contact = check_group($_groupid, $MOD['group_contact']);
			if($username == $_username) $could_contact = true; 
			$could_contact or mobile_msg($L['com_no_permission'].$_MENU[$action]);
			$head_name = $_MENU[$action];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
			$back_link = $HURL;
			include template('homepage-'.$action, 'mobile');
		break;
		case 'news':
			$table = $DT_PRE.'news';
			$table_data = $DT_PRE.'news_data';
			$head_name = $_MENU[$action];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
			if($itemid) {
				$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
				($item && $item['status'] > 2 && $item['username'] == $username) or mobile_msg($L['msg_not_exist']);
				extract($item);
				$t = $db->get_one("SELECT content FROM {$table_data} WHERE itemid=$itemid");
				$content = video5($t['content']);
				if($share_icon) $share_icon = share_icon('', $content);
				$db->query("UPDATE {$table} SET hits=hits+1 WHERE itemid=$itemid");
				$date = timetodate($addtime, 3);
				$back_link = $HURL.'&action='.$action;
				$head_title = $title.$DT['seo_delimiter'].$head_title;
			} else {
				$typeid = isset($typeid) ? intval($typeid) : 0;
				$condition = "username='$username' AND status=3";
				if($kw) $condition .= " AND title LIKE '%$keyword%'";		
				if($typeid) $condition .= " AND typeid='$typeid'";
				$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition", 'CACHE');
				$items = $r['num'];
				$pages = mobile_pages($items, $page, $pagesize);
				$lists = array();
				if($items) {
					$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY addtime DESC LIMIT $offset,$pagesize");
					while($r = $db->fetch_array($result)) {
						$r['date'] = timetodate($r['addtime'], $page < 4 ? 2 : 3);
						$lists[] = $r;
					}
				}
				$back_link = $HURL;
				if($typeid) $back_link .= '&action='.$action;
			}
			include template('homepage-'.$action, 'mobile');
		break;
		case 'honor':
			isset($_MENU[$action]) or dheader($HURL);
			$table = $DT_PRE.'honor';
			$head_name = $_MENU[$action];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
			if($itemid) {
				$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
				($item && $item['status'] > 2 && $item['username'] == $username) or mobile_msg($L['msg_not_exist']);
				extract($item);
				$content = video5($item['content']);
				if($share_icon) $share_icon = share_icon($thumb, $content);
				$image = str_replace('.thumb.'.file_ext($thumb), '', $thumb);
				$db->query("UPDATE {$table} SET hits=hits+1 WHERE itemid=$itemid", 'UNBUFFERED');
				$date = timetodate($addtime, 3);
				$back_link = $HURL.'&action='.$action;
				$head_title = $title.$DT['seo_delimiter'].$head_title;
			} else {
				$condition = "username='$username' AND status=3";
				$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition", 'CACHE');
				$items = $r['num'];
				$pages = mobile_pages($items, $page, $pagesize);
				$lists = array();
				if($items) {
					$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY addtime DESC LIMIT $offset,$pagesize");
					while($r = $db->fetch_array($result)) {
						$lists[] = $r;
					}
				}
				$back_link = $HURL;
			}
			include template('homepage-'.$action, 'mobile');
		break;
		case 'link':
			isset($_MENU[$action]) or dheader($HURL);
			$table = $DT_PRE.'link';
			$head_name = $_MENU[$action];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
			$condition = "username='$username' AND status=3";
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition", 'CACHE');
			$pages = mobile_pages($r['num'], $page, $pagesize);
			$lists = array();
			$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY listorder DESC,addtime DESC LIMIT $offset,$pagesize");
			while($r = $db->fetch_array($result)) {
				$lists[] = $r;
			}
			$back_link = $HURL;
			include template('homepage-'.$action, 'mobile');
		break;
		case 'type':
			isset($item) or $item = '';
			if($item == 'sell') {
				$_TYPE = get_type('product-'.$userid);
				$head_name = $L['com_type_sell'];
			} else if($item == 'mall') {
				$_TYPE = get_type('mall-'.$userid);
				$head_name = $L['com_type_mall'];
			} else if($item == 'news') {
				$_TYPE = get_type('news-'.$userid);
				$head_name = $L['com_type_news'];
			} else {
				dheader($HURL);
			}
			$_TP = $_TYPE ? sort_type($_TYPE) : array();
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;		
			$back_link = $HURL.'&action='.$item;
			include template('homepage-'.$action, 'mobile');
		break;
		case 'mall':
			$moduleid = 16;
		break;
		case 'sell':
			$moduleid = 5;
		break;
		case 'buy':
			isset($_MENU[$action]) or dheader($HURL);
			$could_buy = check_group($_groupid, $MOD['group_buy']);
			if($username == $_username) $could_buy = true;
			$could_buy or mobile_msg($L['com_no_permission'].$_MENU[$action]);
			$moduleid = 6;
		break;
		case 'job':
			$moduleid = 9;
		break;
		case 'photo':
			$moduleid = 12;
		break;
		case 'info':
			$moduleid = 22;
		break;
		case 'brand':
			$moduleid = 13;
		break;
		case 'video':
			$moduleid = 14;
		break;
		default:
			$background = (isset($HOME['background']) && $HOME['background']) ? $HOME['background'] : '';
			$logo = (isset($HOME['logo']) && $HOME['logo']) ? $HOME['logo'] : ($COM['thumb'] ? $COM['thumb'] : 'static/img/home-logo.png');
			$M = array();
			foreach($MENU as $v) {
				if(in_array($v['file'], array('introduce', 'news', 'credit', 'contact'))) continue;
				$M[] = $v;
			}
			include template('homepage', 'mobile');
		break;
	}
	if(in_array($action, array('mall', 'sell', 'buy', 'job', 'photo', 'info', 'brand', 'video'))) {
		isset($_MENU[$action]) or dheader($HURL);
		$table = get_table($moduleid);
		$head_name = $_MENU[$action];
		$head_title = $head_name.$DT['seo_delimiter'].$head_title;
		$back_link = $HURL;
		$condition = "username='$username' AND status=3";
		if(in_array($action, array('mall', 'sell'))) {
			$typeid = isset($typeid) ? intval($typeid) : 0;
			if($typeid) {
				$condition .= " AND mycatid='$typeid'";
				$back_link .= '&action='.$action;
			}
		}
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition", 'CACHE');
		$pages = mobile_pages($r['num'], $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY edittime DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			$r['linkurl'] = mobileurl($moduleid, 0, $r['itemid']);
			$r['date'] = timetodate($r['edittime'], 5);
			$lists[] = $r;
		}
		include template('homepage-channel', 'mobile');
	}
	if(!$DT_BOT) {
		if($DT['cache_hits']) {
			 cache_hits(4, $userid);
		} else {
			$db->query("UPDATE LOW_PRIORITY {$DT_PRE}company SET hits=hits+1 WHERE userid=$userid", 'UNBUFFERED');
		}
	}
} else {
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
	$condition = "groupid>5";
	if($keyword) $condition .= " AND keyword LIKE '%$keyword%'";
	if($catid) $condition .= " AND catids LIKE '%,".$catid.",%'";
	if($areaid) $condition .= $ARE['child'] ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
	$r = $db->get_one("SELECT COUNT(userid) AS num FROM {$table} WHERE $condition", 'CACHE');
	$items = $r['num'];
	$pages = mobile_pages($items, $page, $pagesize);
	$lists = array();
	if($items) {
		$order = $MOD['order'];
		$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
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
		$head_name = $CAT['catname'];
		if($CAT['parentid']) $back_link = mobileurl($moduleid, $CAT['parentid']);
	} else {
		$seo_file = 'index';
		$head_name = $MOD['name'];
	}
	include DT_ROOT.'/include/seo.inc.php';
	include template($module, 'mobile');
}
?>