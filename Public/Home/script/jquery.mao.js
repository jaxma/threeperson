$(function(){
	var y = ($(window).height()-$(".m_point").height())/2
	$(".m_point").css({"top":y});
	navpos();
	
	var mao0 = $("#mao0").offset().top-110;
	var mao1 = $("#mao1").offset().top-110;
	var mao2 = $("#mao2").offset().top-110;
	var mao3 = $("#mao3").offset().top-110;
	var mao4 = $("#mao4").offset().top-110;
	var mao5 = $("#mao5").offset().top-110;
	//alert(tops);
	$(window).scroll(function(){
		var scroH = $(this).scrollTop();
		if(scroH>=mao5-110){
			set_cur(".mao5");
		}else if(scroH>=mao4){
			set_cur(".mao4");
		}else if(scroH>=mao3){
			set_cur(".mao3");
		}else if(scroH>=mao2){
			set_cur(".mao2");
		}else if(scroH>=mao1){
			set_cur(".mao1");
		}else if(scroH>=mao0){
			set_cur(".mao0");
		}
	});
	
	$(".m_point li").click(function() {
		var el = $(this).attr('class');
     	$('html, body').animate({
         	scrollTop: $("#"+el).offset().top-110
     	}, 300);
 	});
	
	$(".m_arrows").click(function() {
		var el = $(this).attr('title');
     	$('html, body').animate({
         	scrollTop: $("#mao"+el).offset().top-110
     	}, 300);
 	});
});
$(window).resize(function(){
  navpos();
});
function navpos(){
	var page_w = $(document).width();
	var offset = $("#main").offset().left;
	var main_w = $("#main").outerWidth();
	var right = page_w-offset-main_w-50;
	//alert(right);
	if(right>10){
		$(".m_point").css('right',right-10);
	}else{
		$(".m_point").css('right',10);
	}
}
function set_cur(n){
	if($(".m_point li").hasClass("cur")){
		$(".m_point li").removeClass("cur");
	}
	$(".m_point li"+n).addClass("cur");
}