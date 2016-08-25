<?php defined('IN_DESTOON') or exit('Access Denied');?><?php if(is_array($tags)) { foreach($tags as $k => $t) { ?>
<div class="list" id="item_<?php echo $t['itemid'];?>">
<table>
<tr align="center">
<td width="100"><div><a href="<?php echo $t['linkurl'];?>" target="_blank"><img src="<?php echo imgurl($t['thumb']);?>" width="80" height="80" alt="<?php echo $t['alt'];?>" onmouseover="img_tip(this, this.src);" onmouseout="img_tip(this, '');"/></a></div></td>
<td width="10"> </td>
<td align="left">
<ul>
<li><?php if($t['vip']) { ?><span class="f_r"><img src="<?php echo DT_SKIN;?>image/vip_<?php echo $t['vip'];?>.gif" alt="<?php echo VIP;?>" title="<?php echo VIP;?>:<?php echo $t['vip'];?>级"/></span><?php } ?>
<a href="<?php echo $t['linkurl'];?>" target="_blank"><strong class="px14"><?php echo $t['title'];?></strong></a></li>
<li class="f_gray"><?php echo $t['introduce'];?></li>
<li><span class="f_r px11"><?php echo timetodate($t['edittime'], $datetype);?>&nbsp;&nbsp;</span>
<span class="f_gray">&nbsp;&nbsp;<?php if(!$t['username']) { ?> [未注册]<?php } ?>
</span>
</li>
</ul>
</td>
<td width="10"> </td>
<td width="100" class="f_gray">[<?php echo area_pos($t['areaid'], '', 1);?>]</td>
</tr>
</table>
</div>
<?php } } ?>
<?php if($showpage && $pages) { ?><div class="pages"><?php echo $pages;?></div><?php } ?>
