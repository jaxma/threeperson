$(function(){
  
   $.ajax({
        url:LogoAPI,
        type:'post',
        dataType:'json',
        data:{key:'topos',},
        success:function(data){
          // console.log(data); 
          $(".links-content").append('<img src="'+app+data.logo_img+'" alt="">'); 
        }
    });

});