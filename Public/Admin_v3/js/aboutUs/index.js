$(function(){

  //获取url栏 参数id
  var url = new URL(window.location.href);
  var type = url.searchParams.get('type');
    if(!type){
      var type=1;
    }

   $.ajax({
          url:companyList,
          type:'post',
          dataType:'json',
          data:{key:'topos',type:type},
          success: function(data){
          if(data.state == 1){
                $("#tab1 .content-block").html(data.list.content);
            }else{
               $.toast("获取失败,请重新再试");
            }
          }
    });


    var typeTwo = 2;
    $.ajax({
        url:companyList,
        type:'post',
        dataType:'json',
        data:{key:'topos',type:typeTwo},
        success: function(data){
          if(data.state == 1){
            $("#tab2 .content-block").html(data.list.content);
          }else{
             $.toast("获取失败,请重新再试");
          }
        }
    });

 


})

