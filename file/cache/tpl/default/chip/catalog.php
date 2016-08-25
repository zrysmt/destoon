<?php defined('IN_DESTOON') or exit('Access Denied');?><?php if(!$DT['page_bigcat']) { ?>
<table width="100%" cellpadding="0" cellspacing="0">
<?php $child = get_maincat(0, $mid, 1);?>
<?php if(is_array($child)) { foreach($child as $i => $c) { ?>
<?php if($i%2==0) { ?><tr<?php if($i%4==2) { ?> bgcolor="#F9F9F9"<?php } ?>
><?php } ?>
<td valign="top" width="50%" class="catalog_tds">
<p>
<a href="<?php echo $MODULE[$mid]['linkurl'];?><?php echo $c['linkurl'];?>" class="px15"><strong><?php echo set_style($c['catname'], $c['style']);?></strong></a>
<?php if($c['child']) { ?>
<?php $sub = get_maincat($c['catid'], $mid, 2);?>
<?php if(is_array($sub)) { foreach($sub as $j => $s) { ?><?php if($j < 5) { ?> <a href="<?php echo $MODULE[$mid]['linkurl'];?><?php echo $s['linkurl'];?>"><strong><?php echo set_style($s['catname'], $s['style']);?></strong></a><?php } ?>
<?php } } ?>
<?php } ?>
</p>
<?php if($c['child']) { ?>
<?php $sub = get_maincat($c['catid'], $mid, 1);?>
<ul>
<?php if(is_array($sub)) { foreach($sub as $j => $s) { ?>
<li><a href="<?php echo $MODULE[$mid]['linkurl'];?><?php echo $s['linkurl'];?>" class="g"><?php echo set_style($s['catname'], $s['style']);?></a></li>
<?php } } ?>
<?php if($j>8) { ?><li><a href="<?php echo $MODULE[$mid]['linkurl'];?><?php echo $c['linkurl'];?>" class="g">更多</a></li><?php } ?>
</ul>
<div class="c_b"></div>
<?php } ?>
</td>
<?php if($i%2==1) { ?></tr><?php } ?>
<?php } } ?>
</table>
<?php } else { ?>
<?php $BIG = explode('|', $DT['page_bigcat']);?>
<?php if(is_array($BIG)) { foreach($BIG as $kkk => $vvv) { ?>
<?php if($kkk) { ?><div class="b5 c_b"></div><?php } ?>
<div class="catalog_on_1" onmouseover="this.className='catalog_on_2';" onmouseout="this.className='catalog_on_1';">
<table width="100%" cellspacing="2" cellspacing="2">
<tr>
<td class="catalog_tdl"><?php echo $vvv;?></td>
<td class="catalog_tdr">
<table width="100%" cellpadding="0" cellspacing="0">
<?php $child = get_maincat(0, $mid, $kkk+1);?>
<?php if(is_array($child)) { foreach($child as $i => $c) { ?>
<?php if($i%2==0) { ?><tr><?php } ?>
<td valign="top" width="50%" class="catalog_tds">
<p>
<a href="<?php echo $MODULE[$mid]['linkurl'];?><?php echo $c['linkurl'];?>" class="px15"><strong><?php echo set_style($c['catname'], $c['style']);?></strong></a>
<?php if($c['child']) { ?>
<?php $sub = get_maincat($c['catid'], $mid, 2);?>
<?php if(is_array($sub)) { foreach($sub as $j => $s) { ?><?php if($j < 5) { ?> <a href="<?php echo $MODULE[$mid]['linkurl'];?><?php echo $s['linkurl'];?>"><strong><?php echo set_style($s['catname'], $s['style']);?></strong></a><?php } ?>
<?php } } ?>
<?php } ?>
</p>
<?php if($c['child']) { ?>
<?php $sub = get_maincat($c['catid'], $mid, 1);?>
<ul>
<?php if(is_array($sub)) { foreach($sub as $j => $s) { ?>
<li><a href="<?php echo $MODULE[$mid]['linkurl'];?><?php echo $s['linkurl'];?>" class="g"><?php echo set_style($s['catname'], $s['style']);?></a></li>
<?php } } ?>
<?php if($j>8) { ?><li><a href="<?php echo $MODULE[$mid]['linkurl'];?><?php echo $c['linkurl'];?>" class="g">更多</a></li><?php } ?>
</ul>
<div class="c_b"></div>
<?php } ?>
</td>
<?php if($i%2==1) { ?></tr><?php } ?>
<?php } } ?>
</table>
</td>
</tr>
</table>
</div>
<?php } } ?>
<?php } ?>
