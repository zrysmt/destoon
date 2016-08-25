<?php
defined('DT_ADMIN') or exit('Access Denied');
?>
<div class="menu" onselectstart="return false" id="destoon_menu">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td valign="bottom">
<table cellpadding="0" cellspacing="0">
<tr>
<td width="10">&nbsp;</td>
<?php echo $menu;?>
</tr>
</table>
</td>
<td width="110"><div><img src="admin/image/spacer.gif" width="40" height="24" title="刷新" onclick="window.location.reload();" style="cursor:pointer;" alt=""/><img src="admin/image/spacer.gif" width="20" height="24" title="后退" onclick="history.back(-1);" style="cursor:pointer;" alt=""/><img src="admin/image/spacer.gif" width="20" height="24" title="前进" onclick="history.go(1);" style="cursor:pointer;" alt=""/><img src="admin/image/spacer.gif" width="20" height="24" title="帮助" onclick="Go('?file=cloud&action=doc&mfa=<?php echo $module;?>-<?php echo $file?>-<?php echo $action?>');" style="cursor:help;" alt=""/></div></td>
</tr>
</table>
</div>