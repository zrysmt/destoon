<?php 
defined('IN_DESTOON') or exit('Access Denied');
class spread {
	var $itemid;
	var $db;
	var $table;
	var $table_price;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		global $db, $DT_PRE;
		$this->table = $DT_PRE.'spread';
		$this->table_price = $DT_PRE.'spread_price';
		$this->db = &$db;
		$this->fields = array('mid','tid', 'word','price','currency','addtime','fromtime','totime','editor','edittime','username','company','status','note');
    }

    function spread() {
		$this->__construct();
    }

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!$post['word']) return $this->_($L['spread_pass_word']);
		if(!$post['price']) return $this->_($L['spread_pass_price']);
		if(!$post['fromtime'] || !$post['totime']) return $this->_($L['spread_pass_period']);
		if(!intval($post['tid'])) return $this->_($L['spread_pass_tid']);
		if(!$post['username']) return $this->_($L['spread_pass_username']);
		return true;
	}

	function set($post) {
		global $MOD, $DT_TIME, $_username, $_userid, $DT_PRE;
		$post['status'] = $post['status'] == 3 ? 3 : 2;
		$post['addtime'] = $DT_TIME;
		$post['edittime'] = $DT_TIME;
		$post['editor'] = $_username;
		$post['price'] = dround($post['price']);
		$post['fromtime'] = strtotime($post['fromtime'].' 0:0:0');
		$post['totime'] = strtotime($post['totime'].' 23:59:59');
		$m = $this->db->get_one("SELECT company FROM {$DT_PRE}member WHERE username='$post[username]'");
		if($m) $post['company'] = $m['company'];
		return array_map("trim", $post);
	}

	function get_one() {
        return $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid'");
	}

	function get_list($condition = '1', $order = 'itemid DESC') {
		global $MOD, $TYPE, $pages, $page, $pagesize, $offset, $DT_TIME, $L, $sum;
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
			$r['fromdate'] = timetodate($r['fromtime'], 3);
			$r['todate'] = timetodate($r['totime'], 3);
			if($r['totime'] < $DT_TIME) {
				$r['process'] = $L['status_expired'];
			} else if($r['fromtime'] > $DT_TIME) {
				$r['process'] = $L['status_not_start'];
			} else {
				$r['process'] = $L['status_displaying'];
			}
			$r['days'] = $r['totime'] > $DT_TIME ? intval(($r['totime']-$DT_TIME)/86400) : 0;
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		global $module;
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
		global $module;
		$post = $this->set($post);
		$sql = '';
		foreach($post as $k=>$v) {
			if(in_array($k, $this->fields)) $sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->db->query("UPDATE {$this->table} SET $sql WHERE itemid=$this->itemid");
		return true;
	}

	function delete($itemid) {
		global $module;
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->delete($v, $all); 
			}
		} else {
			$this->itemid = $itemid;
			$this->db->query("DELETE FROM {$this->table} WHERE itemid=$itemid");
		}
	}

	function check($itemid, $status) {
		global $_username, $DT_TIME;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->check($v, $status); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=$status,editor='$_username',edittime=$DT_TIME WHERE itemid=$itemid");
			return true;
		}
	}
	
	function get_price_list($condition = '1') {
		global $pages, $page, $pagesize, $offset, $pagesize, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table_price} WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $this->db->query("SELECT * FROM {$this->table_price} WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
		while($r = $this->db->fetch_array($result)) {
			$r['edittime'] = timetodate($r['edittime'], 6);
			$lists[] = $r;
		}
		return $lists;
	}

	function price_update($post) {
		$this->_add($post[0]);
		unset($post[0]);
		foreach($post as $k=>$v) {
			if(isset($v['delete'])) {
				$this->_delete($k);
				unset($post[$k]);
			}
		}
		$this->_edit($post);
		return true;
	}

	function _add($post) {
		global $DT_TIME, $_username;
		$post['word'] = trim($post['word']);
		if(!$post['word']) return false;
		$post['sell_price'] = dround($post['sell_price']);
		$post['buy_price'] = dround($post['buy_price']);
		$post['company_price'] = dround($post['company_price']);
		$this->db->query("INSERT INTO {$this->table_price} (word,sell_price,buy_price,company_price,editor,edittime) VALUES('$post[word]','$post[sell_price]','$post[buy_price]','$post[company_price]','$_username','$DT_TIME')");
	}

	function _edit($post) {
		foreach($post as $k=>$v) {
			$v['word'] = trim($v['word']);
			if(!$v['word']) return false;
			$v['sell_price'] = dround($v['sell_price']);
			$v['buy_price'] = dround($v['buy_price']);
			$v['company_price'] = dround($v['company_price']);
			$this->db->query("UPDATE {$this->table_price} SET word='$v[word]',sell_price='$v[sell_price]',buy_price='$v[buy_price]',company_price='$v[company_price]' WHERE itemid='$k'");
		}
	}

	function _delete($itemid) {
		$this->db->query("DELETE FROM {$this->table_price} WHERE itemid=$itemid");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>