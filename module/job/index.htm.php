<?php 
defined('IN_DESTOON') or exit('Access Denied');
require_once DT_ROOT.'/include/post.func.php';
$fileroot = DT_ROOT.'/'.$MOD['moduledir'].'/';
$filename = $fileroot.$DT['index'].'.'.$DT['file_ext'];
if(!$MOD['index_html']) {
	if(is_file($filename)) unlink($filename);
	return false;
}
$maincat = get_maincat(0, $moduleid);
$seo_file = 'index';
include DT_ROOT.'/include/seo.inc.php';
$destoon_task = "moduleid=$moduleid&html=index";
if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].mobileurl($moduleid, 0, 0, $page);
ob_start();
include template('index', $module);
$data = ob_get_contents();
ob_clean();
file_put($filename, $data);
return true;
?>