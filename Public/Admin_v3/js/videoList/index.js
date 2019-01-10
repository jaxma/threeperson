$(function(){
       $.init();
///滚动加载 条件
  var page = 1;
  var loading = false;
  videoListData();
  $.attachInfiniteScroll($('.infinite-scroll'));
 	 
	$(document).on('infinite', '.infinite-scroll',function() {
    if(loading){
      return false;
    }
    loading = true;
	setTimeout(function() {
		videoListData();
    }, 1500);

  });
///滚动加载
 function videoListData(){
	$.ajax({
		url:video_list,
		type:'post',
		dataType:'json',
		data:{key:'topos',page_num:page,},
	    success: function(data){
	   		 // console.log(data);
    	  var empty = '<div><p style="text-align: center;margin-top:2rem;">- 暂无数据 -</p></div>';
    	  var html = [];
    	  if(data.state == 1){
    	  	if(data.list != null){
            // $.toast(data.msg);
    	  		$.each(data.list, function(k,v) {
				var str = '<div class="card"><div class="card-header">'+v.time+'</div><div class="card-content"><div class="card-content-inner">'+
				    '<video poster="'+app+v.image+'" controls controlslist="nodownload" playsinline="" webkit-playsinline="" ><source src="'+app+v.video+'" type="video/mp4"></video>'+
				    '</div></div><div class="card-footer"><div class="card-footer-video"><h2>'+v.name+'</h2><p>'+v.presents+'</p></div></div></div>';
	          	  html.push(str);
	    	    });
    	  	}else{
    	  	   $.detachInfiniteScroll($('.infinite-scroll'));
	           $('.infinite-scroll-preloader').remove();
	            html.push(empty);
    	  	}

    	    $('.videoList-content').append(html);
    	    page++;
    	  }else{
    	    $.toast(data.msg);
    	    $.detachInfiniteScroll($('.infinite-scroll'));
    	    $('.infinite-scroll-preloader').remove();
    	    $('.videoList-content').append(empty);
    	    return false;
    	  }
    	  loading = false;
    	}
	})
}
})
