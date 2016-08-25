<?php
defined('DT_ADMIN') or exit('Access Denied');
$db->query("DROP TABLE IF EXISTS `".$DT_PRE.$module."`");
$db->query("DROP TABLE IF EXISTS `".$DT_PRE.$module."_data`");
$db->query("DROP TABLE IF EXISTS `".$DT_PRE.$module."_apply`");
$db->query("DROP TABLE IF EXISTS `".$DT_PRE.$module."_talent`");
$db->query("DROP TABLE IF EXISTS `".$DT_PRE."resume`");
$db->query("DROP TABLE IF EXISTS `".$DT_PRE."resume_data`");
?>