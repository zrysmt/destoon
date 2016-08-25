<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
$moduleid = 18;
require 'common.inc.php';
require DT_ROOT.'/module/club/common.inc.php';
$gid = isset($gid) ? intval($gid) : 0;
if($gid) {
	$GRP = get_group($gid);
	($GRP && $GRP['status'] == 3) or mobile_msg($L['my_not_group']);
	$head_title = $GRP['title'].$MOD['seo_name'].$DT['seo_delimiter'].$MOD['name'].$DT['seo_delimiter'].$head_title;
}
$head_title = $MOD['name'].$DT['seo_delimiter'].$head_title;
switch($action) {
	case 'my':
		$_userid or dheader('login.php?forward='.urlencode('club.php?action='.$action.'&gid='.$gid));
		require MD_ROOT.'/join.class.php';
		$do = new djoin($moduleid);
		$lists = $do->get_list("username='$_username' AND status=3");
		$head_name = $L['my_group_title'];
		$head_title = $head_name.$DT['seo_delimiter'].$head_title;
		$foot = '';
		include template('club_my', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
	case 'user':
		$_userid or dheader('login.php?forward='.urlencode('club.php?action='.$action.'&username='.$username));
		(isset($username) && check_name($username)) or $username = '';
		$username or mobile_msg($L['msg_not_user']);
		$user = userinfo($username);
		$user or mobile_msg($L['msg_not_user']);
		$typeid = isset($typeid) ? intval($typeid) : 0;
		in_array($typeid, array(0, 1, 2)) or $typeid = 0;
		if($typeid == 1) {
			$condition = "status=3 AND username='$username'";
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_reply WHERE $condition", 'CACHE');
			$items = $r['num'];
			$pages = mobile_pages($items, $page, $pagesize);
			$lists = array();
			if($items) {
				$result = $db->query("SELECT * FROM {$table}_reply WHERE $condition ORDER BY addtime DESC LIMIT $offset,$pagesize");
				while($r = $db->fetch_array($result)) {
					if(strpos($r['content'], '<hr class="club_break" />') !== false) $r['content'] = substr($r['content'], strpos($r['content'], '<hr class="club_break" />'));
					$r['title'] = get_intro($r['content'], 50);
					$r['date'] = timetodate($r['addtime'], 'Y/m/d H:i');
					$lists[] = $r;
				}
				$db->free_result($result);
			}
		} else {
			$condition = "status=3 AND username='$username'";
			if($typeid == 2) $condition .= " AND level>0";
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition", 'CACHE');
			$items = $r['num'];
			$pages = mobile_pages($items, $page, $pagesize);
			$lists = array();
			if($items) {
				$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE $condition ORDER BY addtime DESC LIMIT $offset,$pagesize");
				while($r = $db->fetch_array($result)) {
					$r['date'] = timetodate($r['addtime'], 'Y/m/d H:i');
					$lists[] = $r;
				}
				$db->free_result($result);
			}
		}
		$head_name = $L['user_title'];
		$head_title = $head_name.$DT['seo_delimiter'].$head_title;
		$foot = '';
		include template('club_user', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
	case 'fans_quit':
		($_userid && $gid) or exit('ko');
		$M = $db->get_one("SELECT * FROM {$table}_fans WHERE gid=$gid AND username='$_username'");
		if($M) {
			if($M['status'] == 3) {
				$itemid = $M['itemid'];
				$db->query("DELETE FROM {$table}_fans WHERE itemid=$itemid");
				exit('ok');
			}
		}
		exit('ko');
	break;
	case 'fans_join':
		($_userid && $gid) or exit('ko');
		$M = $db->get_one("SELECT * FROM {$table}_fans WHERE gid=$gid AND username='$_username'");
		if($M) {
			if($M['status'] == 3) exit('ko');
			exit('join');
		}
		if($MG['club_join_limit']) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_fans WHERE username='$_username' AND status>1");
			$limit_used = $r['num'];
			$limit_free = $MG['club_join_limit'] > $limit_used ? $MG['club_join_limit'] - $limit_used : 0;
			if($limit_used >= $MG['club_join_limit']) exit('max');
		}
		$reason = convert(input_trim($reason), 'UTF-8', DT_CHARSET);
		$reason = dhtmlspecialchars($reason);
		if(strlen($reason) > DT_CHARLEN*500) exit('ko');
		if($GRP['join_type']) {
			if(strlen($reason) < 3) exit('reason');
		}
		$status = $GRP['join_type'] ? 2 : 3;
		$db->query("INSERT INTO {$table}_fans (gid,username,passport,reason,addtime,status) VALUES ('$gid','$_username','$_passport','$reason','$DT_TIME','$status')");
		exit($status == 3 ? 'ok' : 'check');
	break;
	case 'fans':
		$_userid or dheader('login.php?forward='.urlencode('club.php?action='.$action.'&gid='.$gid));
		$gid or mobile_msg($L['my_choose_group']);
		$lists = array();
		$condition = "gid='$gid' AND status=3";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_fans WHERE $condition");
		$items = $r['num'];
		$pages = mobile_pages($items, $page, $pagesize);
		$result = $db->query("SELECT * FROM {$table}_fans WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 'Y/m/d H:i');
			$lists[] = $r;
		}
		if($items != $GRP['fans']) $db->query("UPDATE {$table}_group SET fans='$items' WHERE itemid='$gid'");
		$M = $db->get_one("SELECT * FROM {$table}_fans WHERE gid=$gid AND username='$_username'");
		$is_fans = $M ? 1 : 0;
		$head_title = $L['fans_title'].$DT['seo_delimiter'].$head_title;
		$foot = '';
		include template('club_fans', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
	case 'reply':
		$gid or dheader(mobileurl($moduleid));
		$itemid or dheader(mobileurl($moduleid, $gid));
		check_group($_groupid, $MOD['group_reply']) or mobile_msg($L['reply_no_rights']);
		if($GRP['post_type'] && !is_fans($GRP)) mobile_msg($L['msg_not_fans'], 'club.php?action=fans&gid='.$gid);
		$tid = $itemid;
		$T = $db->get_one("SELECT * FROM {$table} WHERE itemid=$tid");
		($T && $T['status'] == 3) or mobile_msg($L['my_not_post']);
		$gid == $T['gid'] or mobile_msg($L['my_not_group']);
		$sql = $_userid ? "username='$_username'" : "ip='$DT_IP'";
		$limit_used = $limit_free = $need_password = $need_captcha = $need_question = $fee_add = 0;
		$today = $today_endtime - 86400;
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_reply WHERE $sql AND status>1 AND addtime>$today");
		$limit_used = $r['num'];
		$limit_free = $MG['club_reply_limit'] > $limit_used ? $MG['club_reply_limit'] - $limit_used : 0;
		if($MG['club_reply_limit'] && $limit_used >= $MG['club_reply_limit']) mobile_msg($L['reply_too_many']);

		$need_captcha = $MOD['captcha_reply'] == 2 ? $MG['captcha'] : $MOD['captcha_reply'];
		$need_question = $MOD['question_reply'] == 2 ? $MG['question'] : $MOD['question_reply'];
		if($need_question) $need_captcha = 1;
		if(isset($_POST['ok'])) {
			$captcha = isset($captcha) ? convert(input_trim($captcha), 'UTF-8', DT_CHARSET) : '';
			$msg = captcha($captcha, $need_captcha, true);
			if($msg) exit('captcha');
			$post = array();
			$post['content'] = isset($content) ? convert(input_trim(nl2br($content)), 'UTF-8', DT_CHARSET) : '';
			require DT_ROOT.'/include/post.func.php';
			require MD_ROOT.'/reply.class.php';
			$do = new reply($moduleid);
			if($do->pass($post)) {
				$post['tid'] = $tid;
				$post['gid'] = $gid;
				$need_check =  $MOD['check_reply'] == 2 ? $MG['check'] : $MOD['check_reply'];
				$post['status'] = get_status(3, $need_check);
				$post['username'] = $_username;
				$do->add($post);
				exit($post['status'] == 3 ? 'ok' : 'check');
			}
			exit('ko');
		}
		$head_title = $L['reply_title'].$DT['seo_delimiter'].$head_title;
		$foot = '';
		include template('club_reply', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
	case 'post':
		$gid or dheader(mobileurl($moduleid));
		$MG['club_limit'] > -1 or mobile_msg($L['post_no_rights']);
		if($GRP['post_type'] && !is_fans($GRP)) mobile_msg($L['msg_not_fans'], 'club.php?action=fans&gid='.$gid);

		$sql = $_userid ? "username='$_username'" : "ip='$DT_IP'";
		$limit_used = $limit_free = $need_password = $need_captcha = $need_question = $fee_add = 0;
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $sql AND status>1");
		$limit_used = $r['num'];
		$limit_free = $MG['club_limit'] > $limit_used ? $MG['club_limit'] - $limit_used : 0;
		if($MG['club_limit'] && $limit_used >= $MG['club_limit']) mobile_msg($L['post_too_many']);
		if($MG['day_limit']) {
			$today = $today_endtime - 86400;
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $sql AND addtime>$today");
			if($r && $r['num'] >= $MG['day_limit']) mobile_msg($L['post_too_many_today']);
		}
		if($MG['club_free_limit'] >= 0) {
			$fee_add = ($MOD['fee_add'] && (!$MOD['fee_mode'] || !$MG['fee_mode']) && $limit_used >= $MG['club_free_limit'] && $_userid) ? dround($MOD['fee_add']) : 0;
		} else {
			$fee_add = 0;
		}
		if($fee_add) mobile_msg($L['post_msg_fee'].'<a href="'.$MODULE[2]['linkurl'].$DT['file_my'].'?mid='.$moduleid.'&action=add&gid='.$gid.'" rel="external" class="b">'.$L['post_msg_advance'].'</a>');

		$need_captcha = $MOD['captcha_add'] == 2 ? $MG['captcha'] : $MOD['captcha_add'];
		$need_question = $MOD['question_add'] == 2 ? $MG['question'] : $MOD['question_add'];
		if($need_question) $need_captcha = 1;
		if(isset($_POST['ok'])) {
			$captcha = isset($captcha) ? convert(input_trim($captcha), 'UTF-8', DT_CHARSET) : '';
			$msg = captcha($captcha, $need_captcha, true);
			if($msg) exit('captcha');
			$post = array();			
			$post['gid'] = $GRP['itemid'];
			$post['catid'] = $GRP['catid'];
			$post['title'] = isset($title) ? convert(input_trim($title), 'UTF-8', DT_CHARSET) : '';
			$post['content'] = isset($content) ? convert(input_trim(nl2br($content)), 'UTF-8', DT_CHARSET) : '';
			require DT_ROOT.'/include/post.func.php';
			require MD_ROOT.'/club.class.php';
			$do = new club($moduleid);
			if($do->pass($post)) {
				$post['addtime'] = $post['level'] = $post['fee'] = 0;
				$post['style'] = $post['template'] = $post['note'] = $post['thumb'] = $post['filepath'] = '';
				$need_check =  $MOD['check_add'] == 2 ? $MG['check'] : $MOD['check_add'];
				$post['status'] = get_status(3, $need_check);
				$post['hits'] = 0;
				$post['username'] = $_username;
				$post['areaid'] = $cityid;
				$do->add($post);
				if($MOD['show_html'] && $post['status'] > 2) $do->tohtml($do->itemid);
				exit($post['status'] == 3 ? 'ok|'.$do->itemid : 'check');
			}
			exit('ko');
		}
		$head_title = $L['post_title'].$DT['seo_delimiter'].$head_title;
		$foot = '';
		include template('club_post', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
	default:
		dheader(mobileurl($moduleid));
	break;
}
?>