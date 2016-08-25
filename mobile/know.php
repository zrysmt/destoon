<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
$moduleid = 10;
require 'common.inc.php';
require DT_ROOT.'/module/club/common.inc.php';
$head_title = $MOD['name'].$DT['seo_delimiter'].$head_title;
switch($action) {
	case 'user':
		(isset($username) && check_name($username)) or $username = '';
		$username or mobile_msg($L['msg_not_user']);
		$_userid or dheader('login.php?forward='.urlencode('know.php?action='.$action.'&username='.$username));
		$user = userinfo($username);
		$user or mobile_msg($L['msg_not_user']);
		$typeid = (isset($typeid) && $typeid == 1) ? 1 : 0;
		if($typeid == 1) {
			$condition = "status=3 AND username='$username'";
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table}_answer WHERE $condition", 'CACHE');
			$items = $r['num'];
			$pages = mobile_pages($items, $page, $pagesize);
			$lists = array();
			if($items) {
				$result = $db->query("SELECT * FROM {$table}_answer WHERE $condition ORDER BY addtime DESC LIMIT $offset,$pagesize");
				while($r = $db->fetch_array($result)) {
					$r['title'] = get_intro($r['content'], 50);
					$r['date'] = timetodate($r['addtime'], 'Y/m/d H:i');
					$lists[] = $r;
				}
				$db->free_result($result);
			}
		} else {
			$condition = "status=3 AND username='$username'";
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
		include template('know_user', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
	case 'answer':
		$itemid or dheader(mobileurl($moduleid));
		$_userid or dheader('login.php?forward='.urlencode('know.php?action='.$action.'&itemid='.$itemid));
		check_group($_groupid, $MOD['group_answer']) or mobile_msg($L['know_msg_right']);
		$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		($item && $item['status'] > 2) or mobile_msg($L['know_msg_not_question']);
		if($item['process'] != 1 || ($_username && $_username == $item['username'])) mobile_msg($L['know_msg_right']);
		if(!$MOD['answer_repeat']) {
			if($_username) {
				$r = $db->get_one("SELECT itemid FROM {$table_answer} WHERE username='$_username' AND qid=$itemid");
			} else {
				$r = $db->get_one("SELECT itemid FROM {$table_answer} WHERE ip='$DT_IP' AND qid=$itemid AND addtime>$DT_TIME-86400");
			}
			if($r) mobile_msg($L['know_msg_has_answered']);
		}
		$need_captcha = $MOD['captcha_answer'] == 2 ? $MG['captcha'] : $MOD['captcha_answer'];
		$need_question = $MOD['question_answer'] == 2 ? $MG['question'] : $MOD['question_answer'];
		if($need_question) $need_captcha = 1;
		if(isset($_POST['ok'])) {
			require_once DT_ROOT.'/include/post.func.php';
			$captcha = isset($captcha) ? convert(input_trim($captcha), 'UTF-8', DT_CHARSET) : '';
			$msg = captcha($captcha, $need_captcha, true);
			if($msg) exit('captcha');
			$content = isset($content) ? convert(input_trim(nl2br($content)), 'UTF-8', DT_CHARSET) : '';
			$need_check =  $MOD['check_answer'] == 2 ? $MG['check'] : $MOD['check_answer'];
			$status = get_status(3, $need_check);
			$db->query("INSERT INTO {$table}_answer (qid,content,username,addtime,ip,status) VALUES ('$itemid', '$content', '$_username', '$DT_TIME', '$DT_IP', '$status')");			
			if($status == 3) $db->query("UPDATE {$table} SET answer=answer+1 WHERE itemid=$itemid");
			if($MOD['credit_answer'] && $_username && $status == 3) {
				$could_credit = true;
				if($MOD['credit_maxanswer'] > 0) {					
					$r = $db->get_one("SELECT SUM(amount) AS total FROM {$DT_PRE}finance_credit WHERE username='$_username' AND addtime>$DT_TIME-86400  AND reason='".$L['answer']."'");
					if($r['total'] > $MOD['credit_maxanswer']) $could_credit = false;
				}
				if($could_credit) {
					credit_add($_username, $MOD['credit_answer']);
					credit_record($_username, $MOD['credit_answer'], 'system', $L['answer'], 'ID:'.$itemid.'('.$L['know_by_mobile'] .')');
				}
			}
			if($MOD['answer_message'] && $item['username']) {
				$linkurl = $MOD['linkurl'].$item['linkurl'];
				$message = lang($L['answer_message'], array(dsubstr($item['title'], 20, '...'), $item['title'], nl2br($content), $linkurl));
				send_message($item['username'], dsubstr($message, 60, '...'), $message);
			}
			exit($status == 3 ? 'ok' : 'check');
		}
		$head_title = $L['know_title'].$DT['seo_delimiter'].$head_title;
		$foot = '';
		include template('know_answer', 'mobile');
		if(DT_CHARSET != 'UTF-8') toutf8();
	break;
	default:
		dheader(mobileurl($moduleid));
	break;
}
?>