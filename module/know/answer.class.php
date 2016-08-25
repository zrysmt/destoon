<?php 
defined('IN_DESTOON') or exit('Access Denied');
class answer {
	var $itemid;
	var $db;
	var $table;
	var $errmsg = errmsg;

    function __construct() {
		global $db, $DT_PRE;
		$this->table = $DT_PRE.'know_answer';
		$this->db = &$db;
    }

    function answer() {
		$this->__construct();
    }

	function pass($post) {
		if(!is_array($post)) return false;
		if(!$post['content']) return $this->_(lang('message->pass_know_answer'));
		return true;
	}

	function set($post) {
		global $DT_TIME, $_username;
		$post['status'] = $post['status'] == 3 ? 3 : 2;
		$post['editor'] = $_username;
		$post['edittime'] = $DT_TIME;
		return array_map("trim", $post);
	}

	function get_one() {
        return $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid'");
	}

	function get_list($condition = 'status=3', $order = 'itemid DESC') {
		global $MOD, $TYPE, $pages, $page, $pagesize, $offset, $items, $sum;
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
			$r['adddate'] = timetodate($r['addtime'], 6);
			$lists[] = $r;
		}
		return $lists;
	}

	function edit($post) {
		$post = $this->set($post);
		$sql = '';
		foreach($post as $k=>$v) {
			$sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->db->query("UPDATE {$this->table} SET $sql WHERE itemid=$this->itemid");
		clear_upload($post['content']);
		return true;
	}

	function delete($itemid) {
		global $MOD, $DT_PRE;
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->delete($v); 
			}
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();
			if($r) {
				$this->db->query("DELETE FROM {$this->table} WHERE itemid=$itemid");
				$this->db->query("DELETE FROM {$DT_PRE}know_vote WHERE aid=$itemid");
				if($r['content']) delete_local($r['content'], get_user($r['username']));
				if($r['username'] && $MOD['credit_del_answer']) {
					credit_add($r['username'], -$MOD['credit_del_answer']);
					credit_record($r['username'], -$MOD['credit_del_answer'], 'system', lang('my->credit_record_answer_del'), 'ID:'.$r['qid']);
				}
			}
		}
	}

	function check($itemid, $status = 3) {
		global $MOD;
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->check($v, $status); 
			}
		} else {
			if($MOD['credit_answer'] && $status == 3) {
				$this->itemid = $itemid;
				$item = $this->get_one();
				if($item['username']) {
					credit_add($item['username'], $MOD['credit_answer']);
					credit_record($item['username'], $MOD['credit_answer'], 'system', lang('my->credit_record_answer_add'), 'ID:'.$itemid);
				}
			}
			$this->db->query("UPDATE {$this->table} SET status=$status WHERE itemid=$itemid");
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>