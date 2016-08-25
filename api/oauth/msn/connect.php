<?php
require '../../../common.inc.php';
require 'init.inc.php';
dheader(WRAP_CONSENT_URL.'?client_id='.WRAP_CLIENT_ID.'&scope=wl.signin%20wl.basic&response_type=code&redirect_uri='.urlencode(WRAP_CALLBACK));
?>