<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="tt">后台搜索</div>
<form action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>
&nbsp;<input type="text" size="30" name="kw" value="<?php echo $kw;?>" title="关键词" x-webkit-speech speech/>
&nbsp;<input type="submit" name="submit" value="开始搜索" class="btn"/>
&nbsp;<input type="button" value="重新搜索" class="btn" onclick="Go('?file=<?php echo $file;?>');"/>
&nbsp;&nbsp;<span class="f_gray">输入关键词，例如“会员整合”、“支付接口”、“手机短信”</span>
</td>
</tr>
</table>
</form>
<?php if($kw) { ?>
<div class="tt">搜索结果</div>
<table cellpadding="2" cellspacing="1" class="tb">
<?php if($lists || $files) { ?>
<tr>
<th>名称</th>
<th width="60">查看</th>
</tr>

<?php if($files) { ?>
<?php foreach($files as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td align="left" height="22">&nbsp;&nbsp;<img src="admin/image/folder.gif" align="absmiddle"/> <a href="<?php echo $v[1];?>&search=1#high"><?php echo $v[0];?></a></td>
<td><a href="<?php echo $v[1];?>&search=1#high" target="_blank"><img src="admin/image/view.png" width="16" height="16"/></a></td>
</tr>
<?php }?>
<?php }?>

<?php if($lists) { ?>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td align="left" height="22">&nbsp;&nbsp;<img src="admin/image/folder.gif" align="absmiddle"/> <a href="?moduleid=<?php echo $k;?>&file=setting&kw=<?php echo $ukw;?>&search=1#high"><?php echo $v['name'];?></a></td>
<td><a href="?moduleid=<?php echo $k;?>&file=setting&kw=<?php echo urlencode($kw);?>&search=1#high" target="_blank"><img src="admin/image/view.png" width="16" height="16"/></a></td>
</tr>
<?php }?>
<?php }?>

<?php } else { ?>
<tr>
<td class="f_blue" height="40">&nbsp;- 未找到到相关设置，请调整关键词再试&nbsp;&nbsp;&nbsp;&nbsp;<a href="?file=<?php echo $file;?>" class="t">[重新搜索]</a></td>
</tr>
<?php } ?>
</table>
<?php } ?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>