<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
$moduleid = 2;
require 'common.inc.php';
$_userid or dheader('login.php?forward='.urlencode('message.php?action='.$action));
switch($action) {
	case 'send':
		if(isset($_POST['ok'])) {
			require DT_ROOT.'/include/post.func.php';
			require DT_ROOT.'/module/member/message.class.php';
			$do = new message;
			$message = array();
			$message['typeid'] = 0;
			$message['touser'] = input_trim($touser);
			$message['title'] = convert($title, 'UTF-8', DT_CHARSET);
			$message['content'] = convert($content, 'UTF-8', DT_CHARSET);
			if($do->send($message)) {
				mobile_msg($L['message_success'], $forward ? $forward : 'message.php?reload='.$DT_TIME);
			} else {
				mobile_msg($do->errmsg);
			}
		} else {
			$touser = isset($touser) ? trim($touser) : '';
			$title = isset($title) ? trim(decrypt($title, DT_KEY.'SEND')) : '';
			$content = isset($content) ? trim(decrypt($content, DT_KEY.'SEND')) : '';
			$typeid = isset($typeid) ? intval($typeid) : 0;
			$head_name = $L['message_send'];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
		}
	break;
	case 'delete':
		if($itemid) {
			require DT_ROOT.'/include/post.func.php';
			require DT_ROOT.'/module/member/message.class.php';
			$do = new message;			
			$do->itemid = $itemid;
			$do->delete();
			mobile_msg($L['message_delete'], 'message.php?reload='.$DT_TIME);
		} else {			
			mobile_msg($L['message_id']);
		}
	break;
	case 'show':
		if($itemid) {
			require DT_ROOT.'/module/member/message.class.php';
			$do = new message;
			$do->itemid = $itemid;
			$message = $do->get_one();
			if(!$message) mobile_msg($L['msg_no_right']);
			extract($message);
			if($status == 4 || $status == 3) {
				if($touser != $_username) mobile_msg($L['msg_no_right']);
				if(!$isread) {
					$do->read();
					if($feedback) $do->feedback();
				}
			} else if($status == 2 || $status == 1) {
				if($fromuser != $_username) mobile_msg($L['msg_no_right']);
			}
			$adddate = timetodate($addtime, 5);
			$head_name = $L['message_detail'];
			$head_title = $title.$DT['seo_delimiter'].$L['message_title'].$DT['seo_delimiter'].$head_title;
		} else {			
			mobile_msg($L['not_message']);
		}
	break;
	default:
		$TYPE = $L['message_type'];
		$typeid = isset($typeid) ? intval($typeid) : -1;
		$lists = array();
		if($_userid) {
			$condition = "touser='$_username' AND status=3";
			if($typeid != -1) $condition .= " AND typeid=$typeid";
			if($keyword) $condition .= " AND title LIKE '%$keyword%'";
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}message WHERE $condition");
			$pages = mobile_pages($r['num'], $page, $pagesize);
			$result = $db->query("SELECT * FROM {$DT_PRE}message WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
			while($r = $db->fetch_array($result)) {
				$r['adddate'] = timetodate($r['addtime'], 'Y/m/d H:i');
				$r['type'] = $TYPE[$r['typeid']];
				$lists[] = $r;
			}
		}
		$head_name = $kw ? $L['message_search'] : $L['message_title'];
		$head_title = $L['message_title'].$DT['seo_delimiter'].$head_title;
	break;
}
$foot = 'my';
include template('message', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>