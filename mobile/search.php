<?php
require 'common.inc.php';
$club_post = (substr($action, 0, 4) == 'post' && isset($MODULE[18])) ? 1 : 0;
if(isset($_POST['ok']) && isset($wd) && $wd) {
	if(in_array($action, array('message'))) {
		$url = $action.'.php?';
	} else if($club_post) {
		$catid = intval(substr($action, 4));
		$url = 'index.php?moduleid=18&catid='.$catid.'&';
	} else {
		$moduleid = intval(str_replace('mod', '', $action));
		$url = 'index.php?moduleid='.$moduleid.'&';
	}
	$wd = input_trim($wd);
	$wd = convert($wd, 'UTF-8', DT_CHARSET);
	dheader($url.'kw='.encrypt($wd, DT_KEY.'KW'));
}
$head_title = $L['search_title'].$DT['seo_delimiter'].$head_title;
$foot = 'channel';
include template('search', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>