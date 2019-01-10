var slideflag = false
var isfold = (getCookie('menu-style') == 'mini'?true:false)

$(function() {
  //搜索框
  //$('#search-text').bind({
  //  'focus':function() { $('#search-btn').css('background-color', 'white')},
  //  'blur':function() { $('#search-btn').css('background-color', '#374850')}
  //});
  
 if(isfold){
   $('.treeview-menu').slideUp();
    $('.arrow-icon').removeClass('active');
    $('.user-wrapper').toggleClass('fold');
    $('.search-wrapper').toggle('fold');
    $('.sidebar-menu').toggleClass('fold');
 }
  //一级菜单的点击事件
  $(document).on('click', '.list', function() {
    if(!isfold) {
      if(slideflag)
        return
      slideflag = true
      $(this).parent('li').siblings('li').children('.treeview-menu').slideUp();
      $(this).parent('li').siblings('li').children('.list').removeClass('active');
      $(this).parent('li').siblings('li').children('.only').removeClass('active');
      $(this).parent('li').siblings('li').find('.arrow-icon').removeClass('active');
      $(this).children('.arrow-icon').toggleClass('active')
      $(this).addClass('active').siblings('.treeview-menu').slideToggle(function() {
        slideflag = false
      });
    }
  })
  //一级菜单没有子菜单的点击事件
  $(document).on('click', '.only', function() {
    if(slideflag)
      return
    slideflag = true
    $(this).parent().siblings('li').children('.treeview-menu').slideUp();
    $(this).parent().siblings('li').children('.list').removeClass('active');
    $(this).parent().siblings('li').find('.arrow-icon').removeClass('active');
    $(this).parent().siblings('li').find('.only').removeClass('active');
    $(this).addClass('active')
    slideflag = false
  })
  //二级菜单点击事件
  $(document).on('click', '.tm-items', function() {
    $('.list').removeClass('active')
    $(this).parent().parent().parent().siblings('li').find('.only').removeClass('active')
    $(this).parent().parent().siblings('.list').addClass('active')
    $('.tm-items').removeClass('active')
    $(this).addClass('active');
  });
  //折叠按钮点击事件
  $('.sidebar-fold').bind('click', function() {
    isfold = !isfold;
    $('.framework-sidebar').addClass('active')
    $('.treeview-menu').slideUp();
    $('.arrow-icon').removeClass('active');
    $('.user-wrapper').toggleClass('fold');
    $('.search-wrapper').toggle('fold');
    $('.sidebar-menu').toggleClass('fold');
  });
  $('.treeview').hover(function(){$('.framework-sidebar').css({overflowX:'visible'})},function(){$('.framework-sidebar').css({'overflow-x':'hidden'})})
})

function getCookie(name) {
  var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
  if(arr = document.cookie.match(reg))
    return unescape(arr[2]);
  else
    return null;
}