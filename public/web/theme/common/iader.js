$(document).ready(function(){
 
 $(".stepkuai .title").click(function(){
   $(this).parents().find(".often").show();
 })
 
 $(".oftenlist li").click(function(){
   $(this).parents(".often").hide();
   $(".stepkuai .cont textarea").val($(this).html())
 })	

 $(document).on("click",'.handlerlist li .more',function(){
       $(this).parent().remove();
 });
	
 $(".avatarlist .avacont").click(function(){
   $(this).parent().find(".avatarnote").show();
 })
 $(".avatarswitch").click(function(){
   $(this).parents(".avatarnote").hide();
 })
 $(".phonekuai .avacont").click(function(){
   $(this).parent().find(".phobecont").show();
 })
 $(".phonemore").click(function(){
   $(this).parents(".phobecont").hide();
 })
 
 $(".department li .title").click(function(){
   $(this).toggleClass("on");	 
   $(this).parent().find("dl").toggle(500);
 })
 $(".department li dd .twotitle").click(function(){
   $(this).toggleClass("on");
   $(this).parent().find(".threelist").toggle(500);
 })

 $(".onclasson").click(function(){
   $(this).parents().find(".bulleton").show();
 })
 
  $(".onclasshover").click(function(){
   $(this).parents().find(".bullethover").show();
 })

 var id="1";
 var name="1";
 $(".bulleton .contact li dd a").click(function(){
	 id += 1;
	 name += 2;
	 $(this).parents().find(".bullet").hide();
	 $(".stepon").prepend('<li><p id="'+id+'" class="tu"><img src="" width="83" height="83" /></p><p id="'+name+'" class="name"></p><p class="more"></p></li>');
	 var contacturl = $(this).find(".tu img").attr("src");    //获取当前点击图片的地址
	 document.getElementById(id).innerHTML = "<img src='"+contacturl+"' width='100%' height='100%' />";
	 var contacthtml = $(this).find(".name").html();         //获取文字
	 document.getElementById(name).innerHTML = contacthtml;
 })
 
 $(".bullethover .contact li dd a").click(function(){
	 id += 1;
	 name += 2;
	 $(this).parents().find(".bullet").hide();
	 $(".stephover").prepend('<li><p id="'+id+'" class="tu"><img src="" width="83" height="83" /></p><p id="'+name+'" class="name"></p><p class="more"></p></li>');
	 var contacturl = $(this).find(".tu img").attr("src");    //获取当前点击图片的地址
	 document.getElementById(id).innerHTML = "<img src='"+contacturl+"' width='100%' height='100%' />";
	 var contacthtml = $(this).find(".name").html();         //获取文字
	 document.getElementById(name).innerHTML = contacthtml;
 })
  
 /*选项卡*/	
 function tabs(tabTit,on,tabCon){
	$(tabCon).each(function(){
	  $(this).children().eq(0).show();
	  });
	$(tabTit).each(function(){
	  $(this).children().eq(0).addClass(on);
	  });
     $(tabTit).children().click(function(){
      $(this).addClass(on).siblings().removeClass(on);
      var index = $(tabTit).children().index(this);
      $(tabCon).children().eq(index).show().siblings().hide();
 });
 }
 tabs(".tab-hd,","active",".tab-bd");

 
});