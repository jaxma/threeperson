$(function(){
     $.init();
    //获取url栏 参数id
  var url = new URL(window.location.href);
  var id = url.searchParams.get('id');

//滚动加载 条件
  var page = 1;
  var loading = true;
  titleImageData();
  $.attachInfiniteScroll($('.infinite-scroll'));
   
  $(document).on('infinite', '.infinite-scroll',function() {
    $('.infinite-scroll-preloader').show();
    if(loading){
      return false;
    }
    loading = true;
    setTimeout(function() {
      titleImageData();
    }, 1500);
  });
 
///滚动加载
 function titleImageData(){
  $.ajax({
    url:photopicTitle,
    type:'post',
    dataType:'json',
    data:{key:'topos',page_num:page,},
      success: function(data){
         // console.log(data);
        var empty = '<div><p style="text-align: center;margin-top:2rem;">- 暂无数据 -</p></div>';
        var html = [];
        if(data.state == 1){
          if(data.list != null){
              $.each(data.list,function(k,v){
                  if(v.cat1 == id){
                    var str = '<div class="classifyTitle-content-img"><a href="'+GROUP+'/Image/small_image.html?id='+v.id+'" class="classifyTitle-content-a external">'+
                    '<div class="classifyTitle-content-img-in"><img src="'+app+v.image+'" alt=""></div><div class="classifyTitle-content-img-title"><span>'+v.name+'</span>'+
                    '<h1>'+v.presents+'</h1><p class="">更多精彩</p></div></a></div>';
                  }
                   html.push(str);
              })
          }else{
             $.detachInfiniteScroll($('.infinite-scroll'));
             $('.infinite-scroll-preloader').remove();
              html.push(empty);
          }
          $('.classifyTitle-content').append(html);
          page++;
        }else{
          $.toast("获取失败,请刷新后重新再试!");
          $.detachInfiniteScroll($('.infinite-scroll'));
          $('.infinite-scroll-preloader').remove();
          $('.classifyTitle-content').append(empty);
          return false;
        }
        loading = false;
      }
    })
  }
})

 