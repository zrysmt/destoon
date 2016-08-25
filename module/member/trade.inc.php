<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('order.lang');
$_status = $L['trade_status'];
$dstatus = $L['trade_dstatus'];
$_send_status = $L['send_status'];
$dsend_status = $L['send_dstatus'];
$step = isset($step) ? trim($step) : '';
$timenow = timetodate($DT_TIME, 3);
$memberurl = $MOD['linkurl'];
$myurl = userurl($_username);
$table = $DT_PRE.'mall_order';
$STARS = $L['star_type'];
if($action == 'update') {
	$itemid or message();
	$td = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	$td or message($L['trade_msg_null']);
	if($td['buyer'] != $_username && $td['seller'] != $_username) message($L['trade_msg_deny']);
	$td['adddate'] = timetodate($td['addtime'], 5);
	$td['updatedate'] = timetodate($td['updatetime'], 5);
	$td['linkurl'] = DT_PATH.'api/redirect.php?mid='.$td['mid'].'&itemid='.$td['mallid'];
	$td['par'] = '';
	if(strpos($td['note'], '|') !== false) list($td['note'], $td['par']) = explode('|', $td['note']);
	$mallid = $td['mallid'];
	$nav = $_username == $td['buyer'] ? 'action_order' : 'action';
	switch($step) {
		case 'edit_price'://修改价格||确认订单||修改为货到付款
			if($td['status'] > 1 || $td['seller'] != $_username) message($L['trade_msg_deny']);
			if($DT['trade'] && $_trade == '') message(lang($L['trade_msg_pay_bind'], array($DT['trade_nm'])), '?action=bind');
			if($submit) {
				$fee = dround($fee);
				if($fee < 0 && $fee < -$td['amount']) message(lang($L['trade_msg_less_fee'], array(-$td['amount'])));
				$fee_name = dhtmlspecialchars(trim($fee_name));
				$status = isset($confirm_order) ? 1 : 0;
				$cod = 0;
				if(isset($edit_cod)) {
					$cod = 1;
					$status = 7;
				}
				$db->query("UPDATE {$table} SET fee='$fee',fee_name='$fee_name',status=$status,cod=$cod,updatetime=$DT_TIME WHERE itemid=$itemid");				
				if(isset($confirm_order)) {
					$touser = $td['buyer'];
					$title = lang($L['trade_message_t1'], array($itemid));
					$url = $memberurl.'trade.php?action=order&itemid='.$itemid;
					$content = lang($L['trade_message_c1'], array($myurl, $_username, $timenow, $url));
					$content = ob_template('messager', 'mail');
					send_message($touser, $title, $content);
					//send sms
					if($DT['sms'] && $_sms && $touser && isset($sendsms)) {
						$touser = userinfo($touser);
						if($touser['mobile']) {
							$message = lang('sms->ord_confirm', array($itemid));
							$message = strip_sms($message);
							$word = word_count($message);
							$sms_num = ceil($word/$DT['sms_len']);
							if($sms_num <= $_sms) {
								$sms_code = send_sms($touser['mobile'], $message, $word);
								if(strpos($sms_code, $DT['sms_ok']) !== false) {
									$tmp = explode('/', $sms_code);
									if(is_numeric($tmp[1])) $sms_num = $tmp[1];
									if($sms_num) sms_add($_username, -$sms_num);
									if($sms_num) sms_record($_username, -$sms_num, $_username, $L['trade_sms_confirm'], $itemid);
								}
							}
						}
					}
					//send sms
				}
				message($L['trade_price_edit_success'], $forward, 3);
			} else {
				$confirm = isset($confirm) ? 1 : 0;
				$head_title = $L['trade_price_title'];
			}
		break;
		case 'detail'://订单详情
			$td['total'] = $td['amount'] + $td['fee'];
			$auth = encrypt('mall|'.$td['send_type'].'|'.$td['send_no'].'|'.$td['send_status'].'|'.$td['itemid'], DT_KEY.'EXPRESS');
			$head_title = $L['trade_detail_title'];
		break;
		case 'print'://订单打印
			$td['total'] = $td['amount'] + $td['fee'];
			if($td['seller'] != $_username) message($L['trade_msg_deny']);
			include template('trade_print', $module);
			exit;
		break;
		case 'express'://快递追踪
			($td['send_type'] && $td['send_no']) or dheader('?action=update&step=detail&itemid='.$itemid);
			$auth = encrypt('mall|'.$td['send_type'].'|'.$td['send_no'].'|'.$td['send_status'].'|'.$td['itemid'], DT_KEY.'EXPRESS');
			$head_title = $L['trade_exprss_title'];
		break;
		case 'pay'://买家付款
			if($td['status'] == 2) dmsg($L['trade_pay_order_success'], '?action=order&nav=2&itemid='.$itemid);
			if($td['status'] == 0) message($L['trade_msg_confirm'], '?action=update&step=detail&itemid='.$itemid);
			if($td['status'] != 1 || $td['buyer'] != $_username) message($L['trade_msg_deny']);
			$money = $td['amount'] + $td['fee'];
			$money > 0 or message($L['trade_msg_deny']);
			$seller = userinfo($td['seller']);
			if($DT['trade']) exit(include DT_ROOT.'/api/trade/'.$DT['trade'].'/update.inc.php');
			$auto = 0;
			$auth = isset($auth) ? dround(decrypt($auth, DT_KEY.'CG')) : '';
			if($auth && $_money >= $money && $auth <= $money && $auth >= dround($money*0.5)) $auto = $submit = 1;
			if($submit) {
				if(!$auto) {
					is_payword($_username, $password) or message($L['error_payword']);
				}
				$_money >= $money or message($L['money_not_enough']);
				money_add($_username, -$money);
				money_record($_username, -$money, $L['in_site'], 'system', $L['trade_pay_order_title'], $L['trade_order_id'].$itemid);
				$db->query("UPDATE {$table} SET status=2,updatetime=$DT_TIME WHERE itemid=$itemid");

				$touser = $td['seller'];
				$title = lang($L['trade_message_t2'], array($itemid));
				$url = $memberurl.'trade.php?itemid='.$itemid;
				$content = lang($L['trade_message_c2'], array($myurl, $_username, $timenow, $url));
				$content = ob_template('messager', 'mail');
				send_message($touser, $title, $content);			
				//send sms
				if($DT['sms'] && $_sms && $touser && isset($sendsms)) {
					$touser = userinfo($touser);
					if($touser['mobile']) {
						$message = lang('sms->ord_pay', array($itemid, $money));
						$message = strip_sms($message);
						$word = word_count($message);
						$sms_num = ceil($word/$DT['sms_len']);
						if($sms_num <= $_sms) {
							$sms_code = send_sms($touser['mobile'], $message, $word);
							if(strpos($sms_code, $DT['sms_ok']) !== false) {
								$tmp = explode('/', $sms_code);
								if(is_numeric($tmp[1])) $sms_num = $tmp[1];
								if($sms_num) sms_add($_username, -$sms_num);
								if($sms_num) sms_record($_username, -$sms_num, $_username, $L['trade_sms_pay'], $itemid);
							}
						}
					}
				}
				//send sms
				//更新商品数据
				if($td['mid'] == 16) {
					$db->query("UPDATE {$DT_PRE}mall SET orders=orders+1,sales=sales+$td[number],amount=amount-$td[number] WHERE itemid=$mallid");
				} else {
					$db->query("UPDATE ".get_table($td['mid'])." SET amount=amount-$td[number] WHERE itemid=$mallid");
				}
				dmsg($L['trade_pay_order_success'], '?action=order&nav=2&itemid='.$itemid);
			} else {
				$head_title = $L['trade_pay_order_title'];
			}
		break;
		case 'refund'://买家退款
			if($DT['trade']) exit(include DT_ROOT.'/api/trade/'.$DT['trade'].'/update.inc.php');
			$gone = $DT_TIME - $td['updatetime'];
			if(!in_array($td['status'], array(2, 3)) || $td['buyer'] != $_username) message($L['trade_msg_deny']);
			if($td['status'] == 3 && $gone > ($MOD['trade_day']*86400 + $td['add_time']*3600)) message($L['trade_msg_deny']);
			$money = $td['amount'] + $td['fee'];
			if($submit) {
				$content or message($L['trade_refund_reason']);
				clear_upload($content);
				$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
				is_payword($_username, $password) or message($L['error_payword']);
				$db->query("UPDATE {$table} SET status=5,updatetime=$DT_TIME,buyer_reason='$content' WHERE itemid=$itemid");
				message($L['trade_refund_success'], $forward, 3);
			} else {
				$head_title = $L['trade_refund_title'];
			}
		break;
		case 'refund_agree'://卖家同意买家退款
			if($DT['trade']) exit(include DT_ROOT.'/api/trade/'.$DT['trade'].'/update.inc.php');
			if($td['status'] != 5 || $td['seller'] != $_username) message($L['trade_msg_deny']);
			$money = $td['amount'] + $td['fee'];
			if($submit) {
				$content .= $L['trade_refund_by_seller'];
				clear_upload($content);
				$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
				is_payword($_username, $password) or message($L['error_payword']);
				money_add($td['buyer'], $money);
				money_record($td['buyer'], $money, $L['in_site'], 'system', $L['trade_refund'], $L['trade_order_id'].$itemid.$L['trade_refund_by_seller']);
				$db->query("UPDATE {$table} SET status=6,editor='$_username',updatetime=$DT_TIME,refund_reason='$content' WHERE itemid=$itemid");
				//更新商品数据 增加库存
				if($td['mid'] == 16) {
					$db->query("UPDATE {$DT_PRE}mall SET orders=orders-1,sales=sales-$td[number],amount=amount+$td[number] WHERE itemid=$mallid");
				} else {
					$db->query("UPDATE ".get_table($td['mid'])." SET amount=amount+$td[number] WHERE itemid=$mallid");
				}
				message($L['trade_refund_agree_success'], $forward, 3);
			} else {
				$head_title = $L['trade_refund_agree_title'];
			}
		break;
		case 'remind'://买家提醒卖家发货			
			if($td['status'] != 2 || $td['buyer'] != $_username) message($L['trade_msg_deny']);
		break;
		case 'send_goods'://卖家发货
			if(($td['status'] != 2 && $td['status'] != 7) || $td['seller'] != $_username) message($L['trade_msg_deny']);
			if($DT['trade'] && $td['status'] == 2) exit(include DT_ROOT.'/api/trade/'.$DT['trade'].'/update.inc.php');
			if($submit) {
				$send_type = trim(dhtmlspecialchars($send_type));
				if(strlen($send_type) > 2 && strlen($send_no) < 5) message($L['msg_express_no']);
				if(strlen($send_no) > 4 && strlen($send_type) < 3) message($L['msg_express_type']);
				if($send_no && !preg_match("/^[a-z0-9_\-]{4,}$/i", $send_no)) message($L['msg_express_no_error']);
				is_date($send_time) or message($L['msg_express_date_error']);
				$status = $td['status'] == 7 ? 7 : 3;
				$db->query("UPDATE {$table} SET status=$status,updatetime=$DT_TIME,send_type='$send_type',send_no='$send_no',send_time='$send_time' WHERE itemid=$itemid");

				$touser = $td['buyer'];
				$title = lang($L['trade_message_t3'], array($itemid));
				$url = $memberurl.'trade.php?action=order&itemid='.$itemid;
				$content = lang($L['trade_message_c3'], array($myurl, $_username, $timenow, $url));
				$content = ob_template('messager', 'mail');
				send_message($touser, $title, $content);
			
				//send sms
				if($DT['sms'] && $_sms && $touser && isset($sendsms)) {
					$touser = userinfo($touser);
					if($touser['mobile']) {
						$message = lang('sms->ord_send', array($itemid, $send_type, $send_no, $send_time));
						$message = strip_sms($message);
						$word = word_count($message);
						$sms_num = ceil($word/$DT['sms_len']);
						if($sms_num <= $_sms) {
							$sms_code = send_sms($touser['mobile'], $message, $word);
							if(strpos($sms_code, $DT['sms_ok']) !== false) {
								$tmp = explode('/', $sms_code);
								if(is_numeric($tmp[1])) $sms_num = $tmp[1];
								if($sms_num) sms_add($_username, -$sms_num);
								if($sms_num) sms_record($_username, -$sms_num, $_username, $L['trade_sms_send'], $itemid);
							}
						}
					}
				}
				//send sms
				
				//更新商品数据 限货到付款的商品
				if($td['cod']) {
					if($td['mid'] == 16) {
						$db->query("UPDATE {$DT_PRE}mall SET orders=orders+1,sales=sales+$td[number],amount=amount-$td[number] WHERE itemid=$mallid");
					} else {
						$db->query("UPDATE ".get_table($td['mid'])." SET amount=amount-$td[number] WHERE itemid=$mallid");
					}
				}
				message($L['trade_send_success'], $forward, 3);
			} else {
				$head_title = $L['trade_send_title'];
				$send_types = explode('|', trim($MOD['send_types']));
				$send_time = timetodate($DT_TIME, 3);
			}
		break;
		case 'cod_success'://货到付款，确认完成
			if($td['status'] != 7 || !$td['cod'] || !$td['send_time'] || $td['seller'] != $_username) message($L['trade_msg_deny']);
			$db->query("UPDATE {$table} SET status=4,updatetime=$DT_TIME WHERE itemid=$itemid");
			//交易成功
			message($L['trade_success'], $forward, 3);
			
		break;
		case 'add_time'://增加确认收货时间
			if($DT['trade']) exit(include DT_ROOT.'/api/trade/'.$DT['trade'].'/update.inc.php');
			if($td['status'] != 3 || $td['seller'] != $_username) message($L['trade_msg_deny']);
			if($submit) {
				$add_time = intval($add_time);
				$add_time > 0 or message($L['trade_addtime_null']);
				$add_time = $td['add_time'] + $add_time;
				$db->query("UPDATE {$table} SET add_time='$add_time' WHERE itemid=$itemid");
				message($L['trade_addtime_success'], $forward);
			} else {
				$head_title = $L['trade_addtime_title'];
			}
		break;
		case 'receive_goods'://确认收货
			if($DT['trade']) exit(include DT_ROOT.'/api/trade/'.$DT['trade'].'/update.inc.php');
			$gone = $DT_TIME - $td['updatetime'];
			if($td['status'] != 3 || $td['buyer'] != $_username || $gone > ($MOD['trade_day']*86400 + $td['add_time']*3600)) message($L['trade_msg_deny']);
			//交易成功
			$money = $td['amount'] + $td['fee'];
			money_add($td['seller'], $money);
			money_record($td['seller'], $money, $L['in_site'], 'system', $L['trade_record_pay'], $L['trade_order_id'].$itemid);
			//网站服务费
			$G = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE username='".$td['seller']."'");
			$SG = cache_read('group-'.$G['groupid'].'.php');
			if($SG['commission']) {
				$fee = dround($money*$SG['commission']/100);
				if($fee > 0) {
					money_add($td['seller'], -$fee);
					money_record($td['seller'], -$fee, $L['in_site'], 'system', $L['trade_fee'], $L['trade_order_id'].$itemid);	
				}
			}
			$db->query("UPDATE {$table} SET status=4,updatetime=$DT_TIME WHERE itemid=$itemid");
			$touser = $td['seller'];
			$title = lang($L['trade_message_t4'], array($itemid));
			$url = $memberurl.'trade.php?itemid='.$itemid;
			$content = lang($L['trade_message_c4'], array($myurl, $_username, $timenow, $url));
			$content = ob_template('messager', 'mail');
			send_message($touser, $title, $content);

			message($L['trade_success'], $forward, 3);
		break;
		case 'get_pay'://买家确认超时 卖家申请直接付款
			if($DT['trade']) exit(include DT_ROOT.'/api/trade/'.$DT['trade'].'/update.inc.php');
			$gone = $DT_TIME - $td['updatetime'];
			if($td['status'] != 3 || $td['seller'] != $_username || $gone < ($MOD['trade_day']*86400 + $td['add_time']*3600)) message($L['trade_msg_deny']);
			//交易成功
			$money = $td['amount'] + $td['fee'];
			money_add($td['seller'], $money);
			money_record($td['seller'], $money, $L['in_site'], 'system', $L['trade_record_pay'], lang($L['trade_buyer_timeout'], array($itemid)));
			//网站服务费
			$G = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE username='".$td['seller']."'");
			$SG = cache_read('group-'.$G['groupid'].'.php');
			if($SG['commission']) {
				$fee = dround($money*$SG['commission']/100);
				if($fee > 0) {
					money_add($td['seller'], -$fee);
					money_record($td['seller'], -$fee, $L['in_site'], 'system', $L['trade_fee'], $L['trade_order_id'].$itemid);	
				}
			}
			$db->query("UPDATE {$table} SET status=4,updatetime=$DT_TIME WHERE itemid=$itemid");
			message($L['trade_success'], $forward, 3);
		break;
		case 'comment'://交易评价
			if($td['mid'] != 16) message($L['trade_msg_deny_comment']);
			if($submit) {
				$star = intval($star);
				in_array($star, array(1, 2, 3)) or $star = 3;
				$content = dhtmlspecialchars($content);
			}
			if($_username == $td['seller']) {
				if($td['buyer_star']) message($L['trade_msg_comment_again']);
				if($submit) {
					$db->query("UPDATE {$table} SET buyer_star=$star WHERE itemid=$itemid");
					$s = 'b'.$star;
					$db->query("UPDATE {$DT_PRE}mall_comment SET buyer_star=$star,buyer_comment='$content',buyer_ctime=$DT_TIME WHERE itemid=$itemid");
					$db->query("UPDATE {$DT_PRE}mall_stat SET bcomment=bcomment+1,`$s`=`$s`+1 WHERE mallid=$mallid");
					message($L['trade_msg_comment_success'], $forward);
				}
			} else if($_username == $td['buyer']) {
				if($td['seller_star']) message($L['trade_msg_comment_again']);
				if($submit) {
					$db->query("UPDATE {$DT_PRE}mall SET comments=comments+1 WHERE itemid=$mallid");
					$db->query("UPDATE {$table} SET seller_star=$star WHERE itemid=$itemid");
					$s = 's'.$star;
					$db->query("UPDATE {$DT_PRE}mall_comment SET seller_star=$star,seller_comment='$content',seller_ctime=$DT_TIME WHERE itemid=$itemid");
					$db->query("UPDATE {$DT_PRE}mall_stat SET scomment=scomment+1,`$s`=`$s`+1 WHERE mallid=$mallid");
					message($L['trade_msg_comment_success'], $forward);
				}
			}
		break;
		case 'comment_detail'://评价详情
			if($td['mid'] != 16) message($L['trade_msg_deny_comment']);
			$cm = $db->get_one("SELECT * FROM {$DT_PRE}mall_comment WHERE itemid=$itemid");
			if($submit) {
				$content = dhtmlspecialchars($content);
				$content or message($L['trade_msg_empty_explain']);
				if($_username == $td['seller']) {
					if($cm['buyer_reply']) message($L['trade_msg_explain_again']);
					$db->query("UPDATE {$DT_PRE}mall_comment SET buyer_reply='$content',buyer_rtime=$DT_TIME WHERE itemid=$itemid");
				} else {
					if($cm['seller_reply']) message($L['trade_msg_explain_again']);
					$db->query("UPDATE {$DT_PRE}mall_comment SET seller_reply='$content',seller_rtime=$DT_TIME WHERE itemid=$itemid");
				}
				dmsg($L['trade_msg_explain_success'], '?action='.$action.'&step='.$step.'&itemid='.$itemid);
			}
		break;
		case 'close'://关闭交易
			if($_username == $td['seller']) {
				if($td['status'] == 0) {
					$db->query("UPDATE {$table} SET status=9,updatetime=$DT_TIME WHERE itemid=$itemid");
					dmsg($L['trade_close_success'], $forward);
				} else if($td['status'] == 1) {
					$db->query("UPDATE {$table} SET status=9,updatetime=$DT_TIME WHERE itemid=$itemid");
					dmsg($L['trade_close_success'], $forward);
				} else if($td['status'] == 8) {
					$db->query("DELETE FROM {$table} WHERE itemid=$itemid");
					dmsg($L['trade_delete_success'], $forward);
				} else { 
					message($L['trade_msg_deny']);
				}
				message($L['trade_close_success'], $forward);
			} else if($_username == $td['buyer']) {
				if($td['status'] == 0) {
					$db->query("UPDATE {$table} SET status=8,updatetime=$DT_TIME WHERE itemid=$itemid");
					dmsg($L['trade_close_success'], $forward);
				} else if($td['status'] == 1) {
					$db->query("UPDATE {$table} SET status=8,updatetime=$DT_TIME WHERE itemid=$itemid");
					dmsg($L['trade_close_success'], $forward);
				} else if($td['status'] == 9) {
					$db->query("DELETE FROM {$table} WHERE itemid=$itemid");
					dmsg($L['trade_delete_success'], $forward);
				} else {
					message($L['trade_msg_deny']);
				}
			}
		break;
	}
} else if($action == 'bind') {
	$DT['trade'] or message($L['trade_msg_secured_close']);
	$member = $db->get_one("SELECT trade,vtrade FROM {$DT_PRE}member WHERE userid=$_userid");
	if($submit) {
		if($member['trade'] && $member['vtrade']) message($L['trade_msg_bind_edit']);
		if($trade) {
			if($DT['trade'] == 'alipay' && !is_email($trade) && !is_mobile($trade)) message(lang($L['trade_bind_error'], array($DT['trade_nm'])));
			$r = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE trade='$trade' AND vtrade=1");
			if($r) message($L['trade_msg_bind_exists']);
		} else {
			$trade = '';
		}
		$db->query("UPDATE {$DT_PRE}member SET trade='$trade',vtrade=0 WHERE userid=$_userid");
		dmsg($L['trade_msg_bind_success'], '?action=bind');
	} else {
		if(!$member['trade']) $member['vtrade'] = 0;
		$head_title = lang($L['trade_bind_title'], array($DT['trade_nm']));
	}
} else if($action == 'muti') {//批量付款
	$auto = 0;
	$auth = isset($auth) ? dround(decrypt($auth, DT_KEY.'CG')) : '';
	if($auth) {
		$auto = $submit = 1;
		$itemid = explode(',', $auth);
	}
	if($submit) {
		if(!$auto) {
			is_payword($_username, $password) or message($L['error_payword']);
		}
		($itemid && is_array($itemid)) or message($L['trade_msg_muti_choose']);
		$itemids = implode(',', $itemid);
		$condition = "buyer='$_username' AND status=1 AND itemid IN ($itemids)";
		$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY itemid DESC LIMIT 50");
		while($td = $db->fetch_array($result)) {
			$itemid = $td['itemid'];
			$money = $td['amount'] + $td['fee'];
			if($_money < $money) break;
			$seller = userinfo($td['seller']);
			money_add($_username, -$money);
			money_record($_username, -$money, $L['in_site'], 'system', $L['trade_pay_order_title'], $L['trade_order_id'].$itemid);
			$db->query("UPDATE {$table} SET status=2,updatetime=$DT_TIME WHERE itemid=$itemid");
			$_money = $_money - $money;

			$touser = $td['seller'];
			$title = lang($L['trade_message_t2'], array($itemid));
			$url = $memberurl.'trade.php?itemid='.$itemid;
			$content = lang($L['trade_message_c2'], array($myurl, $_username, $timenow, $url));
			$content = ob_template('messager', 'mail');
			send_message($touser, $title, $content);			
			//send sms
			if($DT['sms'] && $_sms && $touser && isset($sendsms)) {
				$touser = userinfo($touser);
				if($touser['mobile']) {
					$message = lang('sms->ord_pay', array($itemid, $money));
					$message = strip_sms($message);
					$word = word_count($message);
					$sms_num = ceil($word/$DT['sms_len']);
					if($sms_num <= $_sms) {
						$sms_code = send_sms($touser['mobile'], $message, $word);
						if(strpos($sms_code, $DT['sms_ok']) !== false) {
							$tmp = explode('/', $sms_code);
							if(is_numeric($tmp[1])) $sms_num = $tmp[1];
							if($sms_num) sms_add($_username, -$sms_num);
							if($sms_num) sms_record($_username, -$sms_num, $_username, $L['trade_sms_pay'], $itemid);
						}
					}
				}
			}
			//send sms
			//更新商品数据
			if($td['mid'] == 16) {
				$db->query("UPDATE {$DT_PRE}mall SET orders=orders+1,sales=sales+$td[number],amount=amount-$td[number] WHERE itemid=$mallid");
			} else {
				$db->query("UPDATE ".get_table($td['mid'])." SET amount=amount-$td[number] WHERE itemid=$mallid");
			}
		}
		dmsg($L['trade_pay_order_success'], '?action=order&nav=2');
	} else {
		$ids = isset($ids) ? explode(',', $ids) : array();
		if($ids) $ids = array_map('intval', $ids);
		$condition = "buyer='$_username' AND status=1";
		if($ids) $condition .= " AND itemid IN (".implode(',', $ids).")";
		$lists = array();
		$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY itemid DESC LIMIT 50");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['linkurl'] = DT_PATH.'api/redirect.php?mid='.$r['mid'].'&itemid='.$r['mallid'];
			$r['dstatus'] = $_status[$r['status']];
			$r['money'] = $r['amount'] + $r['fee'];
			$r['money'] = number_format($r['money'], 2, '.', '');
			$lists[] = $r;
		}
		if(!$lists) {
			if($ids) dmsg($L['trade_pay_order_success'], '?action=order&nav=2');
			message($L['trade_msg_muti_empty'], '?action=order', 5);
		}
		$head_title = $L['trade_muti_title'];
	}
} else if($action == 'express') {//我的快递
	$sfields = $L['express_sfields'];
	$dfields = array('title', 'title', 'send_type ', 'send_no');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$status = isset($status) && isset($dsend_status[$status]) ? intval($status) : '';
	$type = isset($type) ? intval($type) : 0;
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dsend_status, 'status', $L['status'], $status, '', 1, '', 1);
	$condition = "send_no<>''";
	if($type == 2) {
		$condition .= " AND buyer='$_username'";
	} else if($type == 1) {
		$condition .= " AND seller='$_username'";
	} else {
		$condition .= " AND (buyer='$_username' OR seller='$_username')";
	}
	if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
	if($status !== '') $condition .= " AND send_status='$status'";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition");
	$pages = pages($r['num'], $page, $pagesize);		
	$lists = array();
	$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
	while($r = $db->fetch_array($result)) {
		$r['addtime'] = timetodate($r['addtime'], 5);
		$r['updatetime'] = timetodate($r['updatetime'], 5);
		$r['dstatus'] = $_send_status[$r['send_status']];
		$lists[] = $r;
	}
	$head_title = $L['express_title'];
} else if($action == 'order') {
	$sfields = $L['trade_order_sfields'];
	$dfields = array('title', 'title ', 'amount', 'fee', 'fee_name', 'seller', 'send_type', 'send_no', 'note');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$mallid = isset($mallid) ? intval($mallid) : 0;
	$cod = isset($cod) ? intval($cod) : 0;
	$nav = isset($nav) ? intval($nav) : -1;
	(isset($seller) && check_name($seller)) or $seller = '';
	isset($fromtime) or $fromtime = '';
	isset($totime) or $totime = '';
	$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dstatus, 'status', $L['status'], $status, '', 1, '', 1);
	$condition = "buyer='$_username'";
	if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
	if($fromtime) $condition .= " AND addtime>".(strtotime($fromtime.' 00:00:00'));
	if($totime) $condition .= " AND addtime<".(strtotime($totime.' 23:59:59'));
	if($status !== '') $condition .= " AND status='$status'";
	if($itemid) $condition .= " AND itemid='$itemid'";
	if($mallid) $condition .= " AND mallid=$mallid";
	if($seller) $condition .= " AND seller='$seller'";
	if($cod) $condition .= " AND cod=1";
	if(in_array($nav, array(0,1,2,3,5,6))) {
		$condition .= " AND status=$nav";
	} else if($nav == 4) {
		$condition .= " AND status=$nav AND seller_star=0";
	}
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition");
	$pages = pages($r['num'], $page, $pagesize);		
	$lists = array();
	$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
	$amount = $fee = $money = 0;
	while($r = $db->fetch_array($result)) {
		$r['gone'] = $DT_TIME - $r['updatetime'];
		if($r['status'] == 3) {
			if($r['gone'] > ($MOD['trade_day']*86400 + $r['add_time']*3600)) {
				$r['lefttime'] = 0;
			} else {
				$r['lefttime'] = secondstodate($MOD['trade_day']*86400 + $r['add_time']*3600 - $r['gone']);
			}
		}
		$r['par'] = '';		
		if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
		$r['addtime'] = str_replace(' ', '<br/>', timetodate($r['addtime'], 5));
		$r['updatetime'] = str_replace(' ', '<br/>', timetodate($r['updatetime'], 5));
		$r['linkurl'] = DT_PATH.'api/redirect.php?mid='.$r['mid'].'&itemid='.$r['mallid'];
		$r['dstatus'] = $_status[$r['status']];
		$r['money'] = $r['amount'] + $r['fee'];
		$r['money'] = number_format($r['money'], 2, '.', '');
		$amount += $r['amount'];
		$fee += $r['fee'];
		$lists[] = $r;
	}
	$money = $amount + $fee;
	$money = number_format($money, 2, '.', '');
	$head_title = $L['trade_order_title'];
} else {
	$sfields = $L['trade_sfields'];
	$dfields = array('title', 'title ', 'amount', 'fee', 'fee_name', 'buyer', 'buyer_name', 'buyer_address', 'buyer_postcode', 'buyer_mobile', 'buyer_phone', 'send_type', 'send_no', 'note');
	$mallid = isset($mallid) ? intval($mallid) : 0;
	$cod = isset($cod) ? intval($cod) : 0;
	$nav = isset($nav) ? intval($nav) : -1;
	(isset($buyer) && check_name($buyer)) or $buyer = '';
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($fromtime) or $fromtime = '';
	isset($totime) or $totime = '';
	$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dstatus, 'status', $L['status'], $status, '', 1, '', 1);
	$condition = "seller='$_username'";
	if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
	if($fromtime) $condition .= " AND addtime>".(strtotime($fromtime.' 00:00:00'));
	if($totime) $condition .= " AND addtime<".(strtotime($totime.' 23:59:59'));
	if($status !== '') $condition .= " AND status='$status'";
	if($itemid) $condition .= " AND itemid=$itemid";
	if($mallid) $condition .= " AND mallid=$mallid";
	if($buyer) $condition .= " AND buyer='$buyer'";
	if($cod) $condition .= " AND cod=1";
	if(in_array($nav, array(0,1,2,3,5,6))) {
		$condition .= " AND status=$nav";
	} else if($nav == 4) {
		$condition .= " AND status=$nav AND buyer_star=0";
	}
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition");
	$pages = pages($r['num'], $page, $pagesize);
	$orders = $r['num'];
	$lists = array();
	$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
	$amount = $fee = $money = 0;
	while($r = $db->fetch_array($result)) {
		$r['gone'] = $DT_TIME - $r['updatetime'];
		if($r['status'] == 3) {
			if($r['gone'] > ($MOD['trade_day']*86400 + $r['add_time']*3600)) {
				$r['lefttime'] = 0;
			} else {
				$r['lefttime'] = secondstodate($MOD['trade_day']*86400 + $r['add_time']*3600 - $r['gone']);
			}
		}
		$r['par'] = '';
		if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
		$r['addtime'] = str_replace(' ', '<br/>', timetodate($r['addtime'], 5));
		$r['updatetime'] = str_replace(' ', '<br/>', timetodate($r['updatetime'], 5));
		$r['linkurl'] = DT_PATH.'api/redirect.php?mid='.$r['mid'].'&itemid='.$r['mallid'];
		$r['dstatus'] = $_status[$r['status']];
		$r['money'] = $r['amount'] + $r['fee'];
		$r['money'] = number_format($r['money'], 2, '.', '');
		$amount += $r['amount'];
		$fee += $r['fee'];
		$lists[] = $r;
	}
	$money = $amount + $fee;
	$money = number_format($money, 2, '.', '');
	$head_title = $L['trade_title'];
}
include template('trade', $module);
?>