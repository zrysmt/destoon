<?php 
defined('IN_DESTOON') or exit('Access Denied');
class member {
	var $userid;
	var $username;
	var $db;
	var $table_member;
	var $table_member_check;
	var $table_company;
	var $table_company_data;
	var $errmsg = errmsg;

    function __construct() {
		global $db;
		$this->table_member = $db->pre.'member';
		$this->table_member_check = $db->pre.'member_check';
		$this->table_company = $db->pre.'company';
		$this->table_company_data = $db->pre.'company_data';
		$this->db = &$db;
    }

    function member() {
		$this->__construct();
    }

	function is_username($username) {
		global $MOD, $L;
		if(!check_name($username)) return $this->_($L['member_username_match']);
		$MOD['minusername'] or $MOD['minusername'] = 4;
		$MOD['maxusername'] or $MOD['maxusername'] = 20;
		if(strlen($username) < $MOD['minusername'] || strlen($username) > $MOD['maxusername']) return $this->_(lang($L['member_username_len'], array($MOD['minusername'], $MOD['maxusername'])));
		if($MOD['banusername'] && !$this->userid) {
			$tmp = explode('|', $MOD['banusername']);
			foreach($tmp as $v) {
				if($MOD['banmodeu']) {
					if($username == $v) return $this->_($L['member_username_ban']);
				} else {
					if(strpos($username, $v) !== false) return $this->_($L['member_username_ban']);
				}
			}
		}
		if($this->username_exists($username)) return $this->_($L['member_username_reg']);
		return true;
	}

	function is_company($company) {
		global $MOD, $L;
		$company = trim($company);
		if($MOD['bancompany'] && !$this->userid) {
			$tmp = explode('|', $MOD['bancompany']);
			foreach($tmp as $v) {
				if($MOD['banmodec']) {
					if($company == $v) return $this->_($L['member_company_ban']);
				} else {
					if(strpos($company, $v) !== false) return $this->_($L['member_company_ban']);
				}
			}
		}
		return true;
	}

	function is_email($email) {
		global $MOD, $L;
		$email = trim($email);
		if(!is_email($email)) return $this->_($L['member_email_null']);
		if($MOD['banemail']) {
			$domain = substr(strstr($email, '@'), 1);
			$tmp = explode('|', $MOD['banemail']);
			foreach($tmp as $v) {
				if($domain == $v) return $this->_($L['member_email_ban']);
			}
		}
		return true;
	}

	function is_passport($passport) {
		global $MOD, $L;
		$MOD['minusername'] or $MOD['minusername'] = 4;
		$MOD['maxusername'] or $MOD['maxusername'] = 20;
		if(strlen($passport) < $MOD['minusername'] || strlen($passport) > $MOD['maxusername']) return $this->_(lang($L['member_passport_len'], array($MOD['minusername'], $MOD['maxusername'])));
		$badwords = array("$","\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
		foreach($badwords as $v) {
			if(strpos($passport, $v) !== false) return $this->_($L['member_passport_char']);
		}
		if($MOD['banusername'] && !$this->userid) {
			$tmp = explode('|', $MOD['banusername']);
			foreach($tmp as $v) {
				if($MOD['banmodeu']) {
					if($passport == $v) return $this->_($L['member_passport_ban']);
				} else {
					if(strpos($passport, $v) !== false) return $this->_($L['member_passport_ban']);
				}
			}
		}
		if($this->passport_exists($passport)) return $this->_($L['member_passport_reg']);
		return true;
	}

	function is_password($password, $cpassword) {
		global $MOD, $L;
		if(!$password) return $this->_($L['member_password_null']);
		if($password != $cpassword) return $this->_($L['member_password_match']);
		if(!$MOD['minpassword']) $MOD['minpassword'] = 6;
		if(!$MOD['maxpassword']) $MOD['maxpassword'] = 20;
		if(strlen($password) < $MOD['minpassword'] || strlen($password) > $MOD['maxpassword']) return $this->_(lang($L['member_password_len'], array($MOD['minpassword'], $MOD['maxpassword'])));
		return true;
	}

	function is_payword($password, $cpassword) {
		global $MOD, $L;
		if(!$password) return $this->_($L['member_payword_null']);
		if($password != $cpassword) return $this->_($L['member_payword_match']);
		if(!$MOD['minpassword']) $MOD['minpassword'] = 6;
		if(!$MOD['maxpassword']) $MOD['maxpassword'] = 20;
		if(strlen($password) < $MOD['minpassword'] || strlen($password) > $MOD['maxpassword']) return $this->_(lang($L['member_payword_len'], array($MOD['minpassword'], $MOD['maxpassword'])));
		return true;
	}

	function is_clean($string) {
		$chars = array("\\", "'",'"','/','<','>',"\r","\t","\n");
		foreach($chars as $v) {
			if(strpos($string, $v) !== false) return false;
		}
		return true;
	}

	function is_member($member) {
		global $L, $AREA;
		if(!is_array($member)) return false;
		if(!$this->is_passport($member['passport'])) return false;
		if(!$member['groupid']) return $this->_($L['member_groupid_null']);
		if(strlen($member['truename']) < 2 || !$this->is_clean($member['truename'])) return $this->_($L['member_truename_null']);
		if(!$this->is_email(trim($member['email']))) return false;
		if($this->email_exists(trim($member['email']))) return $this->_($L['member_email_reg']);
		$groupid = $this->userid ? $member['groupid'] : $member['regid'];
		if($groupid > 5) {
			if(strlen($member['company']) < 2) return $this->_($L['member_company_null']);
			if(preg_match("/[0-9]+/", $member['company']) || !$this->is_clean($member['company'])) return $this->_($L['member_company_bad']);
			if($this->company_exists($member['company'])) return $this->_($L['member_company_reg']);
		}
		if($this->userid) {
			$areaid = intval($member['areaid']);
			if(!$areaid || !$this->db->get_one("SELECT areaid FROM {$this->db->pre}area WHERE areaid=$areaid")) return $this->_($L['member_areaid_null']);
			if($member['password'] && !$this->is_password($member['password'], $member['cpassword'])) return false;
			if($member['payword'] && !$this->is_payword($member['payword'], $member['cpayword'])) return false;
			if($member['groupid'] > 5) {
				if(strlen($member['type']) < 2) return $this->_($L['member_type_null']);
				if(!is_telephone($member['telephone'])) return $this->_($L['member_telephone_null']);
				if(strlen($member['regyear']) != 4 || !is_numeric($member['regyear'])) return $this->_($L['member_regyear_null']);
				if(empty($member['address'])) return $this->_($L['member_address_null']);
				if(word_count($member['content']) < 5) return $this->_($L['member_introduce_null']);
				if(!$member['business']) return $this->_($L['member_business_null']);
				if(strlen($member['catid']) < 2) return $this->_($L['member_catid_null']);
			}
		} else {
			if(!$this->is_username($member['username'])) return false;
			if($member['groupid'] > 5 && !$this->is_company($member['company'])) return false;
			if(!$this->is_password($member['password'], $member['cpassword'])) return false;
		}
		if(DT_MAX_LEN && strlen($member['content']) > DT_MAX_LEN) return $this->_(lang('message->pass_max'));
		return true;
	}

	function set_member($member) {
		global $MOD;
		$member['email'] = trim($member['email']);
		$member['mail'] = isset($member['mail']) ? trim($member['mail']) : '';
		is_email($member['mail']) or $member['mail'] = '';
		$member['msn'] = isset($member['msn']) ? trim($member['msn']) : '';
		is_email($member['msn']) or $member['msn'] = '';
		$member['qq'] = isset($member['qq']) ? trim($member['qq']) : '';
		is_numeric($member['qq']) or $member['qq'] = '';
		$member['postcode'] = isset($member['postcode']) ? trim($member['postcode']) : '';
		is_numeric($member['postcode']) or $member['postcode'] = '';
		$member['ali'] = isset($member['ali']) ? trim($member['ali']) : '';
		if(!$this->is_clean($member['ali'])) $member['ali'] = '';
		$member['skype'] = isset($member['skype']) ? trim($member['skype']) : '';
		if(!$this->is_clean($member['skype'])) $member['skype'] = '';
		$member['address'] = isset($member['address']) ? trim($member['address']) : '';
		if(!$this->is_clean($member['address'])) $member['address'] = '';
		$member['mode'] = (isset($member['mode']) && is_array($member['mode']) && $member['mode']) ? implode(',', $member['mode']) : '';
		$member['keyword'] = $member['company'];
		$member['homepage'] = isset($member['homepage']) ? fix_link($member['homepage']) : '';
		$member['capital'] = isset($member['capital']) ? dround($member['capital']) : '';
		$member['sound'] = isset($member['sound']) ? intval($member['sound']) : 0;
		if($this->userid) {
			$member['banktype'] = $member['banktype'] ? 1 : 0;
			$member['keyword'] = $member['company'].addslashes(strip_tags(area_pos($member['areaid'], ','))).','.$member['business'].','.$member['sell'].','.$member['buy'].','.$member['mode'];
			clear_upload($member['thumb'].$member['content'], $this->userid);
			$new = $member['content'];
			if($member['thumb']) $new .= '<img src="'.$member['thumb'].'">';
			$content_table = content_table(4, $this->userid, is_file(DT_CACHE.'/4.part'), $this->table_company_data);
			$r = $this->db->get_one("SELECT content FROM {$content_table} WHERE userid=$this->userid");
			$old = $r['content'];
			$r = $this->get_one();
			if($r['thumb']) $old .= '<img src="'.$r['thumb'].'">';
			delete_diff($new, $old);
		} else {
			if($member['thumb']) clear_upload($member['thumb'].$member['content']);
		}
		$member['introduce'] = addslashes(get_intro($member['content'], $MOD['introduce_length']));
		if(!defined('DT_ADMIN')) {
			$content = $member['content'];
			unset($member['content']);
			$member = dhtmlspecialchars($member);
			$member['content'] = dsafe($content);
		}
		if($MOD['introduce_clear'] || $MOD['introduce_save']) {
			$member['content'] = stripslashes($member['content']);
			$member['content'] = save_local($member['content']);
			if($MOD['introduce_clear']) $member['content'] = clear_link($member['content']);
			if($MOD['introduce_save']) $member['content'] = save_remote($member['content']);
			$member['content'] = addslashes($member['content']);
		}
		if($member['catid']) {
			$catids = explode(',', substr($member['catid'], 1, -1));
			$cids = '';
			foreach($catids as $catid) {
				$C = get_cat($catid);
				if($C) {
					$catid = $C['parentid'] ? $C['arrparentid'].','.$catid : $catid;
					$cids .= $catid.',';
				}
			}
			$cids = array_unique(explode(',', substr(str_replace(',0,', ',', ','.$cids), 1, -1)));
			$member['catids'] = ','.implode(',', $cids).',';
		}
		return $member;
	}

	function email_exists($email) {
		$condition = "email='$email'";
		if($this->userid) $condition .= " AND userid!=$this->userid";
		return $this->db->get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
	}

	function mobile_exists($mobile) {
		$condition = "mobile='$mobile'";
		if($this->userid) $condition .= " AND userid!=$this->userid";
		return $this->db->get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
	}

	function username_exists($username) {
		if(is_mobile($username)) return true;
		$t = $this->db->get_one("SELECT userid FROM {$this->table_member} WHERE username='$username'");
		if($t) return true;
		return $this->db->get_one("SELECT userid FROM {$this->table_member} WHERE passport='$username'");
	}

	function company_exists($company) {
		$condition = "company='$company'";
		if($this->userid) $condition .= " AND userid!=$this->userid";
		return $this->db->get_one("SELECT userid FROM {$this->table_company} WHERE {$condition}");
	}

	function passport_exists($passport) {
		$condition = "passport='$passport'";
		if($this->userid) $condition .= " AND userid!=$this->userid";
		$t = $this->db->get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
		if($t) return true;
		if(check_name($passport)) {
			$condition = "username='$passport'";
			if($this->userid) $condition .= " AND userid!=$this->userid";
			return $this->db->get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
		}
		return false;
	}

	function add($member) {
		global $DT, $DT_TIME, $DT_IP, $MOD, $L;
		if(!$this->is_member($member)) return false;
		$member = $this->set_member($member);
		$member['linkurl'] = userurl($member['username']);
		$password = $member['password'];
		$member['passsalt'] = random(8);
		$member['paysalt'] = random(8);
		$member['password'] = dpassword($password, $member['passsalt']);
		$member['payword'] = dpassword($password, $member['paysalt']);
		$member['sound'] = 1;
		$member_fields = array('username','company','passport', 'password','payword','email','sound','gender','truename','mobile','msn','qq','ali','skype','department','career','groupid','regid','areaid','edittime','inviter','passsalt', 'paysalt');
		$company_fields = array('username','groupid','company','type','catid','catids','areaid', 'mode','capital','regunit','size','regyear','sell','buy','business','telephone','fax','mail','address','postcode','homepage','introduce','thumb','keyword','linkurl');
		$member_sqlk = $member_sqlv = $company_sqlk = $company_sqlv = '';
		foreach($member as $k=>$v) {
			if(in_array($k, $member_fields)) {$member_sqlk .= ','.$k; $member_sqlv .= ",'$v'";}
			if(in_array($k, $company_fields)) {$company_sqlk .= ','.$k; $company_sqlv .= ",'$v'";}
		}
        $member_sqlk = substr($member_sqlk, 1);
        $member_sqlv = substr($member_sqlv, 1);
        $company_sqlk = substr($company_sqlk, 1);
        $company_sqlv = substr($company_sqlv, 1);
		$this->db->query("INSERT INTO {$this->table_member} ($member_sqlk,regip,regtime,loginip,logintime)  VALUES ($member_sqlv,'$DT_IP','$DT_TIME','$DT_IP','$DT_TIME')");
		$this->userid = $this->db->insert_id();
		if(!$this->userid) return 0;
		$member['userid'] = $this->userid;
		$this->username = $member['username'];
	    $this->db->query("INSERT INTO {$this->table_company} (userid, $company_sqlk) VALUES ('$this->userid', $company_sqlv)");
		$content_table = content_table(4, $this->userid, is_file(DT_CACHE.'/4.part'), $this->table_company_data);
	    $this->db->query("INSERT INTO {$content_table} (userid, content) VALUES ('$this->userid', '$member[content]')");
		if($MOD['credit_register'] > 0) {
			credit_add($this->username, $MOD['credit_register']);
			credit_record($this->username, $MOD['credit_register'], 'system', $L['member_record_reg'], $DT_IP);
		}
		if($MOD['money_register'] > 0) {
			money_add($this->username, $MOD['money_register']);
			money_record($this->username, $MOD['money_register'], $L['in_site'], 'system', $L['member_record_reg'], $DT_IP);
		}
		if($MOD['sms_register'] > 0) {
			sms_add($this->username, $MOD['sms_register']);
			sms_record($this->username, $MOD['sms_register'], 'system', $L['member_record_reg'], $DT_IP);
		}
		$this->bind($member['username'], $DT_TIME);
		return $this->userid;
	}

	function edit($member)	{
		if(!$this->is_member($member)) return false;
		$member = $this->set_member($member);
		$r = $this->get_one();
		$member_fields = array('company','passport','sound','email','msn','qq','ali','skype','gender','truename','mobile','department','career','groupid','areaid', 'edittime','black','bank','banktype','branch','account','vemail','vmobile','vbank','vtruename','vcompany','vtrade','trade','support','inviter');
		$company_fields = array('company','type','areaid', 'catid','catids','business','mode','regyear','regunit','capital','size','address','postcode','telephone','fax','mail','homepage','sell','buy','introduce','thumb','keyword','linkurl','groupid','domain','icp','validated','validator','validtime','skin','template');
		$member_sql = $company_sql = '';
		foreach($member as $k=>$v) {
			if(in_array($k, $member_fields)) $member_sql .= ",$k='$v'";
			if(in_array($k, $company_fields)) $company_sql .= ",$k='$v'";
		}
		if($member['password']) {
			$passsalt = random(8);
			$password = dpassword($member['password'], $passsalt);
			$member_sql .= ",password='$password',passsalt='$passsalt'";
		}
		if($member['payword']) {
			$paysalt = random(8);
			$payword = dpassword($member['payword'], $paysalt);
			$member_sql .= ",payword='$payword',paysalt='$paysalt'";
		}
        $member_sql = substr($member_sql, 1);
        $company_sql = substr($company_sql, 1);
	    $this->db->query("UPDATE {$this->table_member} SET $member_sql WHERE userid=$this->userid");
	    $this->db->query("UPDATE {$this->table_company} SET $company_sql WHERE userid=$this->userid");
		$content_table = content_table(4, $this->userid, is_file(DT_CACHE.'/4.part'), $this->table_company_data);
	    $this->db->query("UPDATE {$content_table} SET content='$member[content]' WHERE userid=$this->userid");
		$member['userid'] = $this->userid;
		$member['vip'] = $r['vip'];
		userclean($member['username']);
		return true;
	}

	function get_one($username = '') {
		$condition = $username ? "m.username='$username'" : "m.userid='$this->userid'";
        return $this->db->get_one("SELECT * FROM {$this->table_member} m,{$this->table_company} c WHERE m.userid=c.userid AND $condition");
	}

	function get_list($condition, $order = 'userid DESC') {
		global $pages, $page, $pagesize, $offset, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table_member} WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$members = array();
		$result = $this->db->query("SELECT * FROM {$this->table_member} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
		while($r = $this->db->fetch_array($result)) {
			$r['logindate'] = timetodate($r['logintime'], 5);
			$r['regdate'] = timetodate($r['regtime'], 5);
			$members[] = $r;
		}
		return $members;
	}

	function login($login_username, $login_password, $login_cookietime = 0, $admin = false) {
		global $DT_TIME, $DT_IP, $MOD, $MODULE, $L;
		if(!check_name($login_username)) return $this->_($L['member_login_username_bad']);
		if(!$MOD || !isset($MOD['login_times'])) $MOD = cache_read('module-2.php');
		$login_lock = ($MOD['login_times'] && $MOD['lock_hour']) ? true : false;
		$LOCK = array();
		if($login_lock) {
			$LOCK = cache_read($DT_IP.'.php', 'ban');
			if($LOCK) {
				if($DT_TIME - $LOCK['time'] < $MOD['lock_hour']*3600) {
					if($LOCK['times'] >= $MOD['login_times']) return $this->_(lang($L['member_login_ban'], array($MOD['login_times'], $MOD['lock_hour'])));
				} else {
					$LOCK = array();
					cache_delete($DT_IP.'.php', 'ban');
				}
			}
		}
		$user = userinfo($login_username, 0);
		if(!$user) {
			$this->lock($login_lock, $LOCK, $DT_IP, $DT_TIME);
			return $this->_($L['member_login_not_member']);
		}
		if(!$admin) {
			if($user['password'] != dpassword($login_password, $user['passsalt'])) {
				$this->lock($login_lock, $LOCK, $DT_IP, $DT_TIME);
				return $this->_($L['member_login_password_bad']);
			}
		}
		if($user['groupid'] == 2) return $this->_($L['member_login_member_ban']);
		$userid = $user['userid'];
		if($MOD['credit_login'] > 0 && timetodate($DT_TIME, 3) != timetodate($user['logintime'], 3)) {
			credit_add($login_username, $MOD['credit_login']);
			credit_record($login_username, $MOD['credit_login'], 'system', $L['member_record_login'], $DT_IP);
		}
		$cookietime = $DT_TIME + ($login_cookietime ? intval($login_cookietime) : 86400*7);
		$auth = encrypt($user['userid'].'|'.$user['password'], DT_KEY.'USER');
		set_cookie('auth', $auth, $cookietime);
		set_cookie('username', $user['username'], $DT_TIME + 30*86400);
		$this->db->query("UPDATE {$this->table_member} SET loginip='$DT_IP',logintime=$DT_TIME,logintimes=logintimes+1 WHERE userid=$userid");
		if(isset($MODULE[16])) $this->cart($userid);
		$this->bind($user['username'], $DT_TIME);
		return $user;
	}

	function cart($userid) {//SYNC
		global $DT_TIME;
		$r = $this->db->get_one("SELECT data FROM {$this->db->pre}mall_cart WHERE userid=$userid");
		if($r && $r['data']) {
			$cart = unserialize($r['data']);
			set_cookie('cart', count($cart), $DT_TIME + 30*86400);
		}
	}

	function bind($username, $addtime) {
		$auth = get_cookie('bind');
		if($auth) {
			set_cookie('bind', '');
			$auth = decrypt($auth, DT_KEY.'BIND');
			if(strpos($auth, '|') !== false) {
				$t = explode('|', $auth);
				$itemid = intval($t[0]);
				$this->db->query("UPDATE {$this->db->pre}oauth SET username='$username',addtime='$addtime' WHERE itemid=$itemid");
			}
		}
		$auth = get_cookie('weixin_openid');
		if($auth) {
			set_cookie('weixin_openid', '');
			$openid = decrypt($auth, DT_KEY.'WXID');
			if(is_openid($openid)) {
				$this->db->query("UPDATE {$this->db->pre}weixin_user SET username='$username',edittime='$addtime' WHERE openid='$openid'");
			}
		}
	}

	function lock($login_lock, $LOCK, $DT_IP, $DT_TIME) {
		if($login_lock && $DT_IP) {
			$LOCK['time'] = $DT_TIME;
			$LOCK['times'] = isset($LOCK['times']) ? $LOCK['times'] + 1 : 1;
			cache_write($DT_IP.'.php', $LOCK, 'ban');
		}
	}

	function logout() {
		global $_userid;
		$this->db->query("DELETE FROM {$this->db->pre}online WHERE userid=$_userid");
		set_cookie('auth', '');
		set_cookie('userid', '');
		return true;
	}

	function delete($userid) {
		global $dc, $CFG, $MODULE, $L;
		if(!$userid) return false;
		if(is_array($userid)) {
			if(in_array(1, $userid) || in_array($CFG['founderid'], $userid)) return $this->_($L['member_founder_del']);
			$userids = implode(',', $userid);
		} else {
			if($userid == 1 || $userid == $CFG['founderid']) return $this->_($L['member_founder_del']);
			$userids = intval($userid);
		}
		$result = $this->db->query("SELECT username,userid FROM {$this->table_member} WHERE userid IN ($userids)");
		while($r = $this->db->fetch_array($result)) {
			$userid = $r['userid'];
			$username = $r['username'];
			if(!$userid || !$username) continue;
			$content_table = content_table(4, $userid, is_file(DT_CACHE.'/4.part'), $this->table_company_data);
			$content_table = str_replace($this->db->pre, '', $content_table);
			foreach(array('address', 'admin_log', 'alert', 'ask', 'club_fans', 'club_group', 'club_manage', 'club_reply', 'comment', 'honor', 'finance_card', 'finance_cash', 'finance_charge', 'finance_credit', 'finance_deposit', 'finance_pay', 'finance_record', 'finance_sms', 'form_answer', 'gift_order', 'guestbook', 'job_talent', 'link', 'login', 'mail_list', 'spread', 'upgrade', 'know_answer', 'know_vote', 'validate', 'oauth', 'poll_record', 'vote_record', 'weixin_bind', 'weixin_user') as $v) {
				$this->deluser($v, $username, true);
			}
			foreach(array('news', 'page', 'resume') as $v) {
				$this->deluser($v, $username, true, true);
			}
			foreach($MODULE as $m) {
				if($m['islink'] || $m['moduleid'] < 5) continue;
				if(in_array($m['module'], array('article', 'brand', 'buy', 'down', 'info', 'photo', 'sell', 'video'))) {
					$this->deluser($m['module'].'_'.$m['moduleid'], $username, true, true, $m['moduleid']);
				} else {
					$this->deluser($m['module'], $username, true, true, $m['moduleid']);
				}
			}
			$this->db->query("DELETE FROM {$this->db->pre}group_order WHERE seller='$username'");
			$this->db->query("DELETE FROM {$this->db->pre}job_apply WHERE apply_username='$username'");
			$this->db->query("DELETE FROM {$this->db->pre}message WHERE fromuser='$username'");
			$this->db->query("DELETE FROM {$this->db->pre}message WHERE touser='$username'");
			$this->db->query("DELETE FROM {$this->db->pre}mall_order WHERE seller='$username'");
			$this->db->query("DELETE FROM {$this->db->pre}mall_comment WHERE seller='$username'");
			$this->db->query("DELETE FROM {$this->db->pre}mall_cart WHERE userid='$userid'");
			$this->db->query("DELETE FROM {$this->db->pre}type WHERE item='friend-".$userid."'");
			$this->db->query("DELETE FROM {$this->db->pre}type WHERE item='favorite-".$userid."'");
			$this->db->query("DELETE FROM {$this->db->pre}type WHERE item='product-".$userid."'");
			$this->db->query("DELETE FROM {$this->db->pre}type WHERE item='news-".$userid."'");
			$this->db->query("DELETE FROM {$this->db->pre}type WHERE item='mall-".$userid."'");
			foreach(array('member', 'member_check', 'company', $content_table, 'company_setting', 'admin', 'favorite', 'friend') as $v) {
				$this->deluser($v, $userid, false);
			}
			userclean($username);
			$this->delupload($username, $userid);
		}
		return true;
	}

	function deluser($table, $user, $name = true, $data = false, $moduleid = 0) {
		global $MODULE;
		if(!$user) return;
		$fields = $name ? 'username' : 'userid';
		if($data) {
			$result = $this->db->query("SELECT * FROM {$this->db->pre}{$table} WHERE `$fields`='$user'");
			while($r = $this->db->fetch_array($result)) {
				$itemid = $r['itemid'];
				$this->db->query("DELETE FROM {$this->db->pre}{$table} WHERE itemid='$itemid'");
				$table_data = strpos($table, '_') === false ? $table.'_data' : str_replace('_', '_data_', $table);
				$table_data = $this->db->pre.$table_data;
				if($moduleid) $table_data = content_table($moduleid, $itemid, is_file(DT_CACHE.'/'.$moduleid.'.part'), $table_data);
				$this->db->query("DELETE FROM {$table_data} WHERE itemid='$itemid'");
				if($MODULE[$moduleid]['module'] == 'sell') $this->db->query("DELETE FROM {$this->db->pre}sell_search_{$moduleid} WHERE itemid=$itemid");
				if($moduleid && $r['linkurl'] && strpos($r['linkurl'], '://') === false && strpos($r['linkurl'], '.php') === false && strpos($r['linkurl'], 'show-') === false) {
					$html = DT_ROOT.'/'.$MODULE[$moduleid]['moduledir'].'/'.$r['linkurl'];
					if(is_file($html)) file_del($html);
				}
			}
		} else {
			$this->db->query("DELETE FROM {$this->db->pre}{$table} WHERE `$fields`='$user'");
		}
	}

	function delupload($username, $userid) {
		if(!$userid || !$username) return;
		$result = $this->db->query("SELECT fileurl FROM {$this->db->pre}upload_".($userid%10)." WHERE username='$username'");
		while($r = $this->db->fetch_array($result)) {
			 delete_upload($r['fileurl'], $userid);
		}
	}

	function rename($cusername, $nusername) {
		global $MODULE, $L;
		$cusername = trim($cusername);
		$nusername = trim($nusername);
		if(!$this->username_exists($cusername)) return $this->_($L['member_rename_not_member']);
		if(!$this->is_username($nusername)) return false;
		$tables = array('alert', 'ask', 'comment', 'club_fans', 'club_group', 'club_manage', 'club_reply', 'finance_card', 'finance_cash', 'finance_charge', 'finance_pay', 'finance_deposit', 'finance_record', 'finance_sms', 'form_answer', 'form_record', 'guestbook', 'honor', 'job_talent', 'link', 'admin_log', 'login', 'mail_list', 'spread', 'news', 'resume', 'upgrade', 'know_answer', 'know_vote', 'news', 'page', 'address', 'oauth', 'vote_record', 'gift_order', 'poll_record', 'weixin_bind', 'weixin_user', 'member', 'member_check', 'company');
		foreach($MODULE as $m) {
			if($m['islink'] || $m['moduleid'] < 5) continue;
			$tables[] = in_array($m['module'], array('article', 'brand', 'buy', 'down', 'info', 'photo', 'sell', 'video')) ? $m['module'].'_'.$m['moduleid'] : $m['module'];
		}
		foreach($tables as $table) {
			$this->db->query("UPDATE {$this->db->pre}{$table} SET username='$nusername' WHERE username='$cusername'");
		}
		$this->db->query("UPDATE {$this->db->pre}mall_order SET buyer='$nusername' WHERE buyer='$cusername'");
		$this->db->query("UPDATE {$this->db->pre}mall_order SET seller='$nusername' WHERE seller='$cusername'");
		$this->db->query("UPDATE {$this->db->pre}group_order SET buyer='$nusername' WHERE buyer='$cusername'");
		$this->db->query("UPDATE {$this->db->pre}group_order SET seller='$nusername' WHERE seller='$cusername'");
		$this->db->query("UPDATE {$this->db->pre}job_apply SET apply_username='$nusername' WHERE apply_username='$cusername'");
		$this->db->query("UPDATE {$this->db->pre}message SET fromuser='$nusername' WHERE fromuser='$cusername'");
		$this->db->query("UPDATE {$this->db->pre}message SET touser='$nusername' WHERE touser='$cusername'");
		userclean($cusername);
		return true;
	}

	function edit_passport($cpassport, $npassport, $username) {
		if(!$this->is_passport($npassport)) return false;
		$this->db->query("UPDATE {$this->db->pre}member SET passport='$npassport' WHERE passport='$cpassport'");
		$this->db->query("UPDATE {$this->db->pre}comment SET passport='$npassport' WHERE passport='$cpassport'");
		$this->db->query("UPDATE {$this->db->pre}know SET passport='$npassport' WHERE passport='$cpassport'");
		$this->db->query("UPDATE {$this->db->pre}know_answer SET passport='$npassport' WHERE passport='$cpassport'");
		$this->db->query("UPDATE {$this->db->pre}know_expert SET passport='$npassport' WHERE passport='$cpassport'");
		$this->db->query("UPDATE {$this->db->pre}know_vote SET passport='$npassport' WHERE passport='$cpassport'");
		$this->db->query("UPDATE {$this->db->pre}club SET passport='$npassport' WHERE passport='$cpassport'");
		$this->db->query("UPDATE {$this->db->pre}club_fans SET passport='$npassport' WHERE passport='$cpassport'");
		$this->db->query("UPDATE {$this->db->pre}club_group SET passport='$npassport' WHERE passport='$cpassport'");
		$this->db->query("UPDATE {$this->db->pre}club_reply SET passport='$npassport' WHERE passport='$cpassport'");
		$this->db->query("UPDATE {$this->db->pre}club SET replyer='$npassport' WHERE replyer='$cpassport'");
		userclean($username);
		return true;
	}

	function move($userid, $groupid) {
		global $CFG, $L;
		if(is_array($userid)) {
			foreach($userid as $v) { $this->move($v, $groupid); }
		} else {
			$userid = intval($userid);
			if($userid == 1 || $userid == $CFG['founderid']) return $this->_($L['member_founder_move']);
			$this->userid = $userid;
			$user = $this->get_one();
			if($user) {
				$this->db->query("UPDATE {$this->table_member} SET groupid='$groupid' WHERE userid=$userid");
				$this->db->query("UPDATE {$this->table_company} SET groupid='$groupid' WHERE userid=$userid");
				userclean($user['username']);
			}
			
		}
		return true;
	}

	function check($userid) {
		if(is_array($userid)) {
			foreach($userid as $v) { $this->check($v); }
		} else {
			$this->userid = $userid;
			$user = $this->get_one();
			if($user) {
				$groupid = $user['regid'] ? $user['regid'] : 6;
				$this->db->query("UPDATE {$this->table_member} SET groupid=$groupid WHERE userid=$userid");
				$this->db->query("UPDATE {$this->table_company} SET groupid=$groupid WHERE userid=$userid");
				userclean($user['username']);
			}
			return true;
		}
	}

	function login_log($username, $password, $salt, $admin = 0, $message = '') {
		global $DT_TIME, $DT_IP, $L;
		$password = dpassword($password, $salt);
		$agent = addslashes(dhtmlspecialchars(strip_sql($_SERVER['HTTP_USER_AGENT'])));
		$message or $message = $L['member_login_ok'];
		if($message == $L['member_login_ok']) cache_delete($DT_IP.'.php', 'ban');
		$this->db->query("INSERT INTO {$this->db->pre}login (username,password,passsalt,admin,loginip,logintime,message,agent) VALUES ('$username','$password','$salt','$admin','$DT_IP','$DT_TIME','$message','$agent')");
	}

	function check_get() {
		$r = $this->db->get_one("SELECT content FROM {$this->table_member_check} WHERE userid=$this->userid");
		return $r['content'] ? dstripslashes(unserialize($r['content'])) : array();
	}

	function check_add($post) {
		global $DT_TIME;
		if(isset($post['content'])) {
			$content = dsafe($post['content']);
			unset($post['content']);
			$post = dhtmlspecialchars($post);
			$post['content'] = $content;
		} else {
			$post = dhtmlspecialchars($post);
		}
		$content = addslashes(serialize($post));
		$this->db->query("REPLACE INTO {$this->table_member_check} (userid,username,content,addtime) VALUES ('$this->userid','$this->username','$content','$DT_TIME')");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>