/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
var _p = 0;
function AutoTab() {
	var c;
	Dd('trades').onmouseover = function() {_p = 1;} 
	Dd('trades').onmouseout = function() {_p = 0;}
	if(_p) return;
	for(var i = 1; i < 4; i++) { if(Dd('trade_t_'+i).className == 'tab_2') {c = i;} }
	c++; 
	if(c > 3) c = 1;
	Tb(c, 3, 'trade', 'tab');
}
if(Dd('trades') != null) window.setInterval('AutoTab()',5000);
function ipad_tip_close() {
	Dh('ipad_tips');
	set_local('ipad_tips', 1);
}
if(Dd('ipad_tips') != null && UA.match(/(iphone|ipad|ipod)/i) && get_local('ipad_tips') != 1) {
	Ds('ipad_tips');
	Dd('ipad_tips').innerHTML = '<div class="ipad_tips_logo"><img src="'+DTPath+'apple-touch-icon-precomposed.png" width="50" height="50" alt=""/></div><div class="ipad_tips_text"><strong>把本站添加至主屏幕</strong><br/>请点击工具栏上的<span class="ipad_tips_ico1"></span>或者<span class="ipad_tips_ico2"></span>并选择“添加书签”或者“添加至主屏幕”便于下次直接访问。</div><div class="ipad_tips_hide"><a href="javascript:ipad_tip_close();" class="ipad_tip_close" title="关闭并不再提示">&nbsp;</a></div><div class="c_b"></div>';
}