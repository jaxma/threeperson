$(function(){

  $.init();
    //获取url栏 参数id
  var url = new URL(window.location.href);
  var id = url.searchParams.get('id');
 
  $.ajax({
      url:photopicTitle,
      type:'post',
      dataType:'json',
      data:{key:'topos',appr:app,id:id},
      success: function(data){
        // console.log(data.list);
        if(data.state == 1){
            $(".small-image-im-banner").append('<img src="'+app+data.list.image+'" alt="">');
            $.each(data.list.many_image,function(k,v){
              $(".small-image-im").append('<div class="col-33 small-image-c"><div class="boby-img"><img src="'+app+v+'" alt="" class="small-image"></div></div>');
            })
            // //多张查看 但无法定位
            var myPhotoBrowserPopup = $.photoBrowser({
                photos :data.list.new_many_image,
                type: 'popup'
            });
            $(document).on('click','.small-image-c img',function () {
              // console.log($(this).attr("src"));
              myPhotoBrowserPopup.open();
            });


            //单张查看
            // $(document).on('click','.small-image-c img',function () {
            //     var imger = [];
            //     imger.push($(this).attr("src"));
            //     $.photoBrowser({
            //       photos :imger,
            //       type: 'popup'
            //     }).open();
            // });
            // 
            
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

       