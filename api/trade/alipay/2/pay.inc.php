<?php
defined('IN_DESTOON') or exit('Access Denied');
set_cookie('trade_id', $itemid);
require_once DT_ROOT.'/api/trade/alipay/2/pay/alipay_service.class.php';

/**************************请求参数**************************/

//必填参数//

$out_trade_no		= $itemid;		//请与贵网站订单系统中的唯一订单号匹配
$subject			= $td['title'];	//订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
$body				= $td['note'];	//订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
$price				= $money;	//订单总金额，显示在支付宝收银台里的“应付总额”里

$logistics_fee		= "0.00";				//物流费用，即运费。
$logistics_type		= "EXPRESS";			//物流类型，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
$logistics_payment	= "SELLER_PAY";			//物流支付方式，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）

$quantity			= "1";					//商品数量，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品。

//选填参数//

//买家收货信息（推荐作为必填）
//该功能作用在于买家已经在商户网站的下单流程中填过一次收货信息，而不需要买家在支付宝的付款流程中再次填写收货信息。
//若要使用该功能，请至少保证receive_name、receive_address有值
//收货信息格式请严格按照姓名、地址、邮编、电话、手机的格式填写
$receive_name		= $td['buyer_name'];			//收货人姓名，如：张三
$receive_address	= $td['buyer_address'];			//收货人地址，如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
$receive_zip		= $td['buyer_postcode'];				//收货人邮编，如：123456
$receive_phone		= $td['buyer_phone'];		//收货人电话号码，如：0571-81234567
$receive_mobile		= $td['buyer_phone'];		//收货人手机号码，如：13312341234

//网站商品的展示地址，不允许加?id=123这类自定义参数
$show_url			= DT_PATH.'api/trade/alipay/show.php';

/************************************************************/
//构造要请求的参数数组
$parameter = array(
		"service"		=> "trade_create_by_buyer",
		"payment_type"	=> "1",
		
		"partner"		=> trim($aliapy_config['partner']),
		"_input_charset"=> trim(strtolower($aliapy_config['input_charset'])),
		"seller_email"	=> trim($aliapy_config['seller_email']),
		"return_url"	=> trim($aliapy_config['return_url']),
		"notify_url"	=> trim($aliapy_config['notify_url']),

		"out_trade_no"	=> $out_trade_no,
		"subject"		=> $subject,
		"body"			=> $body,
		"price"			=> $price,
		"quantity"		=> $quantity,

		"buyer_email" => $_trade,//DT ADD
		
		"logistics_fee"		=> $logistics_fee,
		"logistics_type"	=> $logistics_type,
		"logistics_payment"	=> $logistics_payment,

		
		"receive_name"		=> $receive_name,
		"receive_address"	=> $receive_address,
		"receive_zip"		=> $receive_zip,
		"receive_phone"		=> $receive_phone,
		"receive_mobile"	=> $receive_mobile,
		
		"show_url"		=> $show_url
);

//构造标准双接口
$alipayService = new AlipayService($aliapy_config);
$html_text = $alipayService->trade_create_by_buyer($parameter);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo DT_CHARSET;?>">
<meta http-equiv="cache-control" content="no-cache">
<title>Loading...</title>
</head>
<body onload="document.getElementById('alipaysubmit').submit();">
<?php echo $html_text;?>
</body>
</html>