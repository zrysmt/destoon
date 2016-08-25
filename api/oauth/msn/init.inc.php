<?php
defined('IN_DESTOON') or exit('Access Denied');
$OAUTH = cache_read('oauth.php');
$site = 'msn';
$OAUTH[$site]['enable'] or dheader($MODULE[2]['linkurl'].$DT['file_login']);
$session = new dsession();

// Application Specific Globals
define('WRAP_CLIENT_ID', $OAUTH[$site]['id']);
define('WRAP_CLIENT_SECRET', $OAUTH[$site]['key']);
define('WRAP_CALLBACK', DT_PATH.'api/oauth/'.$site.'/callback.php');

// Live URLs required for making requests.
define('WRAP_CONSENT_URL', 'https://login.live.com/oauth20_authorize.srf');
define('WRAP_ACCESS_URL', 'https://login.live.com/oauth20_token.srf');
?>