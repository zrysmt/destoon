/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
function check_mode(c, m) {
	var mode_num = 0; var e = Dd('com_mode').getElementsByTagName('input');	
	for(var i=0; i<e.length; i++) {if(e[i].checked) mode_num++;}
	if(mode_num > m) {confirm(lang(L['max_mode'], [m])); c.checked = false;}
}
function addop(id, v, t) {var op = document.createElement("option"); op.value = v; op.text = t; Dd(id).options.add(op);}
function delop(id) {
	var s = -1;
	for(var i = 0; i < Dd(id).options.length; i++) {if(Dd(id).options[i].selected) {s = i; break;}}
	if(s == -1) {alert(L['choose_category']); Dd(id).focus();} else {Dd(id).remove(s);}
}
function addcate(m) {
	var v = Dd('catid_1').value; var l = Dd('cates').options.length;
	if(l >= m) {alert(lang(L['max_cate'], [m])); return;}
	for(var i = 0; i < l; i++) {if(Dd('cates').options[i].value == v) {alert(L['category_chosen']); return;}}
	var e = Dd('cate').getElementsByTagName('select'); var c = s = '';
	for(var i = 0; i < e.length; i++) {if(e[i].value) {s = e[i].options[e[i].selectedIndex].innerHTML; c += s + '/'; s = '';}}
	if(c) {c = c.replace('&amp;', '&'); c = c.substring(0, c.length-1); addop('cates', v, c); Dd('catid').value = Dd('catid').value ? Dd('catid').value+v+',' : ','+v+',';} else {alert(L['choose_category']);}
}
function delcate() {
	var s = -1;
	for(var i = 0; i < Dd('cates').options.length; i++) {if(Dd('cates').options[i].selected) {s = i; break;}}
	if(s == -1) {alert(L['choose_category']); Dd('cates').focus();} else {Dd('catid').value = Dd('catid').value.replace(','+Dd('cates').options[s].value+',', ','); Dd('cates').remove(s);}
}