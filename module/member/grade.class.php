<?php 
defined('IN_DESTOON') or exit('Access Denied');
class grade {
	var $itemid;
	var $db;
	var $table;
	var $errmsg = errmsg;

    function __construct() {
		global $db, $DT_PRE;
		$this->table = $DT_PRE.'upgrade';
		$this->db = &$db;
    }

    function grade() {
		$this->__construct();
    }

	function get_one($condition = '') {
        return $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid' $condition");
	}

	function get_list($condition = 'status=3', $order = 'addtime DESC') {
		global $MOD, $pages, $page, $pagesize, $offset, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = $this->db->query("SELECT * FROM {$this->table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
		while($r = $this->db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$lists[] = $r;
		}
		return $lists;
	}

	function edit($post) {
		global $DT_PRE, $_username, $DT_TIME, $GROUP, $L;
		$item = $this->get_one();
		$user = $item['username'] ? userinfo($item['username']) : array();
		$gsql = $msql = $csql = '';
		$gsql = "edittime=$DT_TIME,editor='$_username',status=$post[status],note='$post[note]'";
		if($post['status'] == 1) {
			//reject
			if($user) {
				if($post['message'] && $post['content']) {
					send_message($user['username'], lang($L['grade_fail'], array($GROUP[$item['groupid']]['groupname'])), nl2br($post['content']));
					$gsql .= ",message=1";
				}
				if($item['amount']) {
					money_add($item['username'], $item['amount']);
					money_record($item['username'], $item['amount'], $L['in_site'], 'system', $L['grade_title'], $L['grade_return']);
				}
			}
		} else if($post['status'] == 2) {
			//
		} else if($post['status'] == 3) {
			if($user) {
				if(isset($post['pay']) && $post['pay']) {
					if($user['money'] < $post['pay']) {
						return $this->_($L['grade_pass_balance']);
					} else {
						money_add($item['username'], -$post['pay']);
						money_record($item['username'], -$post['pay'], $L['in_site'], 'system', $L['grade_title'], $L['grade_upto'].$GROUP[$item['groupid']]['groupname']);
					}
				}
				$msql = $csql = "groupid=$item[groupid],company='$item[company]'";
				$vip = $GROUP[$item['groupid']]['vip'];
				$csql .= ",vip=$vip,vipt=$vip";
				if(isset($post['pay'])) {
					$csql .= ",fromtime=".strtotime($post['fromtime']).",totime=".strtotime($post['totime']).",validtime=".strtotime($post['validtime']).",validator='$post[validator]',validated=$post[validated]";
				}
				if($post['message'] && $post['content']) {
					send_message($user['username'], lang($L['grade_success'], array($GROUP[$item['groupid']]['groupname'])), nl2br($post['content']));
					$gsql .= ",message=1";
				}
			}
		}
		$this->db->query("UPDATE {$this->table} SET $gsql WHERE itemid=$this->itemid");
		if($msql) $this->db->query("UPDATE {$DT_PRE}member SET $msql WHERE userid=$item[userid]");
		if($csql) $this->db->query("UPDATE {$DT_PRE}company SET $csql WHERE userid=$item[userid]");
		return true;
	}

	function delete($itemid, $all = true) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			$this->db->query("DELETE FROM {$this->table} WHERE itemid=$itemid");
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>