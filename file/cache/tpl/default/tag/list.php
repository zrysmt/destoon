<?php defined('IN_DESTOON') or exit('Access Denied');?><ul>
<?php if(is_array($tags)) { foreach($tags as $i => $t) { ?>
<li><?php if($datetype) { ?><span class="f_r px11">&nbsp;<?php echo timetodate($t['addtime'], $datetype);?></span><?php } ?>
<?php if($showcat) { ?><a href="<?php echo $t['caturl'];?>"<?php if($target) { ?> target="<?php echo $target;?>"<?php } ?>
<?php if($class) { ?> class="<?php echo $class;?>"<?php } ?>
>[<?php echo $t['catname'];?>]</a> <?php } ?>
<a href="<?php echo $t['linkurl'];?>"<?php if($target) { ?> target="<?php echo $target;?>"<?php } ?>
<?php if($class) { ?> class="<?php echo $class;?>"<?php } ?>
 title="<?php echo $t['alt'];?>"><?php echo $t['title'];?></a></li>
<?php } } ?>
</ul>
<?php if($showpage && $pages) { ?><div class="pages"><?php echo $pages;?></div><?php } ?>
