<?php 
defined('IN_DESTOON') or exit('Access Denied');
class price {
	var $itemid;
	var $db;
	var $table;
	var $fields;

	function __construct() {
		global $db;
		$this->table = $db->pre.'quote_price';
		$this->table_product = $db->pre.'quote_product';
		$this->db = &$db;
		$this->fields = array('pid','price','market','username','areaid','company','telephone','qq','ip','addtime','status','editor','edittime','note');
	}

	function price() {
		$this->__construct();
	}

	function pass($post) {
		global $P, $L;
		if(!is_array($post) || !$P) return false;
		if(!$post['price']) return $this->_($L['msg_price']);
		if(!$post['username'] && !$post['company']) return $this->_($L['msg_company']);
		if(!$post['username'] && !$post['areaid']) return $this->_($L['msg_area']);
		if(($P['minprice'] && $post['price'] < $P['minprice']) || ($P['maxprice'] && $post['price'] > $P['maxprice'])) return $this->_($L['msg_bad']);
		return true;
	}

	function set($post) {
		global $MOD, $DT_TIME, $DT_IP, $_username;
		$post['addtime'] = (isset($post['addtime']) && $post['addtime']) ? strtotime($post['addtime']) : $DT_TIME;
		$post['editor'] = $_username;
		$post['edittime'] = $DT_TIME;
		$post['price'] = dround($post['price']);
		if($this->itemid) {
			//
		} else {
			$post['ip'] = $DT_IP;
		}
		$post = dhtmlspecialchars($post);
		return array_map("trim", $post);
	}

	function get_one($condition = '') {
        return $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid' $condition");
	}

	function get_list($condition = '1', $order = 'addtime DESC') {
		global $pages, $page, $pagesize, $offset, $pagesize, $MOD, $item, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = $pids = $P = array();
		$result = $this->db->query("SELECT * FROM {$this->table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
		while($r = $this->db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['linkurl'] = $MOD['linkurl'].rewrite('price.php?itemid='.$r['pid']);
			$pids[$r['pid']] = $r['pid'];
			$lists[] = $r;
		}
		if($pids) {
			$result = $this->db->query("SELECT * FROM {$this->table_product} WHERE itemid IN (".implode(',', $pids).")");
			while($r = $this->db->fetch_array($result)) {
				$P[$r['itemid']] = $r;
			}
			if($P) {
				foreach($lists as $k=>$v) {
					$lists[$k]['unit'] = $P[$v['pid']]['unit'];
					$lists[$k]['alt'] = $P[$v['pid']]['title'];
					$lists[$k]['title'] = set_style($P[$v['pid']]['title'], $P[$v['pid']]['style']);
				}
			}
		}
		return $lists;
	}

	function add($post) {
		global $MOD, $L;
		$post = $this->set($post);
		$sqlk = $sqlv = '';
		foreach($post as $k=>$v) {
			if(in_array($k, $this->fields)) { $sqlk .= ','.$k; $sqlv .= ",'$v'"; }
		}
        $sqlk = substr($sqlk, 1);
        $sqlv = substr($sqlv, 1);
		$this->db->query("INSERT INTO {$this->table} ($sqlk) VALUES ($sqlv)");
		$this->itemid = $this->db->insert_id();
		$this->update($this->itemid, $post);
		$this->product($this->itemid, $post['pid']);
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
		$sql = '';
		foreach($post as $k=>$v) {
			if(in_array($k, $this->fields)) $sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->db->query("UPDATE {$this->table} SET $sql WHERE itemid=$this->itemid");
		$this->update($this->itemid, $post);
		$this->product($this->itemid, $post['pid']);
		return true;
	}

	function update($itemid, $item = array()) {
		$item or $item = $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid=$itemid");
		$sql = '';
		if($item['username']) {
			$m = daddslashes(userinfo($item['username']));
			if($m) $sql = "company='$m[company]',telephone='$m[telephone]',qq='$m[qq]',areaid='$m[areaid]'";
		}
		if($sql) $this->db->query("UPDATE {$this->table} SET $sql WHERE itemid=$itemid");
	}

	function check($itemid) {
		global $_username, $DT_TIME;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->check($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=3,editor='$_username',edittime=$DT_TIME WHERE itemid=$itemid");
			$this->product($itemid);
			return true;
		}
	}

	function delete($itemid) {
		global $MOD, $L;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			$this->itemid = $itemid;
			$t = $this->get_one();
			$pid = $t['pid'];
			$this->db->query("DELETE FROM {$this->table} WHERE itemid=$itemid");
			$this->product($itemid, $pid);
		}
	}

	function product($itemid, $pid = 0) {
		global $DT_TIME;
		if(!$pid) {
			$this->itemid = $itemid;
			$t = $this->get_one();
			$pid = $t['pid'];
		}
		if(!$pid) return false;
		$t = $this->db->get_one("SELECT price FROM {$this->table} WHERE status=3 AND pid=$pid ORDER BY addtime DESC");
		$price = $t['price'];
		if($price) $this->db->query("UPDATE {$this->table_product} SET price='$price',edittime=$DT_TIME WHERE itemid=$pid");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>