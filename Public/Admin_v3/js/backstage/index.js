$(function(){
   $.init();
  //获取url栏 参数id
  var url = new URL(window.location.href);
  var rid = url.searchParams.get('id');

   $.ajax({
        url:backstageFind,
        type:'post',
        dataType:'json',
        data:{key:'topos',id:rid,appr:app},
        success: function(data){
        	// console.log(data);
          if(data.state == 1){
                // //多张查看 但无法定位
                var myPhotoBrowserPopup = $.photoBrowser({
                    photos :data.list,
                    type: 'popup'
                });
                $(document).on('click','.backstage-content-image',function () {
                  // console.log($(this).attr("src"));
                  myPhotoBrowserPopup.open();
                  // myPhotoBrowserPopup.open($(this).parents('div').index());
                });
                
                //图片
                 $(".boby-img").each(function(k,v){
                  var div_img = $(this).height();
                  var small_image = $(this).children('img').height();
                  var num = small_image - div_img;
                  if(num < 0){
                    $(this).children('img').addClass('small-image-imru');
                  }else{
                    $(this).children('img').removeClass('small-image-imru');
                  }   
                })
          }else{
            $.toast("获取失败,请重新再试");
          }
        }
    });



})

