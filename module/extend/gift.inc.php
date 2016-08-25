<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MOD['gift_enable'] or dheader(DT_PATH);
require DT_ROOT.'/include/post.func.php';
$ext = 'gift';
$url = $EXT[$ext.'_url'];
$TYPE = get_type($ext, 1);
$_TP = sort_type($TYPE);
require MD_ROOT.'/'.$ext.'.class.php';
$do = new $ext();
$typeid = isset($typeid) ? intval($typeid) : 0;
$destoon_task = rand_task();
switch($action) {
	case 'my':
		login();
		$condition = "username='$_username'";
		$lists = $do->get_my_order($condition);
		$head_title = $L['gift_my_order'].$DT['seo_delimiter'].$L['gift_title'];
	break;
	case 'order':
		login();
		$itemid or dheader($url);
		$do->itemid = $itemid;
		$item = $do->get_one();
		$item or dheader($url);
		extract($item);
		$left = $amount - $orders > 0 ? $amount - $orders : 0;
		$process = $left ? get_process($fromtime, $totime) : 4;
		if($process == 1) dalert($L['gift_error_1'], $linkurl);
		if($process == 3) dalert($L['gift_error_3'], $linkurl);
		if($process == 4) dalert($L['gift_error_4'], $linkurl);
		if($_credit < $credit) dalert($L['gift_error_5'], $linkurl);
		if(!check_group($_groupid, $groupid)) dalert($L['gift_error_6'], $linkurl);
		$t = $db->get_one("SELECT * FROM {$DT_PRE}gift_order WHERE itemid=$itemid AND username='$_username'");
		if($t) dalert($L['gift_error_7'], $url.'index.php?action=my');		
		credit_add($_username, -$credit);
		credit_record($_username, -$credit, 'system', $L['gift_credit_reason'], 'ID:'.$itemid);
		$db->query("INSERT INTO {$DT_PRE}gift_order (itemid,credit,username,ip,addtime,status) VALUES ('$itemid','$credit','$_username','$DT_IP','$DT_TIME','".$L['gift_status']."')");
		$db->query("UPDATE {$DT_PRE}gift SET orders=orders+1 WHERE itemid=$itemid");
		dheader($url.'index.php?success=1&itemid='.$itemid);
	break;
	default:
		if($itemid) {
			$do->itemid = $itemid;
			$item = $do->get_one();
			$item or dheader($url);
			extract($item);
			$left = $amount - $orders > 0 ? $amount - $orders : 0;
			$process = $left ? get_process($fromtime, $totime) : 4;
			$adddate = timetodate($addtime, 3);
			$fromdate = $fromtime ? timetodate($fromtime, 3) : $L['timeless'];
			$todate = $totime ? timetodate($totime, 3) : $L['timeless'];
			$middle = str_replace('.thumb.', '.middle.', $thumb);
			$gname = '';
			if($groupid) {
				$GROUP = cache_read('group.php');
				foreach(explode(',', $groupid) as $gid) {
					if(isset($GROUP[$gid])) $gname .= $GROUP[$gid]['groupname'].' ';
				}
			}
			$db->query("UPDATE {$DT_PRE}gift SET hits=hits+1 WHERE itemid=$itemid");
			$head_title = $title.$DT['seo_delimiter'].$L['gift_title'];
		} else {
			$pagesize = 8;
			$offset = ($page-1)*$pagesize;
			$head_title = $L['gift_title'];
			if($catid) $typeid = $catid;
			$condition = "1";
			if($typeid) {
				isset($TYPE[$typeid]) or dheader($url);
				$condition .= " AND typeid IN (".type_child($typeid, $TYPE).")";
				$head_title = $TYPE[$typeid]['typename'].$DT['seo_delimiter'].$head_title;
			}
			if($keyword) $condition .= " AND title LIKE '%$keyword%'";
			if($cityid) $condition .= ($AREA[$cityid]['child']) ? " AND areaid IN (".$AREA[$cityid]['arrchildid'].")" : " AND areaid=$cityid";
			$lists = $do->get_list($condition, 'addtime DESC');
		}
	break;
}
include template($ext, $module);
?>