 $(function(){


    $("#san").click(function(){
      $("#san-content").animate({left:'80%'});
      /*遮罩*/
      $("#mask").animate({left:'80%'});
      $("#mask").css("height",$(document).height());     
      $("#mask").css("width",$(document).width());     
      $("#mask").show();    
    });

    $(".go-right-sidebar").click(function(){
      $("#sidebar-content").animate({left:'-80%'});
      var titleName = $(this).text();
      $(".titleName").html(titleName);
    })

    $(".go-left-sidebar").click(function(){
      $("#sidebar-content").animate({left:'0'});
      $(".right-p").empty();
    })


    $(".icon-outxx").click(function(){
      $("#san-content").animate({left:'0'});
      /*遮罩*/
      $("#mask").animate({left:'0'});
      $("#mask").hide();    
    })


    $(".phpotoNum").click(function(){
        $.ajax({
          url:photoCAt,
          type:'post',
          dataType:'json',
          data:{key:'topos'},
          success: function(data){ 
            if(data.state == 1){
                var str="";            
                $.each(data.list,function(k,v){
                    str += '<p><a href="'+GROUP+'/Image/title_image.html?id='+v.id+'" class="external">'+v.name+'</a></p>';
                    $(".right-p").html(str);
                });
            }else{
                $.toast("操作失败,返回重新再试");
            }       
          }
        })
    })
  
      
    // $(".RENTALNum").click(function(){
    //     $.ajax({
    //       url:photoStudio,
    //       type:'post',
    //       dataType:'json',
    //       data:{key:'topos'},
    //       success: function(data){
    //         // console.log(data);    
    //         if(data.state == 1){
    //             var str="";            
    //             $.each(data.list,function(k,v){
    //                 str += '<p><a href="'+GROUP+'/rental/index?id='+v.id+'" class="external">'+v.name+'</a></p>';
    //                 $(".right-p").html(str);
    //             });
    //         }else{
    //             $.toast("操作失败,返回重新再试");
    //         }      
    //       }
    //     })
    // })



})