<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
$itemid or dheader($MOD['linkurl']);
login();
if(!check_group($_groupid, $MOD['group_apply'])) include load('403.inc');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
include load('misc.lang');
$item = $db->get_one("SELECT * FROM {$DT_PRE}job WHERE itemid=$itemid");
$item or message($L['not_exists']);
if($item['totime'] && $DT_TIME > $item['totime']) message($L['has_expired']);
$item['status'] == 3 or message($L['not_exists']);
$item['username'] or message($L['com_not_member']);
$_username != $item['username'] or message($L['send_self']);

$app = $db->get_one("SELECT * FROM {$DT_PRE}job_apply WHERE jobid=$itemid AND apply_username='$_username'");
if($app) message($L['apply_again']);

$linkurl = $MOD['linkurl'].$item['linkurl'];
if($submit) {
	$resumeid = intval($resumeid);
	$resumeid or dheader($linkurl);
	$resume = $db->get_one("SELECT * FROM {$DT_PRE}resume WHERE itemid=$resumeid AND status=3 AND open=3 AND username='$_username'");
	$resume or message($L['not_resume'], $linkurl);
	$db->query("INSERT INTO {$DT_PRE}job_apply (jobid,resumeid,job_username,apply_username,applytime,status) VALUES ('$itemid','$resumeid','$item[username]','$_username','$DT_TIME','1')");
	$db->query("UPDATE {$DT_PRE}job SET apply=apply+1 WHERE itemid=$itemid");
	$resumeurl = $MOD['linkurl'].$resume['linkurl'];
	send_message($item['username'], lang($L['apply_msg_title'], array(dsubstr($item['title'], 20, '...'))), lang($L['apply_msg_content'], array($resumeurl)));
	message($L['apply_success'], $linkurl);
} else {
	$lists = array();
	$result = $db->query("SELECT * FROM {$DT_PRE}resume WHERE username='$_username' AND status=3 AND open=3 ORDER BY edittime DESC");
	while($r = $db->fetch_array($result)) {
		$r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
		$lists[] = $r;
	}
	if($lists) {
		$head_title = $L['apply_title'].$DT['seo_delimiter'].$item['title'].$DT['seo_delimiter'].$MOD['name'];
		include template('apply', $module);
	} else {
		message($L['make_resume'], $MODULE[2]['linkurl'].$DT['file_my'].'?resume=1&action=add&mid='.$moduleid);
	}
}
?>