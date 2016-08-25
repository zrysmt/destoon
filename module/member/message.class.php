<?php 
defined('IN_DESTOON') or exit('Access Denied');
class message {
	var $itemid;
	var $userid;
	var $username;
	var $db;
	var $pre;
	var $errmsg = errmsg;

    function __construct() {
		global $db, $DT_PRE, $_userid, $_username;
		$this->userid = $_userid;
		$this->username = $_username;
		$this->pre = $DT_PRE;
		$this->db = &$db;
    }

    function message()	{
		$this->__construct();
    }

	function is_message($message) {
		global $L;
		if(!is_array($message)) return false;
		if(empty($message['title'])) return $this->_($L['pass_title']);
		if(empty($message['content'])) return $this->_($L['pass_content']);
		if(preg_match("/(embed|object)/i", $message['content'])) return false;
		if(DT_MAX_LEN && strlen($message['content']) > DT_MAX_LEN) return $this->_(lang('message->pass_max'));
		return true;
	}

	function is_member($username) {
		return $this->db->get_one("SELECT userid FROM {$this->pre}member WHERE username='$username'");
	}

	function send($message) {
		global $DT, $MODULE, $MOD, $DT_TIME, $DT_IP, $_email, $L;
		if(!$this->is_message($message)) return false;
		clear_upload($message['content']);
		$message['title'] = dhtmlspecialchars(trim($message['title']));
		$message['content'] = dsafe(addslashes(save_remote(save_local(stripslashes($message['content'])))));
		if(isset($message['save'])) {
			$this->db->query("INSERT INTO {$this->pre}message(title,typeid,content,fromuser,touser,addtime,ip,status) values('$message[title]','$message[typeid]','$message[content]','$this->username','$message[touser]','$DT_TIME','$DT_IP','1')");
		} else {
			if(substr_count($message['touser'], ' ') > ($MOD['maxtouser']-1)) return $this->_(lang($L['message_send_max'], array($MOD['maxtouser'])));
			$tousers = array();
			$feedback = isset($message['feedback']) ? 1 : 0;
			foreach(explode(' ', $message['touser']) as $touser) {
				$touser = strtolower($touser);
				$user = $this->db->get_one("SELECT black FROM {$this->pre}member WHERE username='$touser'");
				if($user) {
					$blacks = $user['black'] ? explode(' ', $user['black']) : array();
					if(!in_array($this->username, $blacks) && !in_array($touser, $tousers)) {
						$tousers[] = $touser;
						if(isset($message['copy'])) $this->db->query("INSERT INTO {$this->pre}message (title,typeid,content,fromuser,touser,addtime,ip,feedback,status) VALUES ('$message[title]','$message[typeid]','$message[content]','$this->username','$touser','$DT_TIME','$DT_IP','$feedback','2')");
						$this->db->query("UPDATE {$this->pre}member SET message=message+1 WHERE username='$touser'");
						$this->db->query("INSERT INTO {$this->pre}message (title,typeid,content,fromuser,touser,addtime,ip,feedback,status) VALUES ('$message[title]','$message[typeid]','$message[content]','$this->username','$touser','$DT_TIME','$DT_IP','$feedback','3')");
					}
				}
			}
		}
		$this->itemid = $this->db->insert_id();
		return true;
	}
	
	function edit($message) {
		global $L;
		if(!$this->is_message($message)) return false;
		$r = $this->get_one();
		if($r['status'] != 1 || $r['fromuser'] != $this->username) return $this->_($L['message_msg_edit']);
		clear_upload($message['content']);
		$message['title'] = dhtmlspecialchars(trim($message['title']));
		$message['content'] = dsafe(addslashes(save_remote(save_local(stripslashes($message['content'])))));
		delete_diff($message['content'], $r['content']);
		$this->db->query("UPDATE {$this->pre}message SET title='$message[title]',content='$message[content]' WHERE itemid='$this->itemid' ");
		if(isset($message['send'])) return $this->send($message);
		return true;
	}

	function get_one() {
        return $this->db->get_one("SELECT * FROM {$this->pre}message WHERE itemid='$this->itemid'");
	}

	function get_list($condition, $order = 'itemid DESC') {
		global $MODULE, $pages, $page, $pagesize, $offset, $pagesize, $L, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->pre}message WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$messages = array();
		$result = $this->db->query("SELECT * FROM {$this->pre}message WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
		while($r = $this->db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], $L['message_list_date']);
			$r['dtitle'] = dsubstr($r['title'], 55, '...');
			$r['user'] = $r['status'] > 2 ? ($r['fromuser'] ? $r['fromuser'] : $L['message_from_system']) : $r['touser'];
			if($r['fromuser']) {
				$r['user'] =  $r['status'] > 2 ? $r['fromuser'] : $r['touser'];
				$r['userurl'] = userurl($r['user']);
			} else {
				$r['user'] = $r['typeid'] == 4 ? $L['message_from_system'] : $L['guest'];
				$r['userurl'] = '';
			}
			$messages[] = $r;
		}
		return $messages;
	}

	function get_sys() {
		global $_groupid, $L;
		$messages = array();
		$result = $this->db->query("SELECT * FROM {$this->pre}message WHERE groupids<>'' ORDER BY itemid DESC", 'CACHE');
		while($r = $this->db->fetch_array($result)) {
			$groupids = explode(',', $r['groupids']);
			if(!in_array($_groupid, $groupids)) continue;
			$r['user'] = $L['message_from_notice'];
			$r['adddate'] = timetodate($r['addtime'], $L['message_list_date']);
			$messages[] = $r;
		}
		return $messages;
	}

	function export($message) {
		global $DT_TIME, $module, $DT, $L;
		$message['status'] = intval($message['status']);
		if(!in_array($message['status'], array(1, 2, 3 ,4))) return false;
		$status = $message['status'];
		$fromtime = isset($message['fromdate']) && is_date($message['fromdate']) ? strtotime($message['fromdate'].' 0:0:0') : 0;
		$totime = isset($message['todate']) && is_date($message['todate']) ? strtotime($message['todate'].' 23:59:59') : 0;
		$condition = "status='$status'";
		$condition .= $status > 2 ? " AND touser='$this->username'" : " AND fromuser='$this->username'";
		if($fromtime) $condition .= " AND addtime>'$fromtime' ";
		if($totime) $condition .= " AND addtime<'$totime' ";
		if(isset($message['isread'])) $condition .= " AND isread=0 ";
		$data = '';
		$result = $this->db->query("SELECT * FROM {$this->pre}message WHERE $condition ORDER BY itemid DESC Limit 100");
		while($r = $this->db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], $L['message_list_date']);
			$r['fromuser'] = $r['fromuser'] ? $r['fromuser'] : 'system';
			$data .= '<strong>'.$r['title'].'</strong><br/>'.$r['fromuser'].'@'.$r['addtime'].'<br/>'.$r['content'].'<hr size="1"/>';
		}
		if($data) {
			$names = $L['message_names'];
			$filename = 'message-'.timetodate($DT_TIME, 'YmdHis');
			$data = '<html><meta http-equiv="Content-Type" content="text/html;charset='.DT_CHARSET.'"/><title>'.$this->username.' '.$names[$status].''.$DT['sitename'].' '.timetodate($DT_TIME, 5).' - Powered By DESTOON.COM</title><style>*{font-size:13px;font-family:Verdana,Arial;}body{width:750px;margin:auto;line-height:200%;}</style><base target="_blank"/><base href="'.DT_PATH.'"/><body><br/>'.$data.'<a href="http://www.destoon.com"><small>Powered By DESTOON.COM</small></a><br/></body></html>';
			ob_start();
			header('Cache-control: max-age=31536000');
			header('Expires: '.gmdate('D, d M Y H:i:s', $DT_TIME + 31536000).' GMT');
			header('Content-Length: '.strlen($data));
			header('Content-Disposition:attachment; filename='.$filename.'.htm');
			header('Content-Type:application/octet-stream');
			echo $data;
			exit;
		} else {
			$this->errmsg = $L['message_msg_null'];
			return false;
		}
	}

	function clear($status) {
		if($status == 4 || $status == 3) {
			$this->db->query("DELETE FROM {$this->pre}message WHERE status='$status' AND touser='$this->username' ");
			if($status == 3) $this->db->query("UPDATE {$this->pre}member SET message=0 WHERE username='$this->username' ");
		} else if($status == 2 || $status == 1) {			
			$this->db->query("DELETE FROM {$this->pre}message WHERE status='$status' AND fromuser='$this->username' ");
		}
	}

	function delete($recycle = 0) {
		if(!$this->itemid) return false;
		$itemids = is_array($this->itemid) ? implode(',', $this->itemid) : intval($this->itemid);
		$result = $this->db->query("SELECT * FROM {$this->pre}message WHERE itemid IN($itemids) ORDER BY itemid DESC");
		while($r = $this->db->fetch_array($result)) {
			if(defined('DT_ADMIN')) {
				if($r['status'] == 3 && !$r['isread']) $this->db->query("UPDATE {$this->pre}member SET message=message-1 WHERE username='$r[touser]' ");
				$this->db->query("DELETE FROM {$this->pre}message WHERE itemid='$r[itemid]'");
			} else {
				if($r['status'] == 4) {
					if($this->username == $r['touser']) $this->_delete($r['itemid']);
				} else if($r['status'] == 3) {
					if($this->username == $r['touser']) {
						if($recycle) {
							$this->db->query("UPDATE {$this->pre}message SET status=4 WHERE itemid='$r[itemid]' ");
						} else {
							$this->_delete($r['itemid']);
						}
						if(!$r['isread']) $this->db->query("UPDATE {$this->pre}member SET message=message-1 WHERE username='$this->username' ");
					}
				} else if($r['status'] == 2 || $r['status'] == 1) {
					if($this->username == $r['fromuser']) $this->_delete($r['itemid']);
				}
			}
		}
	}

	function mark() {
		if(!$this->itemid) return false;
		$itemids = is_array($this->itemid) ? implode(',', $this->itemid) : intval($this->itemid);
		$condition = "status=3 AND isread=0 AND touser='$this->username' AND itemid IN($itemids)";
		$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->pre}message WHERE $condition");
		if($r['num']) {
			$this->db->query("UPDATE {$this->pre}message SET isread=1 WHERE $condition");
			$this->db->query("UPDATE {$this->pre}member SET message=message-$r[num] WHERE username='$this->username' ");
		}
	}

	function markall() {
		$this->db->query("UPDATE {$this->pre}message SET isread=1 WHERE status=3 AND isread=0 AND touser='$this->username'");
		$this->db->query("UPDATE {$this->pre}member SET message=0 WHERE username='$this->username' ");
	}

	function restore() {
		if(!$this->itemid) return false;
		$itemids = is_array($this->itemid) ? implode(',', $this->itemid) : intval($this->itemid);
		$result = $this->db->query("SELECT * FROM {$this->pre}message WHERE itemid IN($itemids) ORDER BY itemid DESC");
		while($r = $this->db->fetch_array($result)) {
			if($r['status'] == 4 && $this->username == $r['touser']) {
				$this->db->query("UPDATE {$this->pre}message SET status=3 WHERE itemid='$r[itemid]' ");				
				if(!$r['isread']) $this->db->query("UPDATE {$this->pre}member SET message=message+1 WHERE username='$this->username' ");
			}
		}
	}

	function read() {
		$this->db->query("UPDATE {$this->pre}message SET isread=1 WHERE itemid='$this->itemid'");
		$this->db->query("UPDATE {$this->pre}member SET message=message-1 WHERE userid='$this->userid'");
	}

	function color($style) {
		$message = $this->get_one();
		if($message['status'] == 3 && $message['touser'] == $this->username) {
			$this->db->query("UPDATE {$this->pre}message SET style='$style' WHERE itemid='$this->itemid'");
		}
	}

	function feedback($r) {
		global $DT_TIME, $L;
		$r or $r = $this->get_one();
		$message = array();
		$message['typeid'] = 0;
		$message['touser'] = $r['fromuser'];
		$message['title'] = lang($L['message_feedback_title'], array(dsubstr($r['title'], 20, '...')));
		$message['content'] = lang($L['message_feedback_content'], array($this->username, timetodate($DT_TIME, 5), $r['title'], timetodate($r['addtime'], 5), $r['content']));
		$this->send($message);
	}

	function fix_message() {
		global $_username, $_message;
		$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->pre}message WHERE touser='$_username' AND status=3 AND isread=0");
		$num = intval($r['num']);
		if($_message != $num) {
			$this->db->query("UPDATE {$this->pre}member SET message='$num' WHERE username='$_username'");
			dheader('message.php');
		}
	}

	function _is_message($message) {
		global $L;
		if(!is_array($message)) return false;
		if($message['type']) {
			if(!isset($message['groupids']) || !is_array($message['groupids']) || empty($message['groupids'])) return $this->_($L['message_pass_groupid']);
		} else {
			if(!$message['touser']) return $this->_($L['message_pass_touser']);
		}
		if(!$message['title'] || !$message['content']) return $this->_($L['message_pass_title']);
		return true;
	}

	function _send($message) {
		global $DT_TIME;
		if(!$this->_is_message($message)) return false;
		clear_upload($message['content']);
		$message['title'] = dhtmlspecialchars(trim($message['title']));
		$message['content'] = dsafe(addslashes(save_remote(save_local(stripslashes($message['content'])))));
		if($message['type']) {
			$message['groupids'] = implode(',', $message['groupids']);
			$this->db->query("INSERT INTO {$this->pre}message(title,content,fromuser,touser,addtime,status,groupids) values('$message[title]','$message[content]','$this->username','','$DT_TIME','0','$message[groupids]')");
		} else {
			foreach(explode(' ', $message['touser']) as $touser) {
				send_message($touser, $message['title'], stripslashes($message['content']));
			}
		}
		return true;
	}

	function _edit($message) {
		if(!$this->_is_message($message)) return false;
		clear_upload($message['content']);
		$message['title'] = dhtmlspecialchars(trim($message['title']));
		$message['content'] = dsafe(addslashes(save_remote(save_local(stripslashes($message['content'])))));
		$message['groupids'] = implode(',', $message['groupids']);
		$this->db->query("UPDATE {$this->pre}message SET title='$message[title]',content='$message[content]',groupids='$message[groupids]' WHERE itemid='$this->itemid' ");
		return true;
	}

	function _clear($message) {
		global $L;
		$message['status'] = intval($message['status']);
		if(!in_array($message['status'], array(0, 1, 2, 3 ,4))) return false;
		$status = $message['status'];
		$fromtime = isset($message['fromdate']) && is_date($message['fromdate']) ? strtotime($message['fromdate'].' 00:00:00') : 0;
		$totime = isset($message['todate']) && is_date($message['todate']) ? strtotime($message['todate'].' 23:59:59') : 0;
		$condition = "1";
		if($status) $condition .= " AND status='$status'";
		if($fromtime) $condition .= " AND addtime>='$fromtime'";
		if($totime) $condition .= " AND addtime<='$totime'";
		if(isset($message['isread'])) $condition .= " AND isread=1";
		if(isset($message['username'])) $condition .= " AND touser='$message[username]'";
		$this->db->query("DELETE FROM {$this->pre}message WHERE $condition");
		return $this->db->affected_rows() ? true : $this->_($L['message_msg_null']);
	}

	function _delete($itemid) {
		$this->itemid = $itemid;
		$r = $this->get_one();
		if($r['fromuser']) {
			$userid = get_user($r['fromuser']);
			if($r['content']) delete_local($r['content'], $userid);
		}
		$this->db->query("DELETE FROM {$this->pre}message WHERE itemid='$itemid' ");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>