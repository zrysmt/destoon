<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<div class="tt"><?php echo $menus[$menuid][0];?></div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr class="on">
<td>
<input type="radio" name="fromtype" value="areaid" id="f_2"/><label for="f_2">从地区ID</label>&nbsp;&nbsp;
<input type="radio" name="fromtype" value="userid" <?php echo $userid ? 'checked' : '';?> id="f_3"/><label for="f_3">从会员ID</label>
</td>
<td></td>
<td>&nbsp;目标地区</td>
</tr>
<tr>
<td width="250" align="center" title="多个ID用,分开 结尾和开头不能有,">
<textarea style="height:300px;width:250px;" name="fromids"><?php echo $userid;?></textarea>
</td>
<td width="60" align="center"><strong>&rarr;</strong></td>
<td><?php echo ajax_area_select('toareaid', '', 0, 'size="2" style="height:300px;width:150px;"');?></td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td> </td>
<td><input type="submit" name="submit" value=" 移 动 " class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" name="reset" value=" 重 置 " class="btn"/></td>
</tr>
</table>
</div>
</form>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>