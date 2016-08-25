<?php
defined('IN_DESTOON') or exit('Access Denied');
/* *
 * 功能：确认发货接口接入页
 * 版本：3.2
 * 修改日期：2011-03-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 
 * 确认发货没有服务器异步通知页面（notify_url）与页面跳转同步通知页面（return_url），
 * 发货操作后，该笔交易的状态发生了变更，支付宝会主动发送通知给商户网站，而商户网站在担保交易或双功能的接口中的服务器异步通知页面（notify_url）
 * 该发货接口仅针对担保交易接口、双功能接口中的担保交易支付里涉及到需要卖家做发货的操作

 * 各家快递公司都属于EXPRESS（快递）的范畴
 */


//require_once("alipay.config.php");
require_once DT_ROOT.'/api/trade/alipay/1/send/alipay_service.class.php';

/**************************请求参数**************************/

//必填参数//

//支付宝交易号。它是登陆支付宝网站在交易管理中查询得到，一般以8位日期开头的纯数字（如：20100419XXXXXXXXXX） 
$trade_no		= $td['trade_no'];

//物流公司名称
$logistics_name	= $send_type;

//物流发货单号
$invoice_no		= $send_no;

//物流发货时的运输类型，三个值可选：POST（平邮）、EXPRESS（快递）、EMS（EMS）
$transport_type	= 'EXPRESS';

/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
		"service"			=> "send_goods_confirm_by_platform",
		"partner"			=> trim($aliapy_config['partner']),
		"_input_charset"	=> trim(strtolower($aliapy_config['input_charset'])),
		"trade_no"			=> $trade_no,
		"logistics_name"	=> $logistics_name,
		"invoice_no"		=> $invoice_no,
		"transport_type"	=> $transport_type
);

//构造确认发货接口
$alipayService = new AlipayService($aliapy_config);
$doc = $alipayService->send_goods_confirm_by_platform($parameter);

//请在这里加上商户的业务逻辑程序代码

//――请根据您的业务逻辑来编写程序（以下代码仅作参考）――

//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

//解析XML
$response = '';
if( ! empty($doc->getElementsByTagName( "response" )->item(0)->nodeValue) ) {
	$response= $doc->getElementsByTagName( "response" )->item(0)->nodeValue;
}

echo $response;

//――请根据您的业务逻辑来编写程序（以上代码仅作参考）――

?>