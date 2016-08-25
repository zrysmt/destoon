<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$head_title = lang($L['faq_title'], array($MOD['name']));
include template('faq', $module);
?>