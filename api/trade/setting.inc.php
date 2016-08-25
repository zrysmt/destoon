<?php
defined('IN_DESTOON') or exit('Access Denied');
?>
<tr style="display:<?php echo $trade ? '' : 'none';?>;">
<td class="tl">支付宝担保交易</td>
<td>
<input type="radio" name="setting[trade]" value="alipay"  <?php if($trade){ ?>checked <?php } ?> onclick="Ds('dtrade');"/> 开启&nbsp;&nbsp;
<input type="radio" name="setting[trade]" value=""  <?php if(!$trade){ ?>checked <?php } ?> onclick="Dh('dtrade');"/> 关闭&nbsp;&nbsp;&nbsp;&nbsp;
<img src="<?php echo DT_PATH;?>api/trade/alipay/ico.gif" align="absmiddle"/> <a href="<?php echo DT_PATH;?>api/redirect.php?url=https://b.alipay.com/order/productDetail.htm?productId=2011042200323187" target="_blank" class="t">[申请帐号]</a>
</td>
</tr>
<tbody id="dtrade" style="display:<?php if(!$trade) echo 'none';?>">
<tr>
<td class="tl">显示名称</td>
<td><input name="setting[trade_nm]" type="text" value="<?php echo $trade_nm;?>" size="30"/></td> 
</tr>
<tr>
<td class="tl">官方网站</td>
<td><input name="setting[trade_hm]" type="text" value="<?php echo $trade_hm;?>" size="30"/></td> 
</tr>
<tr>
<td class="tl">商户ID</td>
<td><input name="setting[trade_id]" type="text" value="<?php echo $trade_id;?>" size="30"/></td> 
</tr>
<tr>
<td class="tl">安全密钥</td>
<td><input name="setting[trade_pw]" type="text" id="trade_pw" size="41" value="<?php echo $trade_pw;?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/></td>
</tr>
<tr>
<td class="tl">商户帐号</td>
<td><input name="setting[trade_ac]" type="text" value="<?php echo $trade_ac;?>" size="30"/></td> 
</tr>
<tr>
<td class="tl">接口类型</td>
<td>
<select name="setting[trade_tp]">
<option value="0" <?php if($trade_tp == 0) echo 'selected';?>>平台商担保交易</option>
<option value="1" <?php if($trade_tp == 1) echo 'selected';?>>平台商双功能</option>
</select> <?php tips('建议申请 平台商担保交易');?>
</td>
</tr>
<tr>
<td class="tl">接收服务器通知文件名</td>
<td><input type="text" size="30" name="setting[trade_nu]" value="<?php echo $trade_nu;?>"/> <?php tips('默认为notify.php 保存于 api/trade/alipay/1/notify.php(平台商担保交易)和api/trade/alipay/2/notify.php(平台商双功能)<br/>建议你修改此文件名，然后在此填写新文件名，以防受到骚扰');?></td>
</tr>
</tbody>