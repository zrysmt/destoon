<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<div class="tt">聊天记录</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="60">头像</th>
<th width="160">会员</th>
<th width="160">时间</th>
<th>内容</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><img src="<?php echo useravatar($v['name']);?>" style="padding:5px;" width="48" height="48"/></td>
<td>
<?php if(check_name($v['name'])) { ?>
<a href="javascript:_user('<?php echo $v['name'];?>')"><?php echo $v['name'];?></a>
<?php } else { ?>
<a href="javascript:_ip('<?php echo $v['name'];?>')" title="IP:<?php echo $v['name'];?> - <?php echo ip2area($v['name']);?>"><span class="f_gray">游客</span></a>
<?php } ?>
</td>
<td class="px11"><?php echo $v['date'];?></td>
<td style="padding:10px;text-align:left;line-height:180%;"><?php echo $v['word'];?></td>
</tr>
<?php }?>
</table>
<?php include tpl('footer');?>