<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
$moduleid = 3;
require 'common.inc.php';
isset($MODULE[$mid]) or dheader('index.php');
$itemid or dheader(mobileurl($mid));
if(in_array($itemid, cache_read('bancomment-'.$mid.'.php'))) mobile_msg($L['comment_close'], mobileurl($mid, 0, $itemid));
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/include/module.func.php';
$head_name = $L['comment_title'];
$head_title = $head_name.$DT['seo_delimiter'].$head_title;

$need_captcha = $MOD['comment_captcha_add'] == 2 ? $MG['captcha'] : $MOD['comment_captcha_add'];
if($MOD['comment_pagesize']) {
	$pagesize = $MOD['comment_pagesize'];
	$offset = ($page-1)*$pagesize;
}
if($mid == 4) {
	$item = $db->get_one("SELECT company,linkurl,username,groupid,thumb FROM ".get_table($mid)." WHERE userid=$itemid");
	$item or exit;
	$item['groupid'] > 4 or mobile_msg($L['msg_not_user']);
	$item['title'] = $item['company'];
	$linkurl = $item['linkurl'];
} else {
	$item = $db->get_one("SELECT title,linkurl,username,status,thumb FROM ".get_table($mid)." WHERE itemid=$itemid");
	$item or exit;
	$item['status'] > 2 or mobile_msg($L['msg_not_exist']);
	$linkurl = $MODULE[$mid]['linkurl'].$item['linkurl'];
}
$title = $item['title'];
$thumb = $item['thumb'];

switch($action) {
	case 'user':
		(isset($username) && check_name($username)) or $username = '';
		$username or mobile_msg($L['msg_not_user']);
		$_userid or dheader('login.php?forward='.urlencode('comment.php?action='.$action.'&username='.$username.'&mid='.$mid.'&itemid='.$itemid));
		$user = userinfo($username);
		$user or mobile_msg($L['msg_not_user']);
		$condition = "status=3 AND username='$username' AND hidden=0";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}comment WHERE $condition", 'CACHE');
		$items = $r['num'];
		$pages = mobile_pages($items, $page, $pagesize);
		$lists = array();
		if($items) {
			$result = $db->query("SELECT * FROM {$DT_PRE}comment WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
			while($r = $db->fetch_array($result)) {
				$lists[] = $r;
			}
			$db->free_result($result);
		}
		$head_name = $L['comment_user'];
		$head_title = $head_name.$DT['seo_delimiter'].$head_title;
		$foot = '';
		include template('comment_user', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
	case 'count':
		if($EXT['comment_api'] == 'changyan') {
			$comments = $dc->get('comments-'.$mid.'-'.$itemid);
			if(strlen($comments) > 0) {
				echo $comments;
				exit;
			}
			$rec = dcurl('http://changyan.sohu.com/api/2/topic/load?client_id='.$EXT['comment_api_id'].'&topic_source_id='.$mid.'-'.$itemid.'&topic_url='.urlencode($linkurl));
			if(strpos($rec, 'cmt_sum') !== false) {
				$arr = json_decode($rec, true);
				$comments = intval($arr['cmt_sum']);
				$dc->set('comments-'.$mid.'-'.$itemid, $comments, $CFG['db_expires'] ? $CFG['db_expires'] : 1800);
				echo $comments;
				exit;
			} else {
				$dc->set('comments-'.$mid.'-'.$itemid, 0, $CFG['db_expires'] ? $CFG['db_expires'] : 1800);
				exit('0');
			}
		} else if($EXT['comment_api'] == 'duoshuo') {
			$comments = $dc->get('comments-'.$mid.'-'.$itemid);
			if(strlen($comments) > 0) {
				echo $comments;
				exit;
			}
			$rec = dcurl('http://api.duoshuo.com/threads/counts.json?short_name='.$EXT['comment_api_id'].'&threads='.$mid.'-'.$itemid);
			if(strpos($rec, 'comments') !== false) {
				$arr = json_decode($rec, true);
				$comments = intval($arr['response'][$mid.'-'.$itemid]['comments']);
				$dc->set('comments-'.$mid.'-'.$itemid, $comments, $CFG['db_expires'] ? $CFG['db_expires'] : 1800);
				echo $comments;
				exit;
			} else {
				$dc->set('comments-'.$mid.'-'.$itemid, 0, $CFG['db_expires'] ? $CFG['db_expires'] : 1800);
				exit('0');
			}
		} else {
			$condition = "item_mid=$mid AND item_id=$itemid AND status=3";
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}comment WHERE {$condition}", 'CACHE');
			echo $r['num'];
			exit;
		}
	break;
	case 'post':
		$username = $item['username'];
		if($username && $username == $_username) exit('self');
		if(check_group($_groupid, $MOD['comment_group'])) {
			//
		} else {
			if($_userid) {
				exit('permission');
			} else {				
				exit('login');
			}
		}
		$captcha = isset($captcha) ? convert(input_trim($captcha), 'UTF-8', DT_CHARSET) : '';
		$msg = captcha($captcha, $need_captcha, true);
		if($msg) exit('captcha');
		$sql = $_userid ? "username='$_username'" : "ip='$DT_IP'";
		if($MOD['comment_limit']) {
			$today = $today_endtime - 86400;
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}comment WHERE $sql AND addtime>$today");
			$r['num'] < $MOD['comment_limit'] or exit('max');
		}
		if($MOD['comment_time']) {
			$r = $db->get_one("SELECT addtime FROM {$DT_PRE}comment WHERE $sql ORDER BY addtime DESC");
			if($r && $DT_TIME - $r['addtime'] < $MOD['comment_time']) exit('fast');
		}
		$content = isset($content) ? convert(input_trim(nl2br($content)), 'UTF-8', DT_CHARSET) : '';
		$content = dhtmlspecialchars(trim($content));
		$content = preg_replace("/&([a-z]{1,});/", '', $content);
		$len = word_count($content);
		if($len < $MOD['comment_min']) exit('ko');
		if($len > $MOD['comment_max']) exit('ko');
		$star = intval($star);
		in_array($star, array(1, 2, 3)) or $star = 3;
		$status = get_status(3, $MOD['comment_check'] == 2 ? $MG['check'] : $MOD['comment_check']);
		$hidden = isset($hidden) ? 1 : 0;
		$title = addslashes($title);
		$content = nl2br($content);
		$quotation = '';
		$qid = 0;
		$db->query("INSERT INTO {$DT_PRE}comment (item_mid,item_id,item_title,item_username,content,quotation,qid,addtime,username,passport,hidden,star,ip,status) VALUES ('$mid','$itemid','$title','$username','$content','$quotation','$qid','$DT_TIME','$_username','$_passport','$hidden','$star','$DT_IP','$status')");
		$cid = $db->insert_id();
		$r = $db->get_one("SELECT sid FROM {$DT_PRE}comment_stat WHERE moduleid=$mid AND itemid=$itemid");
		$star = 'star'.$star;
		if($r) {
			$db->query("UPDATE {$DT_PRE}comment_stat SET comment=comment+1,`{$star}`=`{$star}`+1 WHERE sid=$r[sid]");
		} else {
			$db->query("INSERT INTO {$DT_PRE}comment_stat (moduleid,itemid,{$star},comment) VALUES ('$mid','$itemid','1','1')");
		}
		if($status == 3) {
			if($_username && $MOD['credit_add_comment']) {
				credit_add($_username, $MOD['credit_add_comment']);
				credit_record($_username, $MOD['credit_add_comment'], 'system', $L['comment_record_add'], 'ID:'.$cid);
			}
			exit('ok');
		} else {
			exit('check');
		}
		exit('ko');
	break;
	default:
		if($EXT['comment_api']) {
			//
		} else {
			$lists = array();
			$condition = "item_mid=$mid AND item_id=$itemid AND status=3";
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}comment WHERE {$condition}");
			$items = $r['num'];
			$pages = mobile_pages($items, $page, $pagesize);
			if($items) {
				$result = $db->query("SELECT * FROM {$DT_PRE}comment WHERE {$condition} ORDER BY itemid ASC LIMIT $offset,$pagesize");
				$floor = $page == 1 ? 0 : ($page-1)*$pagesize;
				while($r = $db->fetch_array($result)) {
					$r['floor'] = ++$floor;
					if($r['username']) {
						$r['name'] = $r['hidden'] ? $MOD['comment_am'] : $r['passport'];
						$r['uname'] = $r['hidden'] ? '' : $r['username'];
					} else {
						$r['name'] = 'IP:'.hide_ip($r['ip']);
						$r['uname'] = '';
					}
					$lists[] = $r;
				}
			}
		}		
		$head_title = $title.$DT['seo_delimiter'].$head_title;
		$foot = '';
		include template('comment', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
}
?>