$(function(){

  //获取url栏 参数id
  var url = new URL(window.location.href);
  var id = url.searchParams.get('id');

   $.ajax({
          url:photoStudio2,
          type:'post',  
          dataType:'json',
          data:{key:'topos',id:id,},
          success: function(data){
            // console.log(data);
            if(data.state == 1){
               $(".rental-list-content").append(data.list.news);
            }else{
               $.toast("获取失败,请重新再试");
            }
          }
    });

});