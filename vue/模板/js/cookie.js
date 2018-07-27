function setCookie(name, value, iDay) { //封 设置cookie的函数
	var oDate = new Date();
	//var iDay=300;
	oDate.setDate(oDate.getDate() + iDay); //修改时间 修改的是 var oDate=new Date();这个返回的不是秒
	document.cookie = name + '=' + value + ';expires=' + oDate;
	//alert(oDate)
}

function getCookie(name) {
	var arr = document.cookie.split(';'); // 分号 将cookie字符串切片成一个数组
	for(var i = 0; i < arr.length; i++) {
		var arr2 = arr[i].split('='); //用等号 将数组每一个字符串 切片成一个新数组   新数组就是cookie的name跟value
		if(arr2[0] == name) { //新数组的第0个为name
			return arr2[1] //放回新数组 第1个；
		}

	}
	return ''; //获取不存在的cookie  返回空值
}

function removeCookie(name) {
	setCookie(name, '1', -1)
}