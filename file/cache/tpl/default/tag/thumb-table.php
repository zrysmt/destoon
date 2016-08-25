<?php defined('IN_DESTOON') or exit('Access Denied');?><table width="100%">
<?php if(is_array($tags)) { foreach($tags as $i => $t) { ?>
<?php if($i%$cols==0) { ?><tr align="center"><?php } ?>
<td width="<?php echo $percent;?>" valign="top"><a href="<?php echo $t['linkurl'];?>"<?php if($target) { ?> target="<?php echo $target;?>"<?php } ?>
><img src="<?php echo $t['thumb'];?>" width="<?php echo $width;?>" height="<?php echo $height;?>" alt="<?php echo $t['alt'];?>"/></a>
<?php if($length) { ?><ul><li><a href="<?php echo $t['linkurl'];?>" title="<?php echo $t['alt'];?>"<?php if($target) { ?> target="<?php echo $target;?>"<?php } ?>
><?php echo $t['title'];?></a></li></ul><?php } ?>
</td>
<?php if($i%$cols==($cols-1)) { ?></tr><?php } ?>
<?php } } ?>
</table>
<?php if($showpage && $pages) { ?><div class="pages"><?php echo $pages;?></div><?php } ?>
