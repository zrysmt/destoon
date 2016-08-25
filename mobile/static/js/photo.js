/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
var X1 = X2 = 0;
function photo_show(obj) {
	if(obj != cur) {
		Dd('photo').src = Dd('image_'+(obj-1)).innerHTML;
		Dd('photo_page').innerHTML = obj;
		Dd('photo_intro').innerHTML = Dd('intro_'+(obj-1)).innerHTML;
		cur = obj;
	}
}
function photo_next() {
	if(cur >= max) {
		photo_show(1);
	} else {
		photo_show(cur + 1);
	}
}
function photo_prev() {
	if(cur <= 1) {
		photo_show(max);
	} else {
		photo_show(cur - 1);
	}
}