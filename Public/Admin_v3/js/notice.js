$(function() {
  var oP = $('.content .inform .inform-text p');
  var oWrapper = $('.content .inform .inform-text');
  var pLeft = oP.position.left;
  setInterval(function() {
    pLeft--;
    if(pLeft < -(oP.width() + 10)) {
      pLeft = oWrapper.width();
    }
    oP.css('left', pLeft + 'px');
  }, 30);
})