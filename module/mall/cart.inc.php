<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
if(is_array($itemid) && !$_userid) {
	$DT_URL = $MOD['linkurl'].'cart.php?action=add';
	foreach($itemid as $id) {
		$DT_URL .= '&itemid[]='.$id;
	}
}
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/module/'.$module.'/cart.class.php';
include load('misc.lang');
$do = new cart();
$do->max = intval($MOD['max_cart']);
$cart = $do->get();
if($itemid) $action = 'add';
switch($action) {
	case 'add':
		$s1 = isset($s1) ? intval($s1) : 0;
		$s2 = isset($s2) ? intval($s2) : 0;
		$s3 = isset($s3) ? intval($s3) : 0;
		$a = isset($a) ? intval($a) : 1;
		$code = $do->add($cart, $itemid, $s1, $s2, $s3, $a);
		$id = is_array($itemid) ? end($itemid) : $itemid;
		dheader('?action=show&id='.$id.'&code='.$code);
	break;
	case 'show':
		$code = isset($code) ? intval($code) : 0;
		$id = isset($id) ? intval($id) : 0;
	break;
	case 'clear':
		$do->clear();
		dheader('?rand='.$DT_TIME);
	break;
	case 'delete':
		isset($key) or $key = '';
		if(isset($cart[$key])) {
			unset($cart[$key]);
			$do->set($cart);
		}
		if(isset($ajax)) exit('1');
		dheader('?rand='.$DT_TIME);
	break;
	default:
		$lists = $do->get_list($cart);
	break;
}
$head_title = $L['cart_title'].$DT['seo_delimiter'].$MOD['name'];
include template('cart', $module);
?>