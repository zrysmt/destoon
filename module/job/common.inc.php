<?php 
defined('IN_DESTOON') or exit('Access Denied');
define('MD_ROOT', DT_ROOT.'/module/'.$module);
require DT_ROOT.'/include/module.func.php';
require MD_ROOT.'/global.func.php';
$table = $DT_PRE.$module;
$table_data = $DT_PRE.$module.'_data';
$CATEGORY = cache_read('category-'.$moduleid.'.php');
$AREA = cache_read('area.php');
$TYPE = explode('|', trim($MOD['type']));
$GENDER = explode('|', trim($MOD['gender']));
$MARRIAGE = explode('|', trim($MOD['marriage']));
$EDUCATION = explode('|', trim($MOD['education']));
$SITUATION = explode('|', trim($MOD['situation']));
?>