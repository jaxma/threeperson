$(function(){
  $.ajax({
      url:photoStudio,
      type:'post',
      dataType:'json',
      data:{key:'topos'},
      success: function(data){
        // console.log(data);
        if(data.state == 1){
            $.each(data.list,function(k,v){
              $(".classifyTitle-content").append('<div class="classifyTitle-content-img"><a href="'+GROUP+'/Rental/index.html?id='+v.id+'"class="classifyTitle-content-a external">'+
              '<div class="classifyTitle-content-img-in"><img src="'+app+v.image+'" alt=""></div><div class="classifyTitle-content-img-title"><span></span><h1>'+v.name+'</h1><p class="">更多精彩</p></div></a></div>');
          })
        }else{
            $.toast("操作失败,返回重新再试");
        }
      }
  });
})
