$(function(){ 
	$(".m_wrap1 .box li").hover(function(){
		$(".m_wrap1 .box li").removeAttr("class");
		$(this).addClass("hover");	
	});
	
	$(".m_wrap6").each(function(i){
	   $("#list_img"+i).find("img").hover(function(){
			$("#b_img"+i).attr('src', $(this).attr('data-ori'));
		});	
	});	
	
	$(".m_wrap7 li a:odd").css({"background":"#f9f9f9"});
	/*banner自适应屏幕*/
	//var w_img = window.screen.width
	//$(".slideBox .bd").find("img").width(w_img);
	/*banner自适应屏幕*/
	/*回顶部*/
	/*$("#totop").click(function() {
        $('body,html').animate({
            scrollTop : 0
        }, 500);
        return false;
    }); */
	/*回顶部*/
	/*$(window).scroll(function () {
		if($(window).scrollTop()>500){
			$("#totop").show();
		}else{
			$("#totop").hide();
		}
	});*/
});

/*input提示*/
/*value="关键词" defaultval="关键词" onblur="blurInputEle(this)" onfocus="focusInputEle(this)"*/
function getAttributeValue(o, key) {
if (!o.attributes) return null;
var attr = o.attributes;
for (var i = 0; i < attr.length; i++){
if (key.toLowerCase() == attr[i].name.toLowerCase())
return attr[i].value;
}
return null;
}
function focusInputEle(o) {
if (o.value == getAttributeValue(o, 'defaultVal')){
o.value = '';
o.style.color = "#3b8dd0";
}
}
function blurInputEle(o) {
if (o.value == '') {
o.value = getAttributeValue(o, 'defaultVal');
o.style.color = "#3b8dd0";
}
}
/*input提示*/
/*setTimeout(function(){
alert(msg);
},1000);*/