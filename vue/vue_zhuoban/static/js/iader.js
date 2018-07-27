$(function(){

$(".sty1").hover(function(){
  $(this).css({"background-color":"#f5f5f5","color":"#000000"});
},function(){
  $(this).css({"background-color":"#f98985","color":"#ffffff"});
});
$(".bg3").hover(function(){
  $(this).css({"background-color":"#f5f5f5","color":"#000000"});
},function(){
  $(this).css({"background-color":"#ef8a6f","color":"#ffffff"});
});


$(".iehover").hover(function(){
		$img=$(this).children('a').children('div').children('img');
		$imgsrc=$img.attr("src");
		console.log($imgsrc);
		$imgsrc=$imgsrc.replace('2','1');
		console.log($imgsrc);
		$img.attr("src",$imgsrc)
	},function(){
		$img=$(this).children('a').children('div').children('img');
		$imgsrc=$img.attr("src");
		console.log($imgsrc);
		$imgsrc=$imgsrc.replace('1','2');
		console.log($imgsrc);
		$img.attr("src",$imgsrc)
})

$(".ie-texiao").click(function(){
			console.log(bhg);
			bhg=!bhg;
			if(bhg){
				$(this).attr("src","static/img/array/arr_04a.png");
				$(".forelement .zhaoxiang").eq(3).css("display","block");
				$(".forelement .zhaoxiang").eq(4).css("display","block");
				$(".forelement .zhaoxiang").eq(5).css("display","block");
			}else{
				$(this).attr("src","static/img/array/arr_04b.png");
				$(".forelement .zhaoxiang").eq(3).css("display","none");
				$(".forelement .zhaoxiang").eq(4).css("display","none");
				$(".forelement .zhaoxiang").eq(5).css("display","none");
				Rtop=$("#team").offset().top;
				console.log(Rtop);
				$("body,html").animate({scrollTop:Rtop});
			}
});

$(".ie-texiao").hover(function(){
		if(bhg){
			$(this).attr("src","static/img/array/arr_04c.png");
		}else{
			$(this).attr("src","static/img/array/arr_04d.png");
		};
		$(this).animate({transform:'scale(1.4)'},400);
		$(this).animate({'-webkit-transform':'scale(1.4)'},400);
		$(this).animate({'-o-transform':'scale(1.4)'},400);
		$(this).animate({'-moz-transform':'scale(1.4)'},400);
		$(this).animate({'-ms-transform':'scale(1.4)'},400);
	},function(){
		if(bhg){
			$(this).attr("src","static/img/array/arr_04b.png");
		}else{
			$(this).attr("src","static/img/array/arr_04a.png");
		};
		$(this).animate({transform:'scale(1)'},400);
		$(this).animate({'-webkit-transform':'scale(1)'},400);
		$(this).animate({'-o-transform':'scale(1)'},400);
		$(this).animate({'-moz-transform':'scale(1)'},400);
		$(this).animate({'-ms-transform':'scale(1)'},400);
})

$("#mainNav li").each(function(){
		docuarr.push($($(this).children("a").attr("data-href")).offset().top-30);
	});
	doclen=docuarr.length;
	console.log(docuarr);
	$("#mainNav li").click(function(){
		boolg=false;
			$(this).siblings().removeClass("active");
			$(this).addClass("active");
	});

	$(window).scroll(function(){
				scrtop=$(window).scrollTop();
				console.log(scrtop);
				if(scrtop>=docuarr[doclen-1]){
						scrindex=doclen-1;
				}else{
					for(var i=0;i<docuarr.length;i++){
						if(scrtop>=docuarr[i]&&scrtop<docuarr[i+1]){
							scrindex=i;
							break;
						};
					};
					console.log(scrindex);
				};
		 $("#mainNav>li:eq("+scrindex+")").siblings().removeClass("active");
		 $("#mainNav>li:eq("+scrindex+")").addClass("active");
});


$(".nav a").click(function(e) {
    var sel = $(this).attr("href");
    $('html,body').animate({scrollTop: $(sel).offset().top}, 500);
    e.preventDefault();
});

});
