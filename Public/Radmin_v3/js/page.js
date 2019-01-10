/* 
 * 分页js
 * add by zbs
 * create by 2017-10-22
 */

// layui.use('laypage', function(){
//   var laypage = layui.laypage;

  //执行一个laypage实例
  laypage.render({
    elem: 'page', //注意，这里的 page 是 ID，不用加 # 号
    count: count, //数据总数，从服务端得到
    limit: limit, //每页显示数量
    groups: 5,//连续出现的页码个数
    curr: p, //当前页
    layout: ['count', 'page', 'next'],
    jump : function(obj, first) {
      
        if (!first) {
          
            var url = changeURLArg(window.location.href, 'p', obj.curr);
            window.location.href = url;
        }
    }

  });
// });



