<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(!$itemid) return false;
$item = $db->get_one("SELECT * FROM {$DT_PRE}webpage WHERE itemid=$itemid AND islink=0");
if(!$item) return false;
$_item = $item['item'];
$cityid = $item['areaid'];
unset($item['item']);
extract($item);
$head_title = $seo_title ? $seo_title : $title;
$head_keywords = $seo_keywords;
$head_description = $seo_description;
$destoon_task = "moduleid=$moduleid&html=webpage&itemid=$itemid";
if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].'about.php?itemid='.$itemid;
$template = $item['template'] ? $item['template'] : 'webpage';
ob_start();
include template($template, $module);
$data = ob_get_contents();
ob_clean();
file_put(DT_ROOT.'/'.$linkurl, $data);
return true;
?>