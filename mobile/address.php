<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
$moduleid = 2;
require 'common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$_userid or dheader('login.php?forward='.urlencode('address.php?action='.$action));
require DT_ROOT.'/include/post.func.php';
require MD_ROOT.'/address.class.php';
$do = new address();
$head_name = $L['address_title'];
$head_title = $head_name.$DT['seo_delimiter'].$head_title;
switch($action) {
	case 'add':
		if(isset($_POST['ok'])) {
			if($MG['address_limit']) {
				$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}address WHERE username='$_username'");
				if($r['num'] >= $MG['address_limit']) exit('max');
			}
			foreach($post as $k=>$v) {
				$post[$k] = convert(input_trim($v), 'UTF-8', DT_CHARSET);
			}
			if($do->pass($post)) {
				$post['username'] = $_username;
				$do->add($post);
				exit('ok');
			} else {
				exit($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				$$v = '';
			}
			$back_link = '?page='.$page;
			$head_name = $L['address_add'];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
		}
	break;
	case 'edit':
		$itemid or dheader('?reload='.$DT_TIME);
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['username'] != $_username) mobile_msg($L['msg_no_right']);
		if(isset($_POST['ok'])) {
			foreach($post as $k=>$v) {
				$post[$k] = convert(input_trim($v), 'UTF-8', DT_CHARSET);
			}
			if($do->pass($post)) {
				$post['username'] = $_username;
				$do->edit($post);
				exit('ok');
			} else {
				exit($do->errmsg);
			}
		} else {
			extract($r);
			$back_link = '?page='.$page;
			$head_name = $L['address_edit'];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
		}
	break;
	case 'delete':
		$itemid or exit('ko');
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['username'] != $_username) exit('ko');
		$do->delete($itemid);
		exit('ok');
	break;
	case 'choose':
		$itemid or exit('ko');
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['username'] != $_username) exit('ko');
		set_cookie('addr_id', $itemid, $DT_TIME + 86400*30);
		exit('ok');
	break;
	default:
		$auth = isset($auth) ? decrypt($auth, DT_KEY.'ADDR') : '';
		if($auth) {
			$back_link = $auth;
			set_cookie('addr_back', $back_link);
		} else {
			$back_link = get_cookie('addr_back');
		}
		$back_link or mobile_msg($L['address_expire'], 'index.php?reload='.$DT_TIME);
		$cancel_url = 'channel.php';
		if(strpos($back_link, 'cart.php') !== false || strpos($back_link, 'mall') !== false) {
			$cancel_url = mobileurl(16);
		} else if(strpos($back_link, 'group') !== false) {
			$cancel_url = mobileurl(17);
		} else if(strpos($back_link, 'moduleid=5') !== false) {
			$cancel_url = mobileurl(5);
		}
		$condition = "username='$_username'";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}address WHERE $condition");
		$items = $r['num'];
		$pages = mobile_pages($items, $page, $pagesize);
		$lists = array();
		if($items) {
			$result = $db->query("SELECT * FROM {$DT_PRE}address WHERE $condition ORDER BY listorder ASC,itemid ASC LIMIT $offset,$pagesize");
			while($r = $db->fetch_array($result)) {
				$r['adddate'] = timetodate($r['addtime'], 'Y/m/d H:i');
				if($r['areaid']) $r['address'] = area_pos($r['areaid'], '').$r['address'];
				$lists[] = $r;
			}
		}
	break;
}
$foot = '';
include template('address', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>