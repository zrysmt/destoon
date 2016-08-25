<?php
defined('DT_ADMIN') or exit('Access Denied');
file_copy(DT_ROOT.'/api/ajax.php', DT_ROOT.'/'.$dir.'/ajax.php');
install_file('index', $dir, 1);
install_file('list', $dir, 1);
install_file('show', $dir, 1);
install_file('search', $dir, 1);
install_file('compare', $dir, 0);
install_file('buy', $dir, 1);
?>