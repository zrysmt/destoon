<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$DT['sms'] or dheader($MOD['linkurl']);
if(!$MG['sms']) dalert(lang('message->without_permission_and_upgrade'), 'goback');
$_user = $db->get_one("SELECT mobile,vmobile FROM {$DT_PRE}member WHERE userid=$_userid");
if(!$_user['mobile'] || !$_user['vmobile']) message($L['sms_msg_validate'], 'validate.php?action=mobile');
require DT_ROOT.'/include/post.func.php';
$mobile = $_user['mobile'];
switch($action) {
	case 'add':
		$_sms > 0 or message($L['sms_msg_buy'], '?action=buy');
		if($submit) {
			$mob = trim($mob);
			$mob or message($L['sms_msg_mobile']);
			$message = trim($content);
			$message or message($L['sms_msg_content']);
			$mob = explode("\n", $mob);
			$DT['sms_sign'] = '';
			$message = strip_sms($message);
			$word = word_count($message);
			$sms_num = ceil($word/$DT['sms_len']);
			$s = 0;
			foreach($mob as $mobile) {
				$mobile = trim($mobile);
				if(is_mobile($mobile) && $sms_num <= $_sms) {
					$sms_code = send_sms($mobile, $message, $word);
					if(strpos($sms_code, $DT['sms_ok']) !== false) {
						$tmp = explode('/', $sms_code);
						if(is_numeric($tmp[1])) $sms_num = $tmp[1];
						sms_add($_username, -$sms_num);
						sms_record($_username, -$sms_num, $_username, $L['sms_add_record'], $mobile);
						$_sms = $_sms - $sms_num;
						$s++;
					}
				}
			}
			dmsg(lang($L['sms_add_success'], array($s)), '?action=send');
		} else {
			$mob = isset($mob) ? $mob : '';
			if(isset($auth)) {
				$auth = decrypt($auth, DT_KEY.'SMS');
				if(is_mobile($auth)) $mob = $auth;
			}
			$head_title = $L['sms_add_title'];
		}
	break;
	case 'buy':
		$fee = $DT['sms_fee'];
		$fee or message($L['sms_msg_no_price']);
		if($fee) {
			if($submit) {
				is_payword($_username, $password) or message($L['error_payword']);
				$total = intval($total);
				$total > 0 or message($L['sms_msg_buy_num']);
				$amount = $total*$fee;
				if($amount > 0) {
					$_money >= $amount or message($L['money_not_enough'], 'charge.php?action=pay&amount='.($amount-$_money));
					money_add($_username, -$amount);
					money_record($_username, -$amount, $L['in_site'], 'system', $L['sms_buy_note'], $total);
					sms_add($_username, $total);
					sms_record($_username, $total, 'system', $L['sms_buy_record'], $amount.$DT['money_unit']);
				}
				dmsg($L['sms_buy_success'], '?action=index');
			}
		} else {
			message($L['sms_msg_no_price']);
		}
		$head_title = $L['sms_buy_title'];
	break;
	case 'record':
		isset($fromtime) or $fromtime = '';
		isset($totime) or $totime = '';
		$condition = "mobile='$mobile'";
		if($keyword) $condition .= " AND message LIKE '%$keyword%'";
		if($fromtime) $condition .= " AND sendtime>".(strtotime($fromtime.' 00:00:00'));
		if($totime) $condition .= " AND sendtime<".(strtotime($totime.' 23:59:59'));
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}sms WHERE $condition");
		$pages = pages($r['num'], $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}sms WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			$r['message'] = preg_replace("/:([0-9]{6}),/", ':******,', $r['message']);
			$r['sendtime'] = str_replace(' ', '<br/>', timetodate($r['sendtime'], 6));
			$r['num'] = ceil($r['word']/$DT['sms_len']);
			$lists[] = $r;
		}
		$head_title = $L['sms_record_title'];
	break;
	case 'send':
		isset($fromtime) or $fromtime = '';
		isset($totime) or $totime = '';
		$condition = "editor='$_username'";
		if($keyword) $condition .= " AND message LIKE '%$keyword%'";
		if($fromtime) $condition .= " AND sendtime>".(strtotime($fromtime.' 00:00:00'));
		if($totime) $condition .= " AND sendtime<".(strtotime($totime.' 23:59:59'));
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}sms WHERE $condition");
		$pages = pages($r['num'], $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}sms WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			$r['message'] = preg_replace("/:([0-9]{6}),/", ':******,', $r['message']);
			$r['sendtime'] = str_replace(' ', '<br/>', timetodate($r['sendtime'], 6));
			$r['num'] = ceil($r['word']/$DT['sms_len']);
			$lists[] = $r;
		}
		$head_title = $L['sms_send_title'];
	break;
	default:
		$sfields = $L['sms_sfields'];
		$dfields = array('reason', 'amount', 'reason', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($fromtime) or $fromtime = '';
		isset($totime) or $totime = '';
		isset($type) or $type = 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = "username='$_username'";
		if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
		if($fromtime) $condition .= " AND addtime>".(strtotime($fromtime.' 00:00:00'));
		if($totime) $condition .= " AND addtime<".(strtotime($totime.' 23:59:59'));
		if($type) $condition .= $type == 1 ? " AND amount>0" : " AND amount<0" ;
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}finance_sms WHERE $condition");
		$pages = pages($r['num'], $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}finance_sms WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			$r['amount'] > 0 ? $income += $r['amount'] : $expense += $r['amount'];
			$lists[] = $r;
		}
		$head_title = $L['sms_title'];
	break;
}
include template('sms', $module);
?>