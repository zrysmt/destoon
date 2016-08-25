<?php
defined('IN_DESTOON') or exit('Access Denied');
dheader(DT_PATH.'api/pay/weixin/qrcode.php?auth='.encrypt($orderid.'|'.$charge_title.'|'.$DT_IP, DT_KEY.'QRPAY'));
?>