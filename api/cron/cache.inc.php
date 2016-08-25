<?php
defined('IN_DESTOON') or exit('Access Denied');
if($CFG['cache'] == 'file') $dc->expire();
?>