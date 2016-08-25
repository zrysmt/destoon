<?php
defined('DT_ADMIN') or exit('Access Denied');
$tb = isset($tb) ? strip_sql(trim($tb), 0) : '';
$tb or msg();
$len = strlen($DT_PRE);
if(substr($tb, 0, $len) == $DT_PRE) $tb = substr($tb, $len);
$do = new fields();
$do->tb = $tb;
$menus = array (
    array('添加字段', '?file='.$file.'&tb='.$tb.'&action=add'),
    array('字段列表', '?&file='.$file.'&tb='.$tb),
);
$this_forward = '?moduleid='.$moduleid.'&file='.$file.'&tb='.$tb;
switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				$do->add($post);
				dmsg('添加成功', $this_forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			include tpl('fields_add');
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->pass($post)) {
				$do->edit($post);
				dmsg('修改成功', $this_forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($do->get_one());
			include tpl('fields_edit');
		}
	break;
	case 'delete':
		$itemid or msg();
		$do->delete($itemid);
		dmsg('删除成功', $this_forward);
	break;
	case 'order':
		$do->order($listorder);
		dmsg('更新成功', $this_forward);
	break;
	case 'delete':
		$itemid or msg();
		$do->delete($itemid);
		dmsg('删除成功', $this_forward);
	break;
	default:
		$lists = $do->get_list("tb='$tb'");
		cache_fields($tb);
		include tpl('fields');
	break;
}

class fields {
	var $itemid;
	var $db;
	var $tb;
	var $pre;
	var $table;
	var $errmsg = errmsg;

    function __construct() {
		global $db, $DT_PRE;
		$this->pre = $DT_PRE;
		$this->table = $DT_PRE.'fields';
		$this->db = &$db;
    }

    function fields() {
		$this->__construct();
    }

	function pass($post) {
		global $DT_TIME;
		if(!is_array($post)) return false;
		if(!$post['name']) return $this->_('请填写字段');
		if(!preg_match("/^[a-z0-9]+$/", $post['name'])) return $this->_('字段名只能为小写字母和数字的组合');
		if(!$post['title']) return $this->_('请填写字段名称');
		if(in_array($post['html'], array('select', 'radio', 'checkbox'))) {
			if(!$post['option_value']) return $this->_('请填写选项值');
			if(strpos($post['option_value'], '|') === false) return $this->_('请填写正确的选项值');
		}
		return true;
	}

	function set($post) {
		if(!in_array($post['html'], array('select', 'radio', 'checkbox'))) {
			$post['option_value'] = '';
		}
		$post['length'] = intval($post['length']);
		if($post['html'] == 'textarea') {
			if($post['type'] != 'varchar' && $post['type'] != 'text') $post['type'] = 'text';
		} else if($post['html'] == 'checkbox' || $post['html'] == 'thumb' || $post['html'] == 'file') {
			$post['type'] = 'varchar';
			$post['length'] = 255;
		} else if($post['html'] == 'editor') {
			$post['type'] = 'text';
		} else if($post['html'] == 'area') {
			$post['type'] = 'int';
			$post['length'] = 10;
		}
		return $post;
	}

	function get_one() {
        return $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid'");
	}

	function get_list($condition = '', $order = 'listorder ASC,itemid ASC') {
		global $MOD, $pages, $page, $pagesize, $offset, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = $this->db->query("SELECT * FROM {$this->table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
		while($r = $this->db->fetch_array($result)) {
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		$post = $this->set($post);
		$length = 0;
		if($post['type'] == 'varchar') {
			$length = min($post['length'], 255);
		} else if($post['type'] == 'int') {
			$length = min($post['length'], 10);
		}
		$type = strtoupper($post['type']);
		if($length) $type .= "($length)";
		$name = '`'.$post['name'].'`';
        $this->db->query("ALTER TABLE {$this->pre}{$this->tb} ADD $name $type NOT NULL");
		$sqlk = $sqlv = '';
		foreach($post as $k=>$v) {
			$sqlk .= ','.$k; $sqlv .= ",'$v'";
		}
        $sqlk = substr($sqlk, 1);
        $sqlv = substr($sqlv, 1);
		$this->db->query("INSERT INTO {$this->table} ($sqlk) VALUES ($sqlv)");
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
		$length = 0;
		if($post['type'] == 'varchar') {
			$length = min($post['length'], 255);
		} else if($post['type'] == 'int') {
			$length = min($post['length'], 10);
		}
		$type = strtoupper($post['type']);
		if($length) $type .= "($length)";
		$cname = '`'.$post['cname'].'`';
		unset($post['cname']);
		$name = '`'.$post['name'].'`';
        $this->db->query("ALTER TABLE {$this->pre}{$this->tb} CHANGE $cname $name $type NOT NULL");
		$sql = '';
		foreach($post as $k=>$v) {
			$sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->db->query("UPDATE {$this->table} SET $sql WHERE itemid=$this->itemid");
		return true;
	}

	function delete($itemid) {
		$this->itemid = $itemid;
		$r = $this->get_one();
		$name = '`'.$r['name'].'`';
		$this->db->query("DELETE FROM {$this->table} WHERE itemid=$itemid");
	    $this->db->query("ALTER TABLE {$this->pre}{$this->tb} DROP $name");
	}
	
	function order($listorder) {
		if(!is_array($listorder)) return false;
		foreach($listorder as $k=>$v) {
			$k = intval($k);
			$v = intval($v);
			$this->db->query("UPDATE {$this->table} SET listorder=$v WHERE itemid=$k");
		}
		return true;
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>