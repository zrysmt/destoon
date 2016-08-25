<?php 
defined('IN_DESTOON') or exit('Access Denied');
class news {
	var $itemid;
	var $db;
	var $table;
	var $table_data;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		global $db;
		$this->table = $db->pre.'news';
		$this->table_data = $db->pre.'news_data';
		$this->db = &$db;
		$this->fields = array('title','typeid','level','style','status','username','addtime','editor','edittime','linkurl','note');
    }

    function news() {
		$this->__construct();
    }

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!$post['title']) return $this->_($L['pass_title']);
		if(!$post['content']) return $this->_($L['pass_content']);
		if(DT_MAX_LEN && strlen($post['content']) > DT_MAX_LEN) return $this->_(lang('message->pass_max'));
		return true;
	}

	function set($post) {
		global $MOD, $DT_TIME, $_username, $_userid;
		$post['addtime'] = (isset($post['addtime']) && $post['addtime']) ? strtotime($post['addtime']) : $DT_TIME;
		$post['edittime'] = $DT_TIME;
		clear_upload($post['content']);
		if($this->itemid) {
			$post['editor'] = $_username;
			$new = $post['content'];
			$r = $this->get_one();
			$old = $r['content'];
			delete_diff($new, $old);
		}
		$content = $post['content'];
		unset($post['content']);
		$post = dhtmlspecialchars($post);
		$post['content'] = dsafe($content);
		if($MOD['news_clear'] || $MOD['news_save']) {
			$post['content'] = stripslashes($post['content']);
			$post['content'] = save_local($post['content']);
			if($MOD['news_clear']) $post['content'] = clear_link($post['content']);
			if($MOD['news_save']) $post['content'] = save_remote($post['content']);
			$post['content'] = addslashes($post['content']);
		}
		return array_map("trim", $post);
	}

	function get_one($condition = '') {
        return $this->db->get_one("SELECT * FROM {$this->table} n,{$this->table_data} c WHERE n.itemid=c.itemid AND n.itemid='$this->itemid' $condition");
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
			$r['title'] = set_style($r['title'], $r['style']);
			$lists[] = $r;
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
		$this->db->query("REPLACE INTO {$this->table_data} (itemid,content) VALUES ('$this->itemid', '$post[content]')");		
		$this->update($this->itemid);
		if($post['username'] && $MOD['credit_add_news']) {
			credit_add($post['username'], $MOD['credit_add_news']);
			credit_record($post['username'], $MOD['credit_add_news'], 'system', $L['news_record_add'], 'ID:'.$this->itemid);
		}
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
	    $this->db->query("UPDATE {$this->table_data} SET content='$post[content]' WHERE itemid=$this->itemid");
		$this->update($this->itemid);
		return true;
	}

	function update($itemid) {
		$r = $this->db->get_one("SELECT username FROM {$this->table} WHERE itemid=$itemid");
		$linkurl = userurl($r['username'], 'file=news&itemid='.$itemid); 
		return $this->db->query("UPDATE {$this->table} SET linkurl='$linkurl' WHERE itemid=$itemid");
	}

	function recycle($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->recycle($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=0 WHERE itemid=$itemid");
			return true;
		}		
	}

	function restore($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->restore($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=3 WHERE itemid=$itemid");
			return true;
		}		
	}

	function delete($itemid, $all = true) {
		global $MOD, $L;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();
			$userid = get_user($r['username']);
			if($r['content']) delete_local($r['content'], $userid);
			$this->db->query("DELETE FROM {$this->table} WHERE itemid=$itemid");
			$this->db->query("DELETE FROM {$this->table_data} WHERE itemid=$itemid");
			if($r['username'] && $MOD['credit_del_news']) {
				credit_add($r['username'], -$MOD['credit_del_news']);
				credit_record($r['username'], -$MOD['credit_del_news'], 'system', $L['news_record_del'], 'ID:'.$this->itemid);
			}
		}
	}

	function check($itemid) {
		global $_username, $DT_TIME;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->check($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=3,editor='$_username',edittime=$DT_TIME WHERE itemid=$itemid");
			return true;
		}
	}

	function reject($itemid) {
		global $_username, $DT_TIME;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->reject($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=1,editor='$_username',edittime=$DT_TIME WHERE itemid=$itemid");
			return true;
		}
	}

	function clear() {		
		$result = $this->db->query("SELECT itemid FROM {$this->table} WHERE status=0");
		while($r = $this->db->fetch_array($result)) {
			$this->delete($r['itemid']);
		}
	}
	
	function level($itemid, $level) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$this->db->query("UPDATE {$this->table} SET level=$level WHERE itemid IN ($itemids)");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>