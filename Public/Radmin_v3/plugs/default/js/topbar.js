var a_width = document.body.offsetWidth;
var p_width = a_width - 360;
var s_width = p_width;
var temp;
var html = '';
$(function() {
  $(window).resize(function() {
    a_width = document.body.clientWidth;
    p_width = a_width - 360;
    $('.control-menu').width(p_width)
    var c_width = 10;
    var html = '';
    var aim = $('.control-menu').children('li');
    for(var i = 0; i < aim.length; i++) {
      c_width += $(aim.get(i)).width() + 2
    };
    if(p_width > s_width) {
      if(p_width >= (c_width + 170)&&$('.downs').children().length>0) {
        var _this = $('.downs li:last a')
        temp = '<a data-menu-node="m-2-1" data-open="' + _this.data('open') + '">';
        temp += _this.children('.fa').prop('outerHTML')+_this.children('.title2').prop('outerHTML') + '</a>';
        html = '<li class="items active">' + temp + '<i class="fa fa-close close close-link"></i></li>';
        $('.control-menu').children().removeClass('active').parent().append(html)
        _this.parent().remove();
      }
    } else {
      if(p_width <= (c_width + 170)) {
        var _this = $('.control-menu li:last a')
        html = '<li role="presentation" class="dropdown-items"><a data-open="' + _this.data('open') + '" node-id="10" aria-controls="10" role="tab" data-toggle="tab">' +
          _this.children('.fa').prop('outerHTML')+_this.children('.title2').prop('outerHTML') + '</a><i class="close-tab fa fa-remove close-link"></i></li>';
        $('.downs').append(html)
        $('.control-menu li:last').remove()
      }

    }
    s_width = p_width
    //  else if(p_width>(c_width + 170)){
    //    var _this = $('.downs li:last a')
    //    temp = '<a data-menu-node="m-2-1" data-open="' + _this.data('open') + '">';
    //    temp += _this.children('.fa').prop('outerHTML')+_this.children('.title2').prop('outerHTML') + '</a>';
    //    html = '<li class="items active">' + temp + '<i class="fa fa-close close"></i></li>';
    //    $('.control-menu').children().removeClass('active').parent().append(html)
    //    _this.parent().remove();
    //  }
  })
  $(document).on('click', '.tm-items , .only', function() {
    var c_width = 10;
    var html = '';
    $.each($('.control-menu').children('li'), function(key, value) {
      c_width += $(value).width() + 2
    });
    // console.log(p_width - (c_width + 170));
    var point = $(this).data('menu-node');
    if($('.control-menu').find('.'+point).length>0||$('.downs').find('.'+point)>0){
      if($('.control-menu').find('.'+point).length>0){
        $('.control-menu').children('li').removeClass('active');
        $('.control-menu').find('.'+point).addClass('active')
      }else{
        $('.downs').children('li').removeClass('active');
        $('.downs').find('.'+point).addClass('active')
      }
      return
    }
    if(c_width > (p_width - 170)) {
      html = '<li role="presentation" class="dropdown-items '+$(this).data('menu-node')+'"><a data-open="' + $(this).data('open') + '" node-id="10" aria-controls="10" role="tab" data-toggle="tab">' +
        $(this).children('.fa').prop('outerHTML')+$(this).children('.title2').prop('outerHTML') + '</a><i class="close-tab fa fa-remove close-link"></i></li>';
        $('.tabdrop').show()
        $('.downs').append(html)
    } else {
      temp = '<a data-menu-node="m-2-1" data-open="' + $(this).data('open') + '">';
      temp += $(this).children('.fa').prop('outerHTML')+$(this).children('.title2').prop('outerHTML') + '</a>';
      html = '<li class="items active '+$(this).data('menu-node')+'">' + temp + '<i class="fa fa-close close close-link"></i></li>';
      $('.control-menu').children().removeClass('active').parent().append(html)
    }

  })

  $(document).on('click','.close-link',function(){
    $(this).parent().remove();
    var c_width = 10;
    $.each($('.control-menu').children('li'), function(key, value) {
      c_width += $(value).width() + 2
    });
    if($(this).parent().hasClass('items')&&$('.downs').children().length>0){
      if(c_width > (p_width - 170)){
        return;
      }
      temp = '<a data-menu-node="m-2-1" data-open="' + $('.downs li:last a').data('open') + '">';
      temp += $('.downs li:last a').html() + '</a>';
      html = '<li class="items">' + temp + '<i class="fa fa-close close close-link"></i></li>';
      $('.control-menu').append(html)
      $('.downs li:last').remove()
    }else{
      if(!$('.downs').children().length>0){
        $('.tabdrop').hide()
      }
    }
  })
});