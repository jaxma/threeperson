$(function(){
   layui.use('element',function(){
       var $ = layui.jquery
       ,element = layui.element;
   });
   $('#del_msg_btn').click(function(){
       var temp = [];
       $.each($('.del_msg'), function(k,v) {
           if($(v).is(':checked')){
               temp.push($(v).data('id'));
           }
       }); 
       
       if(temp.length>0){
           layer.confirm('是否删除选中的用户消息？',function(index){
               layer.close(index);
               layer.load();
               $.post(delete_url,{id:temp},function(data){
                  layer.close(layer.load())
                  if(data.code==1){
                      layer.msg(data.msg);
                      setTimeout(function(){window.location.reload()},2000);
                  }else{
                      layer.msg(data.msg);
                  }
               });
           });
       }
});
   
   
   $('#check_all').click(function(){
       if($(this).is(':checked')){
           $('.del_msg').prop('checked',true);
       }else{
           $('.del_msg').prop('checked',false);
       }
       form.render();
   });
   
});
