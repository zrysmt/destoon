<?php defined('IN_DESTOON') or exit('Access Denied');?><?php if($tags) { ?>
<div id="slide_<?php echo $tags['0']['itemid'];?><?php echo $moduleid;?>" class="slide" style="width:<?php echo $width;?>px;height:<?php echo $height;?>px;">
<?php if(is_array($tags)) { foreach($tags as $i => $t) { ?>
<a href="<?php echo $t['linkurl'];?>"<?php if($target) { ?> target="_blank"<?php } ?>
><img src="<?php echo $t['thumb'];?>" width="<?php echo $width;?>" height="<?php echo $height;?>" alt="<?php echo $t['alt'];?>"/></a>
<?php } } ?>
</div>
<?php echo load('slide.js');?>
<script type="text/javascript">new dslide('slide_<?php echo $tags['0']['itemid'];?><?php echo $moduleid;?>');</script>
<?php } else { ?>
<div class="slide" style="width:<?php echo $width;?>px;height:<?php echo $height;?>px;"></div>
<?php } ?>
