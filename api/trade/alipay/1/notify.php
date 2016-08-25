<?php
$_SERVER['REQUEST_URI'] = '';
$_DPOST = $_POST;
$_DGET = $_GET;
require '../../../../common.inc.php';
($DT['trade'] && $DT['trade_id'] && $DT['trade_pw'] && $DT['trade_ac']) or exit('fail');
$_POST = $_DPOST;
$_GET = $_DGET;
require '../config.inc.php';
$api == 1 or exit('fail');
#log_write(array($_SERVER, $_POST, $_GET), 'ali'.$api.'n', 1);
if($_POST['seller_email']) $aliapy_config['seller_email'] = $_POST['seller_email'];
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.2
 * 日期：2011-03-25
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 
 * WAIT_BUYER_PAY(表示买家已在支付宝交易管理中产生了交易记录，但没有付款);
 * WAIT_SELLER_SEND_GOODS(表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货);
 * WAIT_BUYER_CONFIRM_GOODS(表示卖家已经发了货，但买家还没有做确认收货的操作);
 * TRADE_FINISHED(表示买家已经确认收货，这笔交易完成);
 */

//require_once("alipay.config.php");
require_once DT_ROOT.'/api/trade/alipay/1/pay/alipay_notify.class.php';

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($aliapy_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代
	
	//――请根据您的业务逻辑来编写程序（以下代码仅作参考）――
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
    $out_trade_no	= intval($_POST['out_trade_no']);	    //获取订单号
    $trade_no		= $_POST['trade_no'];	    	//获取支付宝交易号
    $total_fee		= dround($_POST['price']);				//获取总价格

	$itemid = $out_trade_no;
	$td = $db->get_one("SELECT * FROM {$DT_PRE}mall_order WHERE itemid=$itemid");
	$money = dround($td['amount'] + $td['fee']);
	if(!$td || $total_fee != $money) exit("fail");
	$seller = $td['seller'];
	$seller_email = $_POST['seller_email'];
	$buyer = $td['buyer'];
	$buyer_email = $_POST['buyer_email'];
	$mallid = $td['mallid'];
	$timenow = timetodate($DT_TIME, 3);
	$memberurl = $MODULE[2]['linkurl'];	
	include load('member.lang');

	if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
	//该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
	
		//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
			
        echo "success";		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
	else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
	//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
	
		//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
		if($td['status'] == 1) {
			$db->query("UPDATE {$DT_PRE}mall_order SET trade_no='$trade_no',status=2,updatetime=$DT_TIME WHERE itemid=$itemid");
			$db->query("UPDATE {$DT_PRE}member SET trade='$seller_email',vtrade=1 WHERE username='$seller' AND vtrade=0");
			$db->query("UPDATE {$DT_PRE}member SET trade='$buyer_email',vtrade=1 WHERE username='$buyer' AND vtrade=0");
			//更新商品数据
			if($td['mid'] == 16) {
				$db->query("UPDATE {$DT_PRE}mall SET orders=orders+1,sales=sales+$td[number],amount=amount-$td[number] WHERE itemid=$mallid");
			} else {
				$db->query("UPDATE ".get_table($td['mid'])." SET amount=amount-$td[number] WHERE itemid=$mallid");
			}

			$myurl = userurl($td['buyer']);
			$_username = $td['seller'];
			//send message
			$touser = $td['seller'];
			$title = lang($L['trade_message_t2'], array($itemid));
			$url = $memberurl.'trade.php?itemid='.$itemid;
			$content = lang($L['trade_message_c2'], array($myurl, $_username, $timenow, $url));
			$content = ob_template('messager', 'mail');
			send_message($touser, $title, $content);

			echo "success";
		}
			
        		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
	else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
	//该判断表示卖家已经发了货，但买家还没有做确认收货的操作
	
		//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序

		if(isset($_POST['refund_status'])) {
			if($_POST['refund_status'] == 'WAIT_SELLER_AGREE' && $td['status'] == 3) {//买家申请退款 等待卖家同意
				$db->query("UPDATE {$DT_PRE}mall_order SET status=5,updatetime=$DT_TIME WHERE itemid=$itemid");
				exit('success');
			}
		}

		if($td['status'] == 2) {

			$db->query("UPDATE {$DT_PRE}mall_order SET status=3,updatetime=$DT_TIME WHERE itemid=$itemid");

			$myurl = userurl($td['seller']);
			$_username = $td['buyer'];
			//send message
			$touser = $td['buyer'];
			$title = lang($L['trade_message_t3'], array($itemid));
			$url = $memberurl.'trade.php?action=order&itemid='.$itemid;
			$content = lang($L['trade_message_c3'], array($myurl, $_username, $timenow, $url));
			$content = ob_template('messager', 'mail');
			send_message($touser, $title, $content);
			echo "success";
		}
			
        //请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
	else if($_POST['trade_status'] == 'TRADE_FINISHED') {
	//该判断表示买家已经确认收货，这笔交易完成
	
		//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
		if($td['status'] == 3) {
			$db->query("UPDATE {$DT_PRE}mall_order SET status=4,updatetime=$DT_TIME WHERE itemid=$itemid");
			$myurl = userurl($td['buyer']);
			$_username = $td['seller'];			
			//send message
			$touser = $td['seller'];
			$title = lang($L['trade_message_t4'], array($itemid));
			$url = $memberurl.'trade.php?itemid='.$itemid;
			$content = lang($L['trade_message_c4'], array($myurl, $_username, $timenow, $url));
			$content = ob_template('messager', 'mail');
			send_message($touser, $title, $content);

			echo "success";
		}
			
        		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
	} else if($_POST['trade_status'] == 'TRADE_CLOSED') {
		if(isset($_POST['refund_status'])) {
			if($_POST['refund_status'] == 'REFUND_SUCCESS' && $td['status'] == 5) {//退款成功
				$db->query("UPDATE {$DT_PRE}mall_order SET status=6,updatetime=$DT_TIME WHERE itemid=$itemid");
				exit('success');
			}
			if($_POST['refund_status'] == 'REFUND_CLOSED' && $td['status'] == 5) {//退款关闭
				$db->query("UPDATE {$DT_PRE}mall_order SET status=7,updatetime=$DT_TIME WHERE itemid=$itemid");
				exit('success');
			}
		}
    } else {
		//其他状态判断
        echo "success";

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult ("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }

	//――请根据您的业务逻辑来编写程序（以上代码仅作参考）――
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //AlipayFunction.logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>