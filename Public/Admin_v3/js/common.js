var empty = '<img src="' + TP_PUBLIC + '/Admin_v3/images/empty.jpg" style="width: 100%;height: 100%;"/>';
//弹框提示方法
function tusi(txt, fun) {
  $('.tusi').remove();
  // var div = $('<div class="tusi" style="background: url(/template/index/default/images/tusi.png);max-width: 85%;min-height: 77px;min-width: 270px;position: absolute;left: -1000px;top: -1000px;text-align: center;border-radius:10px;"><span style="color: #ffffff;line-height: 77px;font-size:20px;">' + txt + '</span></div>');
  // 	var div = $('<div class="tusi" style="display:none;background:rgba(90, 91, 92, 0.8);padding:0px 20px;width: 90%;position: absolute;left: 50%;top: -1000px;transform:translateX(-50%);text-align: center;border-radius:10px; rgba(255, 255, 255, 80)"><span style="color: #ffffff;line-height: 50px;font-size:20px;">' + txt + '</span></div>');
  // 	$('body').append(div);
  // 	div.css('zIndex', 9999999);
  //  div.css('left', parseInt(($(window).width() - div.width()) / 2));
  // 	var top = parseInt($(window).scrollTop() + ($(window).height() - div.height()) / 2);
  // 	div.css('top', top);
  // 	div.fadeIn();
  $.toast(txt);
  setTimeout(function() {
    		// div.fadeOut();
    if(fun) {
      eval("(" + fun + "())");
    }
  }, 2000);
}

function checkPhone(phone) {
  var reg = /^0?1[3|4|5|7|8][0-9]\d{8}$/;
  if(reg.test(phone)) {
    return true;
  } else {
    return false;
  }
}

$(document).ready(function() {
  if(typeof mui != "undefined"){
    mui('body').on('tap', 'a', function() {
      document.location.href = this.href;
    });
  }
  /*input 输入时隐藏搜索图标 */
  hideSearch();

  function hideSearch() {
    var oSearch = $('#search');
    var oIcon = $('.search-input .icon-search');
    oSearch.bind('input propertychange', function() {
      if(oSearch.val()) {
        oIcon.hide();
      } else {
        oIcon.show();
      }
    });
  }
})

//取参
function getUrlParam(name) {
  var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
  var r = window.location.search.substr(1).match(reg); //匹配目标参数
  if(r != null) return unescape(r[2]);
  return null; //返回参数值
}


// url取参 中文乱码解决
function getQueryString(name) { 
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
    var r = window.location.search.substr(1).match(reg); 
    if (r != null) return decodeURI(r[2]); return null; 
}
