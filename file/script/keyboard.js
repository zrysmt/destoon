/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
var iid; var kid;
var chars = ['', '`1234567890-=', '~!@#$%^&*()_+', 'qwertyuiop[]\\', 'QWERTYUIOP{}|', 'asdfghjkl;\'', 'ASDFGHJKL:"', 'zxcvbnm,./', 'ZXCVBNM<>?'];
document.write('<style>#kb{position:absolute;z-index:999;border:#7F9DB9 1px solid;background:#F1FAFF;}#kb input{padding:1px 6px 1px 6px;cursor:pointer;font-size:12px;width:25px;height:25px;line-height:18px;}</style>');
function _h() {Dh(kid);}
function _s(v) {for(var i = 1; i < 9; i++) {Dd('table_'+i).style.display = (i%2 == v ? 0 : 1) ? '' : 'none';}}
function _i(v) {if(v == '&quot;') {v = '"';} Dd(iid).value += v;}
function _k(i, k, o) {
	iid = i; kid = k;
	var htm = '';
	for(var i = 1; i < 9; i++) {
		var l = chars[i].length; var r = Math.floor(Math.random()*l); var s = i%2 == 0 ? 'none' : '';
		htm += '<table id="table_'+i+'" style="display:'+s+';"><tr>';
		for(var j = r; j >= 0; j--) {
			var v = chars[i].charAt([j]);
			if(v == '"') v = '&quot;';
			htm += '<td title=" '+v+' "><input type="button" value="'+v+'" onclick="_i(this.value)"/></td>';
		}
		for(var j = r+1; j < l; j++) {
			var v = chars[i].charAt([j]);
			if(v == '"') v = '&quot;';
			htm += '<td title=" '+v+' "><input type="button" value="'+v+'" onclick="_i(this.value)"/></td>';
		}
		if(i == 5) htm += '<td title="Enter"><input type="button" value="Enter" onclick="_h();" style="width:54px;"/></td>';
		if(i == 6) htm += '<td title="Enter"><input type="button" value="Enter" onclick="_h();" style="width:54px;"/></td>';
		if(i == 7) htm += '<td title="Shift"><input type="button" value="Shift" onclick="_s(1);" style="width:54px;"/><td title="Esc"><input type="button" value="x" onclick="_h();"/></td>';
		if(i == 8) htm += '<td title="Shift"><input type="button" value="Shift" onclick="_s(0);" style="width:54px;"/><td title="Esc"><input type="button" value="x" onclick="_h();"/></td>';
		htm += '</tr></table>';
	}
	var a = o; var kb_l = kb_t = 0;
	do {a = a.offsetParent; kb_l += a.offsetLeft; kb_t += a.offsetTop;
	} while(a.offsetParent != null);
	Dd(kid).style.left = (o.offsetLeft + kb_l - 160) + 'px';
	Dd(kid).style.top = (o.offsetTop + kb_t + 18) + 'px';
	Dd(kid).style.display = '';
	Dd(kid).innerHTML = htm;
}
