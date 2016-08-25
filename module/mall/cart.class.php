<?php 
defined('IN_DESTOON') or exit('Access Denied');
class cart {
	var $db;
	var $table;
	var $userid;
	var $time;
	var $max;

    function __construct() {
		global $db, $table_cart, $DT_TIME, $_userid;
		$this->userid = $_userid;
		$this->time = $DT_TIME;
		$this->table = $table_cart;
		$this->db = &$db;
    }

    function cart() {
		$this->__construct();
    }

	function set($cart) {
		set_cookie('cart', count($cart), $this->time + 30*86400);
		$data = addslashes(serialize($cart));
		$this->db->query("REPLACE INTO {$this->table} (userid,data,edittime) VALUES ('$this->userid', '$data', '$this->time')");
	}

	function add($cart, $itemid, $s1, $s2, $s3, $a) {
		global $table, $_username;
		if(is_array($itemid) && count($itemid) == 1) {
			$id = $itemid[0];
			$itemid = $id;
		}
		$id = 0;
		if(is_array($itemid)) {
			$tags = array();
			$itemids = implode(',', $itemid);
			$result = $this->db->query("SELECT itemid,username,status FROM {$table} WHERE itemid IN ($itemids)");
			while($r = $this->db->fetch_array($result)) {		
				$tags[$r['itemid']] = $r;
			}
			foreach($itemid as $v) {
				if(!isset($tags[$v])) continue;
				if($tags[$v]['status'] != 3) continue;
				if($tags[$v]['username'] == $_username) continue;
				$k = $v.'-0-0-0';
				if(isset($cart[$k])) {
					$cart[$k] = $cart[$k] + 1;
				} else {
					$cart[$k] = 1;
				}
				$id = $v;
			}
			if($id == 0) return -3;
		} else {
			$r = $this->db->get_one("SELECT username,status FROM {$table} WHERE itemid=$itemid");
			if(!$r) return -1;
			if($r['status'] != 3) return -1;
			if($r['username'] == $_username) return -4;
			$k = $itemid.'-'.$s1.'-'.$s2.'-'.$s3;
			if(isset($cart[$k])) {
				$cart[$k] = $cart[$k] + $a;
			} else {
				$cart[$k] = $a;
			}
			$id = $itemid;
		}
		$max = $this->max > 1 ? $this->max : 30;
		while(count($cart) > $max) {
			$cart = array_shift($cart);
		}
		$this->set($cart);
		return $id;
	}

	function get() {
		$r = $this->db->get_one("SELECT data FROM {$this->table} WHERE userid=$this->userid");
		return ($r && $r['data']) ? unserialize($r['data']) : array();
	}

	function clear() {
		set_cookie('cart', '0', $this->time + 30*86400);
		$this->db->query("DELETE FROM {$this->table} WHERE userid=$this->userid");
	}

	function get_list($cart) {
		global $MOD, $table, $_username;
		$lists = $tags = $data = $_cart = array();
		$itemids = '';
		foreach($cart as $k=>$v) {
			$t = array_map('intval', explode('-', $k));
			$itemids .= ','.$t[0];
			$r = array();
			$r['itemid'] = $t[0];
			$r['s1'] = $t[1];
			$r['s2'] = $t[2];
			$r['s3'] = $t[3];
			$r['a'] = $v;
			$data[$k] = $r;
		}
		if($itemids) {
			$itemids = substr($itemids, 1);
			$result = $this->db->query("SELECT * FROM {$table} WHERE itemid IN ($itemids)");
			while($r = $this->db->fetch_array($result)) {
				if($r['username'] == $_username || $r['status'] != 3) continue;
				$r['alt'] = $r['title'];
				$r['title'] = dsubstr($r['title'], 40, '..');
				$r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
				$r['P1'] = get_nv($r['n1'], $r['v1']);
				$r['P2'] = get_nv($r['n2'], $r['v2']);
				$r['P3'] = get_nv($r['n3'], $r['v3']);
				if($r['step']) {
					$s = unserialize($r['step']);
					foreach(unserialize($r['step']) as $k=>$v) {
						$r[$k] = $v;
					}
				} else {
					$r['a1'] = 1;
					$r['p1'] = $r['price'];
					$r['a2'] = $r['a3'] = 0;
					$r['p2'] = $r['p3'] = 0.00;
				}			
				$tags[$r['itemid']] = $r;
			}
			if($tags) {
				foreach($data as $k=>$v) {
					if(isset($tags[$v['itemid']])) {
						$r = $tags[$v['itemid']];
						$r['key'] = $k;
						$r['s1'] = $v['s1'];
						$r['s2'] = $v['s2'];
						$r['s3'] = $v['s3'];
						$r['a'] = $v['a'];
						if($r['a'] > $r['amount']) $r['a'] = $r['amount'];
						if($r['a'] < $r['a1']) $r['a'] = $r['a1'];
						$r['price'] = get_price($r['a'], $r['price'], $r['step']);
						$r['m1'] = isset($r['P1'][$r['s1']]) ? $r['P1'][$r['s1']] : '';
						$r['m2'] = isset($r['P2'][$r['s2']]) ? $r['P2'][$r['s2']] : '';
						$r['m3'] = isset($r['P3'][$r['s3']]) ? $r['P3'][$r['s3']] : '';
						$_cart[$k] = $r['a'];
						$lists[] = $r;
					}
				}
			}
		}
		if(count($_cart) != count($cart) || count($_cart) != get_cookie('cart')) $this->set($_cart);
		return $lists;
	}
}
?>