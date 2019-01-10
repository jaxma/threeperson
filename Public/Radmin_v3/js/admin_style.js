$(function() {
  form.render();
  layui.use('carousel', function() {
    var carousel = layui.carousel;
    
    $.each($('.layui-carousel'), function(key,value) {
      var wrapper ='#' + $(value).attr('id');
      if(new RegExp(/^sm/).test($(value).attr('id'))){
        //建造实例
          carousel.render({
            elem: wrapper,
            width: '100%',
            height: '580px',
            arrow: 'none',
            indicator:'none'
          });
      }else{
        //建造实例
        carousel.render({
          elem: wrapper,
          width: '100%',
          arrow: 'none',
          indicator:'none'
        });
      }
    });
    
  });

  form.on('radio',function(data){
    if(data.elem.checked){
      $(this).parent().siblings('.style_mask').addClass('active');
      $(this).parent().parent().siblings('.style_item').find('.style_mask').removeClass('active');
      $(this).parent().parent().parent().siblings('.style_url').val(data.value);
    }
  });
  
  layui.use('element', function(){
    var element = layui.element;
  });

//$('.style_list').on('click', '.style_item', function() {
//  $(this).find('.style_mask').addClass('active');
//  $(this).siblings('.style_item').find('.style_mask').removeClass('active');
//  $('#style_id').val($(this).data('id'));
//  $('.btn_submit').click();
//})
})