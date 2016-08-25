/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
document.write('<style type="text/css">');
document.write('.color_div_o {width:16px;height:16px;padding:4px 0 0 4px;background:#B6BDD2;cursor:crosshair;}');
document.write('.color_div_t {width:16px;height:16px;padding:4px 0 0 4px;background:#F1F2F3;}');
document.write('.color_div {border:#808080 1px solid;width:10px;height:10px;line-height:10px;font-size:1px;}');
document.write('</style>');
var color_id = 1; var color_bk = color_htm = '';
color_htm += '<table cellpadding="0" cellspacing="0" bgcolor="#2875B9" width="160">';
color_htm += '<tr><td width="2" height="20"> </td>';
color_htm += '<td><input type="text" style="width:60px;height:12px;border:#A0A0A0 1px solid;" value="" id="color_viewview" onblur="color_select(this.value);" onkeyup="color_view(this.value);" ondblclick="this.value=\'\';"/></td>';
color_htm += '<td title="Destoon Color Selector Powered By Destoon.COM">&nbsp;&nbsp;&nbsp;</td>';
color_htm += '<td align="right" style="color:#FFFFFF;font-weight:bold;cursor:pointer;" onclick="color_close();" title="Close">&#215;&nbsp;</td>';
color_htm += '</tr>';
color_htm += '</table>';
color_htm += '<div id="destoon_color_show"></div>';
function color_show(id, color, obj) {
	Eh();
	if(Dd('destoon_color') == null) {
		var destoon_color_div = document.createElement("div");
		with(destoon_color_div.style) {zIndex = 9999; position = 'absolute'; display = 'none'; width = '160px'; padding = '1px'; top = 0; left = 0; border = '#A0A0A0 1px solid'; backgroundColor = '#FFFFFF';}
		destoon_color_div.id = 'destoon_color';
		document.body.appendChild(destoon_color_div);
	}
	var aTag = obj; var leftpos = toppos = 0;
	do {aTag = aTag.offsetParent; leftpos	+= aTag.offsetLeft; toppos += aTag.offsetTop;
	} while(aTag.offsetParent != null);
	Dd('destoon_color').style.left = (obj.offsetLeft + leftpos) + 'px';
	Dd('destoon_color').style.top = (obj.offsetTop + toppos + 20) + 'px';
	Dd('destoon_color').innerHTML = color_htm;
	color_id = id;
	color_bk = color;
	Dd('destoon_color').style.display = '';
	if(color) color_view(color);
	color_setup(color);
}
function color_hide() {Dh('destoon_color'); Es();}
function color_close() {color_hide(); Dd('color_img_'+color_id).style.backgroundColor = color_bk;}
function color_select(color) {Dd('color_input_'+color_id).value = color; Dd('color_img_'+color_id).style.backgroundColor = color; color_hide();}
function color_setup(color) {
	var colors = [
	'#000000', '#993300', '#333300', '#003300', '#003366', '#000080', '#333399', '#333333',
	'#800000', '#FF6600', '#808000', '#008000', '#008080', '#0000FF', '#000000', '#808080', 
	'#FF0000', '#FF9900', '#99CC00', '#339966', '#33CCCC', '#3366FF', '#800080', '#999999', 
	'#FF00FF', '#FFCC00', '#FFFF00', '#00FF00', '#00FFFF', '#00CCFF', '#993366', '#C0C0C0', 
	'#FF99CC', '#FFCC99', '#FFFF99', '#CCFFCC', '#CCFFFF', '#99CCFF', '#CC99FF', ''];
	var colors_select = '';
	colors_select += '<table cellpadding="0" cellspacing="0">'
	for(i = 0; i < colors.length; i++) {
		if(i%8 == 0) colors_select += '<tr>';
		colors_select += '<td width="20" height="20">';
		if(color == colors[i]) {
			colors_select += '<div class="color_div_o" onmouseover="color_view(\''+colors[i]+'\');" onclick="color_select(\''+colors[i]+'\');">';
		} else {
			colors_select += '<div class="color_div_t" onmouseover="this.className=\'color_div_o\';color_view(\''+colors[i]+'\');" onmouseout="this.className=\'color_div_t\';" onclick="color_select(\''+colors[i]+'\');">';
		}
		colors_select += '<div class="color_div" style="background:'+colors[i]+'">&nbsp;</div></div></td>';
		if(i%8 == 7) colors_select += '</tr>';
	}
	colors_select += '</table>';
	Dd('destoon_color_show').innerHTML = colors_select;
}
function color_view(color) {try {Dd('color_viewview').value = color; Dd('color_viewview').style.color = color; Dd('color_img_'+color_id).style.backgroundColor = color;} catch(e) {}}