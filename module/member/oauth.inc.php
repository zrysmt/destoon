<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
$MOD['oauth'] or dheader('./');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
switch($action) {
	case 'delete':
		$itemid or message();
		$U = $db->get_one("SELECT * FROM {$DT_PRE}oauth WHERE itemid=$itemid");
		if(!$U || $U['username'] != $_username) message();
		$db->query("DELETE FROM {$DT_PRE}oauth WHERE itemid=$itemid");
		dmsg($L['oauth_quit'], '?action=index');
	break;
	default:
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}oauth WHERE username='$_username'");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['logindate'] = timetodate($r['logintime'], 5);
			$r['nickname'] or $r['nickname'] = '-';
			$lists[$r['site']] = $r;
		}
		$OAUTH = cache_read('oauth.php');
		$head_title = $L['oauth_title'];	
	break;
}
include template('oauth', $module);
?>