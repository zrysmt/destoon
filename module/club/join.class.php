<?php 
defined('IN_DESTOON') or exit('Access Denied');
class djoin {
	var $itemid;
	var $db;
	var $table;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		global $db, $DT_PRE;
		$this->table = $DT_PRE.'club_fans';
		$this->db = &$db;
		$this->fields = array('gid','reason','username','passport','addtime','status');
    }

    function djoin() {
		$this->__construct();
    }

	function pass($post) {
		global $GRP, $L;
		if(!is_array($post)) return false;
		if($GRP['join_type'] && !$post['reason']) return $this->_($L['join_pass_reason']);
		if(strlen($post['reason']) > DT_CHARLEN*500) return $this->_($L['join_pass_max_reason']);
		return true;
	}

	function set($post) {
		global $DT_TIME, $_username, $_passport;
		$post['status'] = $post['status'] == 3 ? 3 : 2;
		if($this->itemid) {
			//
		} else {
			$post['addtime'] = $DT_TIME;
			$post['username'] = $_username;
			$post['passport'] = $_passport;
		}
		$post = dhtmlspecialchars($post);
		return array_map("trim", $post);
	}

	function get_one() {
        return $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid'");
	}

	function get_list($condition = 'status=3', $order = 'itemid DESC') {
		global $MOD, $table, $pages, $page, $pagesize, $offset, $items, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();	
		$lists = $gids = $GRPS = array();
		$result = $this->db->query("SELECT itemid,gid,addtime FROM {$this->table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
		while($r = $this->db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$gids[$r['gid']] = $r['gid'];
			$lists[] = $r;
		}
		if($gids) {
			$result = $this->db->query("SELECT * FROM {$table}_group WHERE itemid IN (".implode(',', $gids).")");
			while($r = $this->db->fetch_array($result)) {
				$GRPS[$r['itemid']] = $r;
			}
			if($GRPS) {
				foreach($lists as $k=>$v) {
					$lists[$k]['title'] = $GRPS[$v['gid']]['title'];
					$lists[$k]['thumb'] = $GRPS[$v['gid']]['thumb'];
					$lists[$k]['post'] = $GRPS[$v['gid']]['post'];
					$lists[$k]['fans'] = $GRPS[$v['gid']]['fans'];
					$lists[$k]['linkurl'] = $MOD['linkurl'].$GRPS[$v['gid']]['linkurl'];
				}
			}
		}
		return $lists;
	}

	function add($post) {
		$post = $this->set($post);
		$sqlk = $sqlv = '';
		foreach($post as $k=>$v) {
			if(in_array($k, $this->fields)) { $sqlk .= ','.$k; $sqlv .= ",'$v'"; }
		}
        $sqlk = substr($sqlk, 1);
        $sqlv = substr($sqlv, 1);
		$this->db->query("INSERT INTO {$this->table} ($sqlk) VALUES ($sqlv)");
		$this->itemid = $this->db->insert_id();
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
		$sql = '';
		foreach($post as $k=>$v) {
			$sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->db->query("UPDATE {$this->table} SET $sql WHERE itemid=$this->itemid");
		return true;
	}

	function delete($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->delete($v); 
			}
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