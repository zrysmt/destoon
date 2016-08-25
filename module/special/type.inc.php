<?php 
defined('IN_DESTOON') or exit('Access Denied');
$typeid = isset($tid) ? intval($tid) : 0;
$typeid or dheader($MOD['linkurl']);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$type = $db->get_one("SELECT * FROM {$DT_PRE}type WHERE typeid=$typeid");
$type or dheader($MOD['linkurl']);
$item = explode('-', $type['item']);
$item[0] == 'special' or dheader($MOD['linkurl']);
$itemid = $item[1];
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid AND status=3");
$item or dheader($MOD['linkurl']);
extract($item);
$linkurl = $MOD['linkurl'].$linkurl;
include DT_ROOT.'/include/seo.inc.php';
if($seo_title) {
	$seo_title = $seo_title.$seo_delimiter.$seo_sitename;
} else {
	if($MOD['seo_show']) {
		eval("\$seo_title = \"$MOD[seo_show]\";");
	} else {
		$seo_title = $seo_showtitle.$seo_delimiter.$seo_catname.$seo_modulename.$seo_delimiter.$seo_sitename;
	}
}
$head_keywords = $seo_keywords ? $seo_keywords : $keyword;
$head_description = $seo_description ? $seo_description : ($introduce ? $introduce : $title);
$seo_title = $type['typename'].$seo_delimiter.$seo_title;
include template('type', $module);
?>