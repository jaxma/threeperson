$(function(){
     $.init();
  //滚动加载 条件
  var page = 1;
  var loading = true;
  backstageCatData();
  $.attachInfiniteScroll($('.infinite-scroll'));
   
  $(document).on('infinite', '.infinite-scroll',function() {
    $('.infinite-scroll-preloader').show();
    if(loading){
      return false;
    }
    loading = true;
    setTimeout(function() {
      backstageCatData();
    }, 1500);
  });
 
///滚动加载
 function backstageCatData(){
  $.ajax({
    url:studioTitlerow,
    type:'post',
    dataType:'json',
    data:{key:'topos',page_num:page,bs:'bs'},
      success: function(data){
        // console.log(data);
        var empty = '<div><p style="text-align: center;margin-top:2rem;">- 暂无数据 -</p></div>';
        var html = [];
        if(data.state == 1){
          if(data.list != null){
             // console.log(data.list);
              $.each(data.list,function(k,v){
                var str = '<div class="classifyTitle-content-img"><a href="'+GROUP+'/Backstage/index.html?id='+v.id+'"class="classifyTitle-content-a external">'+
              '<div class="classifyTitle-content-img-in"><img src="'+app+v.image+'" alt=""></div><div class="classifyTitle-content-img-title"><span></span><h1>'+v.name+'</h1><p class="">更多精彩</p></div></a></div>';
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
          $.toast("操作失败,返回重新再试");
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
