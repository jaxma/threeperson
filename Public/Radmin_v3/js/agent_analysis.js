 // 日期格式化
 Date.prototype.format = function(format) {
     let o = {
         "M+": this.getMonth() + 1, //月份
         "d+": this.getDate(), //日
         "H+": this.getHours(), //小时
         "m+": this.getMinutes(), //分
         "s+": this.getSeconds(), //秒
         "q+": Math.floor((this.getMonth() + 3) / 3), //季度
         "f+": this.getMilliseconds() //毫秒
     };
     if(/(y+)/.test(format))
         format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
     for(let k in o)
         if(new RegExp("(" + k + ")").test(format))
             format = format.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
     return format;
 };

 var oTimeArea = '';

 var date = new Date();
 var oOneDayTime = 24 * 60 * 60 * 1000;
 var oThreeDayTime = oOneDayTime * 2;
 var oOneWeekTime = oOneDayTime * 6;
 var oTwoWeekTime = oOneWeekTime * 2;
 var oOneMonthTime = oOneDayTime * 29;
 var oThreeMonthTime = beforeThreeMonth();
 var oNowTime = date.getTime();
 var oTimeStart = '';
 var oTimeEnd = '';

 //订单类别的基础属性
 var orderTemp1 = {
         type: 'pie',
         center: ['88%', '28%'],
         radius: ['25%', '30%'],
         avoidLabelOverlap: false,
         tooltip: {
             trigger: 'item'
         },
         label: {
             normal: {
                 position: 'center'
             }
         },
         data: []
     },
     orderTemp2 = {
         type: 'pie',
         center: ['88%', '74%'],
         radius: ['25%', '30%'],
         avoidLabelOverlap: false,
         tooltip: {
             trigger: 'item'
         },
         label: {
             normal: {
                 position: 'center'
             }
         },
         data: []
     };
 $(function() {

     require(['echarts'], function(echarts) {
         //代理城市分布图
         var myChart1 = echarts.init($('#chart1').get(0));

         var myChart2 = echarts.init($('#chart2').get(0));

         var myChart3 = echarts.init($('#chart3').get(0));

         var myChart4 = echarts.init($('#chart4').get(0));

         var myChart5 = echarts.init($('#chart5').get(0));

         var myChart6 = echarts.init($('#chart6').get(0));

         var myChart7 = echarts.init($('#chart7').get(0));

         var myChart8 = echarts.init($('#chart8').get(0));

         $(window).resize(function() {
             myChart1.resize();
             myChart2.resize();
             myChart3.resize();
             myChart4.resize();
             myChart5.resize();
             myChart6.resize();
             myChart7.resize();
             myChart8.resize();
         });

         // 初始化日历和tab卡
         layui.use(['laydate', 'element'], function() {
             var element = layui.element,
                 laydate = layui.laydate;
             //监听增长趋势监听
             laydate.render({
                 elem: '#time-area1',
                 range: '~',
                 max: 0,
                 value: getFormatNowDay(),
                 done: function(value, date, endDate) {
                     var type = 'day';
                     if(endDate.year - date.year != 0 || Math.abs(endDate.month - date.month) > 3) {
                         type = 'month';
                     } else if(Math.abs(endDate.date - date.date) == 0) {
                         type = 'hours';
                     }
                     var str = date.month.toString().length == 1 ? "0" : "";
                     var str2 = date.date.toString().length == 1 ? "0" : "";
                     var s_time = date.year.toString() + str + date.month.toString() + str2 + date.date.toString();
                     str = endDate.month.toString().length == 1 ? "0" : "";
                     str2 = endDate.date.toString().length == 1 ? "0" : "";
                     var e_time = endDate.year.toString() + str + endDate.month.toString() + str2 + endDate.date.toString();
                     add_trend(myChart1, s_time, e_time, type)
                 }
             });
             //监听地区分析
             laydate.render({
                 elem: '#time-area2',
                 range: '~',
                 value: oTimeArea,
                 max: 0,
                 done: function(value, date, endDate) {
                     var str = date.month.toString().length == 1 ? "0" : "";
                     var str2 = date.date.toString().length == 1 ? "0" : "";
                     var s_time = date.year.toString() + str + date.month.toString() + str2 + date.date.toString();
                     str = endDate.month.toString().length == 1 ? "0" : "";
                     str2 = endDate.date.toString().length == 1 ? "0" : "";
                     var e_time = endDate.year.toString() + str + endDate.month.toString() + str2 + endDate.date.toString();
                     getProvinceRank(myChart4, s_time, e_time);
                 }
             });
             //监听订单分析
             laydate.render({
                 elem: '#time-area3',
                 range: '~',
                 value: oTimeArea,
                 max: 0,
                 done: function(value, date, endDate) {
                     var type = 'day';
                     if(Math.abs(endDate.month - date.month) > 1 || endDate.year - date.year > 0) {
                         type = 'month';
                     }
                     var str = date.month.toString().length == 1 ? "0" : "";
                     var str2 = date.date.toString().length == 1 ? "0" : "";
                     var s_time = date.year.toString() + str + date.month.toString() + str2 + date.date.toString();
                     str = endDate.month.toString().length == 1 ? "0" : "";
                     str2 = endDate.date.toString().length == 1 ? "0" : "";
                     var e_time = endDate.year.toString() + str + endDate.month.toString() + str2 + endDate.date.toString();
                     var _id = $('#agent-name').val();
                     getOrder_trend(myChart5, _id, type, s_time, e_time);
                     getOrderRanking(myChart6, s_time, e_time, type);
                 }
             });
             //监听充值分析
             laydate.render({
                 elem: '#time-area4',
                 range: '~',
                 value: oTimeArea,
                 max: 0,
                 done: function(value, date, endDate) {
                     var type = 'day';
                     var type1 = 'day';
                     if(endDate.year - date.year > 0 || Math.abs(endDate.month - date.month) > 1) {
                         type = 'month';
                         type1 = 'month';
                     } else if(Math.abs(endDate.date - date.date) == 0) {
                         type = 'hours';
                     }
                     var str = date.month.toString().length == 1 ? "0" : "";
                     var str2 = date.date.toString().length == 1 ? "0" : "";
                     var s_time = date.year.toString() + str + date.month.toString() + str2 + date.date.toString();
                     str = endDate.month.toString().length == 1 ? "0" : "";
                     str2 = endDate.date.toString().length == 1 ? "0" : "";
                     var e_time = endDate.year.toString() + str + endDate.month.toString() + str2 + endDate.date.toString();
                     var _id = $('#agent-names').val();
                     var a_type = $('#select-type').val();
                     getApply(myChart7, _id, type, s_time, e_time)
                     getApplyRank(myChart8, type1, a_type, s_time, e_time);
                 }
             });

             //  添加点击范围事件
             $('.btn-wrapper .layui-btn').bind('click', function() {
                 var className = $(this).data('name'),
                     pid = $(this).parent('.btn-wrapper').data('pid'),
                     currentTime = "",
                     type = "day",
                     timeArr = [],
                     a_type = $('#select-type').val();
                 switch(className) {
                     case 'now-day':
                         currentTime = getFormatNowDay();
                         type = 'hours';
                         break;
                     case 'one-day':
                         currentTime = getTimeArea(oOneDayTime);
                         break;
                     case 'three-day':
                         currentTime = getTimeArea(oThreeDayTime);
                         break;
                     case 'one-week':
                         currentTime = getTimeArea(oOneWeekTime);
                         break;
                     case 'two-week':
                         currentTime = getTimeArea(oTwoWeekTime);
                         break;
                     case 'one-month':
                         currentTime = getTimeArea(oOneMonthTime);
                         break;
                     case 'three-month':
                         currentTime = beforeThreeMonth();
                         type = 'month';
                         break;
                     default:
                         break;
                 }

                 $(this).parent('.btn-wrapper').siblings('.time-input').find('input').val(currentTime);
                 timeArr = currentTime.replace(/-/g, '').trim().split(' ~ ');
                 switch($(this).parent('.btn-wrapper').data('pid')) {
                     case 1:
                         add_trend(myChart1, timeArr[0], timeArr[1], type);
                         break;
                     case 2:
                         getAreaDate(myChart2, 'all', '', timeArr[0], timeArr[1]);
                         getAreaDate(myChart3, 'allCity', '', timeArr[0], timeArr[1]);
                         getProvinceRank(myChart4, timeArr[0], timeArr[1]);
                         break;
                     case 3:
                         getOrder_trend(myChart5, $('#agent-name').val(), type, timeArr[0], timeArr[1])
                         getOrderRanking(myChart6, timeArr[0], timeArr[1], type);
                         break;
                     case 4:
                         getApply(myChart7, $('#agent-names').val(), type, timeArr[0], timeArr[1]);
                         getApplyRank(myChart8, type, a_type, timeArr[0], timeArr[1]);
                         break;
                     default:
                         break;
                 }
             })

             //监听代理联动选择
             //一级联动      
             form.on('select(level)', function(data) {
                 var level = data.value;
                 var aim = $('#agent-name');
                 getLevelName(aim, level)
             });
             form.on('select(levels)', function(data) {
                 var level = data.value;
                 var aim = $('#agent-names');
                 getLevelName(aim, level)
             });

             //二级联动
             form.on('select(agentName)', function(data) {
                 var _id = data.value;
                 var timeArr = $('#time-area3').val().replace(/-/g, '').trim().split(' ~ ');
                 getOrder_trend(myChart5, _id, 'day', timeArr[0], timeArr[1])
             })
             form.on('select(agentNames)', function(data) {
                 var _id = data.value;
                 var timeArr = $('#time-area4').val().replace(/-/g, '').trim().split(' ~ ');
                 getApply(myChart7, _id, 'day', timeArr[0], timeArr[1])
             })

             form.on('select(selectType)', function(data) {
                 var a_type = data.value;
                 var timeArr = $('#time-area4').val().replace(/-/g, '').trim().split(' ~ ');
                 getApplyRank(myChart8, 'day', a_type, timeArr[0], timeArr[1]);
             })

             //  重置图表的大小
             element.on('tab', function() {
                 myChart1.resize();
                 myChart2.resize();
                 myChart3.resize();
                 myChart4.resize();
                 myChart5.resize();
                 myChart6.resize();
                 myChart7.resize();
                 myChart8.resize();
             })
             form.render();
         });

         //数据配置
         var myoption1 = option = {
             title: {
                 show: true,
                 text: '代理增长趋势图',
                 left: 'center'
             },
             tooltip: {
                 trigger: 'axis'
             },
             legend: {
                 data: [],
                 bottom: 0
             },
             grid: {
                 left: '3%',
                 right: '4%',
                 bottom: '8%',
                 containLabel: true
             },
             toolbox: {
                 feature: {
                     saveAsImage: {}
                 }
             },
             xAxis: {
                 type: 'category',
                 boundaryGap: false,
                 data: [],
                 triggerEvent:true
             },
             yAxis: {
                 type: 'value'
             },
             series: []
         };

         var myoption2 = option = {
             title: {
                 show: true,
                 text: '代理省级地区分布图',
                 textStyle: {
                     color: '#fff',
                     align: 'center',
                     width: 300
                 },
                 padding: [5, 200],
                 left: 'center',
                 backgroundColor: '#1799f6'
             },
             tooltip: {
                 trigger: 'item',
                 formatter: "{a} <br/>{b}: {c} ({d}%)"
             },
             legend: {
                 orient: 'horizontal',
                 x: 'center',
                 y: 'bottom'
             },
             series: [{
                 name: '代理分布',
                 type: 'pie',
                 radius: ['50%', '70%'],
                 avoidLabelOverlap: false,
                 label: {
                     emphasis: {
                         show: true,
                         textStyle: {
                             fontSize: '30',
                             fontWeight: 'bold'
                         }
                     }
                 },
                 labelLine: {
                     normal: {
                         show: true
                     }
                 }
             }]
         };

         var myoption3 = option = {
             title: {
                 show: true,
                 text: '代理市级地区分布图',
                 textStyle: {
                     color: '#fff',
                     align: 'center',
                     width: 300
                 },
                 padding: [5, 200],
                 left: 'center',
                 backgroundColor: '#1799f6'
             },
             tooltip: {
                 trigger: 'item',
                 formatter: "{a} <br/>{b}: {c} ({d}%)"
             },
             legend: {
                 orient: 'horizontal',
                 x: 'center',
                 y: 'bottom',
                 data: []
             },
             series: [{
                 name: '访问来源',
                 type: 'pie',
                 radius: ['50%', '70%'],
                 avoidLabelOverlap: false,
                 label: {
                     emphasis: {
                         show: true,
                         textStyle: {
                             fontSize: '30',
                             fontWeight: 'bold'
                         }
                     }
                 },
                 labelLine: {
                     normal: {
                         show: true
                     }
                 },
                 data: []
             }]
         };

         var myoption4 = option = {
             baseOption: {
                 title: {
                     show: true,
                     text: '代理省级排行',
                     textStyle: {
                         color: '#fff',
                         align: 'center',
                         width: 300
                     },
                     padding: [5, 200],
                     left: 'center',
                     backgroundColor: '#1799f6'
                 },
                 legend: {
                     x: 'center',
                     y: 'bottom',
                     padding: [50, 0, 0, 0],
                 },
                 calculable: true,
                 grid: {
                     top: 80,
                     bottom: 80,
                     tooltip: {
                         trigger: 'axis',
                         axisPointer: {
                             type: 'shadow',
                             label: {
                                 show: true,
                                 formatter: function(params) {
                                     return params.value.replace('\n', '');
                                 }
                             }
                         }
                     }
                 },
                 xAxis: [{
                     type: 'category',
                     axisLabel: {
                         'interval': 0
                     },
                     splitLine: {
                         show: false
                     },
                     name: '省'
                 }],
                 yAxis: [{
                     type: 'value',
                     name: '人数（个）'
                 }],
             },
         };

         var myoption5 = option = {
             color: ['#ffd285', '#ff733f', '#ec4863'],

             title: [{
                 text: '代理订单金额趋势图',
                 left: '32%',
                 top: '4%'
             }, {
                 text: '个人订单类别占比',
                 left: '88%',
                 top: '4%',
                 textAlign: 'center'
             }, {
                 text: '团队订单类别占比',
                 left: '88%',
                 top: '50%',
                 textAlign: 'center'
             }],
             tooltip: {
                 trigger: 'axis'
             },
             legend: {
                 x: '30%',
                 bottom: '0',
                 y: 'bottom',
                 data: ['个人订单金额', '团队订单金额']
             },
             grid: {
                 left: '1%',
                 right: '25%',
                 top: '16%',
                 bottom: '10%',
                 containLabel: true
             },
             //  toolbox: {
             //      "show": false,
             //      feature: {
             //          saveAsImage: {}
             //      }
             //  },
             xAxis: {
                 type: 'category',
                 "axisLine": {
                     lineStyle: {
                         color: '#FF4500'
                     }
                 },
                 "axisTick": {
                     "show": false
                 },
                 boundaryGap: false,
                 data: [],
                 triggerEvent:true
             },
             yAxis: {
                 "axisLine": {
                     lineStyle: {
                         color: '#FF4500'
                     }
                 },
                 splitLine: {
                     show: true
                 },
                 "axisTick": {
                     "show": false
                 },
                 type: 'value'
             },
             series: []
         }

         var myoption6 = option = {
             title: {
                 show: true,
                 text: '代理订单金额排行',
                 textStyle: {
                     color: '#fff',
                     align: 'center',
                     width: 300
                 },
                 padding: [5, 200],
                 left: 'center',
                 backgroundColor: '#1799f6'
             },
             color: ['#00ccff', '#2f4554', '#61a0a8', '#d48265', '#91c7ae', '#749f83', '#ca8622', '#bda29a', '#6e7074', '#546570', '#c4ccd3'],
             tooltip: {
                 trigger: 'axis',
                 axisPointer: { // 坐标轴指示器，坐标轴触发有效
                     type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
                 }
             },
             grid: {
                 left: '3%',
                 right: '4%',
                 bottom: '3%',
                 containLabel: true
             },
             legend: {
                 data: []
             },
             xAxis: {
                 type: 'value'
             },
             yAxis: {
                 type: 'category',
                 data: []
             },
             series: [{
                 name: '个人数量',
                 type: 'bar',
                 stack: '总量',
                 label: {
                     normal: {
                         show: true,
                         position: 'insideTopRight'
                     }
                 },
                 data: []
             }]
         };

         var myoption7 = option = {
             title: {
                 show: true,
                 text: '代理充值趋势',
                 textStyle: {
                     color: '#fff',
                     align: 'center',
                     width: 300
                 },
                 padding: [5, 200],
                 left: 'center',
                 backgroundColor: '#1799f6'
             },
             tooltip: {
                 trigger: 'axis'
             },
             legend: {
                 right: 'center',
                 bottom: '0',
                 data: ['代理人数']
             },
             grid: {
                 left: '3%',
                 right: '4%',
                 bottom: '7%',
                 containLabel: true
             },
             xAxis: {
                 type: 'category',
                 boundaryGap: false,
                 data: [],
                 triggerEvent:true
             },
             yAxis: {
                 type: 'value'
             },
             series: [{
                 name: '代理人数',
                 type: 'line',
                 symbolSize: 8,
                 stack: '总量',
                 data: []
             }]
         }

         var myoption8 = option = {
             title: {
                 show: true,
                 text: '代理充值排行',
                 textStyle: {
                     color: '#fff',
                     align: 'center',
                     width: 300
                 },
                 padding: [5, 200],
                 left: 'center',
                 backgroundColor: '#1799f6'
             },
             legend: {
                 data: []
             },
             color: ['#00ccff', '#2f4554', '#61a0a8', '#d48265', '#91c7ae', '#749f83', '#ca8622', '#bda29a', '#6e7074', '#546570', '#c4ccd3'],
             tooltip: {
                 trigger: 'axis',
                 axisPointer: { // 坐标轴指示器，坐标轴触发有效
                     type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
                 }
             },
             grid: {
                 left: '3%',
                 right: '4%',
                 bottom: '3%',
                 containLabel: true
             },
             xAxis: {
                 type: 'value'
             },
             yAxis: {
                 type: 'category',
                 show: true,
                 data: []
             },
             series: [{
                 name: '金额',
                 type: 'bar',
                 stack: '总量',
                 label: {
                     normal: {
                         show: true,
                         position: 'insideTopRight'
                     }
                 },
                 data: []
             }]
         };
         //初始化图表
         myChart1.setOption(myoption1);
         myChart2.setOption(myoption2);
         myChart3.setOption(myoption3);
         myChart4.setOption(myoption4);
         myChart5.setOption(myoption5);
         myChart6.setOption(myoption6);
         myChart7.setOption(myoption7);
         myChart8.setOption(myoption8);

         //初始化下拉框和日期的数据

         var level1 = $('#agent-level');
         var level2 = $('#agent-levels');
         var name1 = $('#agent-name');
         var name2 = $('#agent-names')
         getLevelName(name1, level1.val()); //第一个下拉联动框
         getLevelName(name2, level2.val()); //第二个下拉联动框

         $('#time-area1').val(getFormatNowDay()); //日期范围选择器
         $('#time-area2').val(getFormatNowDay());
         $('#time-area3').val(getFormatNowDay());
         $('#time-area4').val(getFormatNowDay());

         //初始化表的数据
         add_trend(myChart1, getNowDay(), getNowDay(), 'hours')
         $('.layui-tab-title li').one('click', function() {
             var index = $(this).index() + 1;
             if(index == 2) {
                 getAreaDate(myChart2, 'all', '', '', '');
                 getAreaDate(myChart3, 'allCity', '', '', '');
                 getProvinceRank(myChart4, getNowDay(), getNowDay());
             } else if(index == 3) {
                 getOrder_trend(myChart5, $('#agent-name').val(), 'day', getNowDay(), getNowDay())
                 getOrderRanking(myChart6, getNowDay(), getNowDay(), 'month');
             } else if(index == 4) {
                 getApply(myChart7, $('#agent-names').val(), 'hours', getNowDay(), getNowDay());
                 getApplyRank(myChart8, 'day', '', getNowDay(), getNowDay());
             }
         });

         //监听订单表X轴的点击事件
         myChart5.on('click',function(params) {
                 if(params.componentType =="series"&& new RegExp(/[\u4e00-\u9fa5]/).test(params.name)) {
                    var _year = (params.name).substring(0,4);
                    var _month = (params.name).substring(5,7);
                    var  time = new Date(_year,_month,0);
                    var daycount = time.getDate();
                    var _start_month = (params.name).substring(0,4)+(params.name).substring(5,7)+'01';
                    var _end_month = (params.name).substring(0,4)+(params.name).substring(5,7)+daycount;
                    getOrder_trend(this,$('#agent-name').val(),'day',_start_month,_end_month);
                 }
             });
        myChart1.on('click',function(params) {
                 if(params.componentType =="series"&& new RegExp(/[\u4e00-\u9fa5]/).test(params.name)) {
                    var _year = (params.name).substring(0,4);
                    var _month = (params.name).substring(5,7);
                    var  time = new Date(_year,_month,0);
                    var daycount = time.getDate();
                    var _start_month = (params.name).substring(0,4)+(params.name).substring(5,7)+'01';
                    var _end_month = (params.name).substring(0,4)+(params.name).substring(5,7)+daycount;
                    add_trend(this,_start_month,_end_month,'day');
                 }
             });
        myChart7.on('click',function(params) {
                 if(params.componentType =="series"&& new RegExp(/[\u4e00-\u9fa5]/).test(params.name)) {
                    var _year = (params.name).substring(0,4);
                    var _month = (params.name).substring(5,7);
                    var  time = new Date(_year,_month,0);
                    var daycount = time.getDate();
                    var _start_month = (params.name).substring(0,4)+(params.name).substring(5,7)+'01';
                    var _end_month = (params.name).substring(0,4)+(params.name).substring(5,7)+daycount;
                    getApply(this,$('#agent-name').val(),'day',_start_month,_end_month);
                 }
             });     
        
     })
 })

 //代理趋势增长
 function add_trend(aim, s_time, e_time, type) {
     $.post(add_dis_trend, {
         start_time: s_time,
         end_time: e_time,
         type: type,
         is_show_level: 1
     }, function(data) {
         //  console.log(data);
         if(data.code != 1) {
             console.log(data.msg);
             return;
         }
         if(data.info.list == "" || data.info.list == undefined || data.info.list == null) {
             console.log('暂无数据!');
             return;
         }
         var _legend = [],
             _xData = [],
             _series = [],
             count = 1;
         $.each(data.info.list, function(key, value) {
             _legend.push(key);
             var _tData = [];
             $.each(value, function(k, val) {
                 if(count === 1) {
                     _xData.push(k);
                 }
                 _tData.push(val);
             });
             count++;
             var temp = {
                 name: key,
                 type: 'line',
                 data: _tData
             }
             _series.push(temp)
         });
         aim.setOption({
             legend: {
                 data: _legend,
                 bottom: 0
             },
             xAxis: {
                 type: 'category',
                 boundaryGap: false,
                 data: _xData,
                 triggerEvent:true
             },
             series: _series
         })
     })
 }

 //获取代理订单金额分析
 function getOrder_trend(aim, id, type, s_time, e_time) {
     $.post(getOrderMoneyurl, {
         dis_id: id,
         type: type,
         start_time: s_time,
         end_time: e_time
     }, function(data) {
         if(data.code != 1) {
             console.log(data.msg);
             return;
         }
         //  console.log(data);
         var datax = [],
             self_data = {
                 name: '个人订单金额',
                 smooth: true,
                 type: 'line',
                 symbolSize: 8,
                 symbol: 'circle',
                 data: []
             },
             team_data = {
                 name: '团队订单金额',
                 smooth: true,
                 type: 'line',
                 symbolSize: 8,
                 symbol: 'circle',
                 data: []
             };

         getOrderTypeRadio(id, type, s_time, e_time);
         $.each(data.self_info.count, function(k, v) {
             self_data.data.push(v);
         });
         $.each(data.self_info.day, function(k, v) {
             datax.push(v);
         });
         $.each(data.team_info.count, function(k, v) {
             team_data.data.push(v);
         });
         aim.setOption({
             xAxis: {
                 data: datax,
                 triggerEvent:true
             },
             series: [self_data, team_data, orderTemp1, orderTemp2]
         })
     })
 }

 //获取订单类别占比
 function getOrderTypeRadio(id, type, s_time, e_time) {
     $.ajax({
         url: getOrderTypeRadios,
         async: false,
         data: {
             dis_id: id,
             start_time: s_time,
             end_time: e_time,
             type: type
         },
         success: function(data) {
             if(data.code != 1) {
                 console.log(data.msg);
                 return;
             } else {
                 var tdata1 = [],
                     tdata2 = [];

                 //获取个人
                 if(!(data.myself_info == null || data.myself_info == undefined || data.myself_info == "")) {
                     $.each(data.myself_info, function(kay, value) {
                         if(value.templet == null || value.templet == undefined || value.templet == "") {
                             return;
                         }
                         tdata1.push({
                             value: value.buy_money,
                             name: value.templet.name,
                             label: {
                                 normal: {
                                     show: false
                                 }
                             }
                         })
                         for(var i = 0; i < tdata1.length - 1; i++) {
                             for(var j = 0; j < tdata1.length - 1 - i; j++) {
                                 if(tdata1[j + 1].value > tdata1[j].value) {
                                     var temp = tdata1[j];
                                     tdata1[j] = tdata1[j + 1];
                                     tdata1[j + 1] = temp;
                                 }
                             }
                         }
                     });
                     var str = tdata1[0].name;
                     tdata1[0].label.normal.formatter = '{d} %\n' + str;
                     tdata1[0].label.normal.show = true;
                     tdata1[0].label.normal.fontSize = 20;
                     orderTemp1.tooltip.show = true;
                     orderTemp1.data.hoverAnimation = true;
                 } else {
                     tdata1.push({
                         value: 0,
                         name: '暂无数据',
                         itemStyle: {
                             normal: {
                                 color: '#c9c9c9'
                             }
                         },
                         label: {
                             normal: {
                                 position: 'center',
                                 textStyle: {
                                     color: '#c9c9c9',
                                 }
                             }
                         },
                         hoverAnimation: false
                     })
                     orderTemp1.tooltip.show = false;
                     orderTemp1.label.normal.show = true;
                 }
                 //获取团队
                 if(!(data.team_info == null || data.team_info == undefined || data.team_info == "")) {
                     $.each(data.team_info, function(kay, value) {
                         if(value.templet == null || value.templet == undefined || value.templet == "") {
                             return;
                         }
                         tdata2.push({
                             value: value.buy_money,
                             name: value.templet.name,
                             label: {
                                 normal: {
                                     show: false
                                 }
                             }
                         })
                         for(var i = 0; i < tdata2.length - 1; i++) {
                             if(tdata2[i + 1].value > tdata2[i].value) {
                                 var temp = tdata2[i];
                                 tdata2[i] = tdata2[i + 1];
                                 tdata2[i + 1] = temp;
                             }
                         }
                     });
                     //         tdata2[0].label.normal.padding = [30, 0, 0, 0];
                     var str = tdata2[0].name;
                     tdata2[0].label.normal.formatter = '{d} %\n' + str;
                     tdata2[0].label.normal.show = true;
                     tdata2[0].label.normal.fontSize = 20;
                     orderTemp2.tooltip.show = true;
                     orderTemp2.data.hoverAnimation = true;
                 } else {
                     tdata2.push({
                         value: 0,
                         name: '暂无数据',
                         itemStyle: {
                             normal: {
                                 color: '#c8c8c8'
                             }
                         },
                         label: {
                             normal: {
                                 position: 'center',
                                 textStyle: {
                                     color: '#c8c8c8',
                                 }
                             }
                         },
                         hoverAnimation: false
                     })
                     orderTemp2.tooltip.show = false;
                     orderTemp2.label.normal.show = true;
                 }
                 orderTemp1.data = tdata1;
                 orderTemp2.data = tdata2;
             }
         }
     })
 }

 // 获取地区分布
 function getAreaDate(aim, type, area, s_time, e_time) {
     $.post(getAreadata, {
         type: type,
         area: area,
         start_time: s_time,
         end_time: e_time
     }, function(data) {
         //  console.log(data)
         if(data.code != 1 || data.count == null || data.count == undefined || data.count == "") {
             console.log(data.msg);
             return false;
         }
         var dataX = [],
             _series = [],
             count = 1;
         $.each(data.count, function(key, value) {
             if(type != "all" && count > 10) {
                 return;
             }
             dataX.push(key);
             _series.push({
                 name: key,
                 value: value
             });
             count++;
         })
         aim.setOption({
             legend: {
                 data: dataX
             },
             series: [{
                 data: _series
             }]
         });
     })
 }

 //  获取当前日期
 function getNowDay() {
     var _time = new Date();
     var _year = _time.getFullYear().toString();
     var _month = (_time.getMonth() + 1).toString();
     var _day = _time.getDate().toString();
     return _year + ((_time.getMonth() + 1).toString().length > 1 ? '' : '0') + _month + ((_time.getDate()).toString().length > 1 ? '' : '0') + _day;
 }

 //  日期转换格式
 function getFormatNowDay() {
     //2017-12-06 ~ 2018-01-11
     var _time = new Date();
     var _year = _time.getFullYear().toString();
     var _month = (_time.getMonth() + 1).toString();
     var _day = _time.getDate().toString();
     var str = _year + '-' + ((_time.getMonth() + 1).toString().length > 1 ? '' : '0') + _month + '-' + ((_time.getDate()).toString().length > 1 ? '' : '0') + _day;
     str += ' ~ ' + str;
     return str;
 }

 //  获取当前时间的前三个月
 function beforeThreeMonth() {
     var time = new Date();
     var nowDateFirstDay = new Date(time.getFullYear(), time.getMonth(), 1);
     var preMonthLastDay = new Date(nowDateFirstDay - 1000 * 60 * 60 * 24).format("yyyy-MM-dd");
     var preThreeMonth = new Date(time.getFullYear(), (time.getMonth() - 3), 1).format("yyyy-MM-dd");
     return preThreeMonth + ' ~ ' + preMonthLastDay;
 }

 // 时间范围
 function getTimeArea(timeLimit) {
     oTimeStart = new Date(parseInt(oNowTime - timeLimit)).format("yyyy-MM-dd");
     oTimeEnd = new Date(parseInt(oNowTime)).format("yyyy-MM-dd");
     return oTimeArea = oTimeStart + ' ~ ' + oTimeEnd;
 }

 //获取代理省级排行数据
 function getProvinceRank(aim, s_time, e_time) {
     $.post(getProvinceRanks, {
         start_time: s_time,
         end_time: e_time
     }, function(data) {
         //  $.post(getProvinceRanks,{start_time:20171201,end_time:20171201},function(data){
         if(data.code != 1) {
             console.log(data.msg)
             return;
         } else {
             var dataX = [],
                 dataL = [],
                 _series = [];

             $.each(data.province, function(key, value) {
                 if(key % 2 != 0) {
                     dataX.push('\n' + value);
                 } else {
                     dataX.push(value);
                 }
             });
             $.each(data.info, function(key, value) {
                 var oTemp = {
                     name: key,
                     type: 'bar',
                     data: value
                 }
                 _series.push(oTemp);
                 dataL.push(key)
             });

             //   console.log(_series)
             //   console.log(dataL)
             //   console.log(dataX)
             //更新图表数据
             aim.setOption({
                 legend: {
                     data: dataL
                 },
                 xAxis: [{
                     data: dataX
                 }],
                 series: _series
             })
         }
     })
 }

 //获取订单业绩排行
 function getOrderRanking(aim, s_time, e_time, type) {
     $.post(getOrderRanks, {
         start_time: s_time,
         end_time: e_time,
         type: type
     }, function(data) {

         if(data.code != 1) {
             console.log(data.msg);
             return
         } else {
             //    console.log(data);
             var dataX = [],
                 _series = [],
                 count = 1,
                 color = ['#00ccff', '#2f4554', '#61a0a8', '#d48265', '#91c7ae', '#749f83', '#ca8622', '#bda29a', '#6e7074', '#546570', '#c4ccd3'];
             $.each(data.list, function(key, value) {
                 if(count > 10 || value == null) {
                     return;
                 }
                 dataX.push(value.name);
                 _series.push({
                     value: key,
                     itemStyle: {
                         normal: {
                             color: color[count]
                         }
                     }
                 });
                 count++;
             });
             dataX = dataX.reverse();
             _series = _series.reverse();
             //    console.log(dataX)
             //    console.log(_series)
             aim.setOption({
                 legend: {
                     data: dataX
                 },
                 yAxis: {
                     data: dataX
                 },
                 series: [{
                     name: '个人数量',
                     type: 'bar',
                     stack: '总量',
                     label: {
                         normal: {
                             show: true,
                             position: 'insideTopRight'
                         }
                     },
                     data: _series
                 }]
             })
         }
     })
 }

 //获取充值分析趋势数据
 function getApply(aim, dis_id, type, s_time, e_time) {
     $.post(getDisApply, {
         start_time: s_time,
         end_time: e_time,
         type: type,
         dis_id: dis_id
     }, function(data) {
         //$.post(getDisApply, {
         //  start_time: 20171130,
         //  end_time: 20171204,
         //  type: 'day',
         //  dis_id: 6
         //}, function(data) {
         //  console.log(data);
         if(data.code != 1) {
             console.log(data.msg)
             return;
         } else {
             var dataX = [],
                 _series = [],
                 dataL = [],
                 flag = true;

             $.each(data.list, function(key, value) {
                 var datas = [];
                 $.each(value, function(k, v) {
                     if(flag) {
                         dataX.push(k);
                     }
                     datas.push(v);
                 })
                 _series.push({
                     name: key,
                     type: 'line',
                     symbolSize: 8,
                     data: datas
                 })
                 dataL.push(key)
                 flag = false;
             });

             aim.setOption({
                 legend: {
                     data: dataL
                 },
                 xAxis: {
                     data: dataX,
                     triggerEvent:true
                 },
                 series: _series
             })
         }
     })
 }

 //获取充值排行数据
 function getApplyRank(aim, type, a_type, s_time, e_time) {
     $.post(getApplyRanks, {
         type: type,
         apply_type: a_type,
         start_time: s_time,
         end_time: e_time
     }, function(data) {
         //$.post(getApplyRanks,{type:'day',apply_type:'',start_time:20171130,end_time:20171204},function(data){
         console.log(data)
         if(data.code != 1) {
             console.log(data.msg)
             return;
         } else {
             var dataX = [],
                 color = ['#00ccff', '#2f4554', '#61a0a8', '#d48265', '#91c7ae', '#749f83', '#ca8622', '#bda29a', '#6e7074', '#546570', '#c4ccd3'],
                 _series = [],
                 count = 0;
             if(!(data.list == null || data.list == "" || data.list == undefined)) {
                 $.each(data.list, function(key, value) {
                     if(count < 10) {
                         _series.push({
                             value: value.money,
                             itemStyle: {
                                 normal: {
                                     color: color[count]
                                 }
                             }
                         })
                         dataX.push(value.dis_name);
                         count++;
                     } else {
                         return;
                     }
                 });
             } else {
                 _series.push({
                     value: 0,
                     itemStyle: {
                         normal: {
                             color: '#c9c9c9'
                         }
                     }
                 })
                 dataX.push('暂无数据');
             }
             _series = _series.reverse();
             dataX = dataX.reverse();
             aim.setOption({
                 legend: {
                     data: dataX
                 },
                 yAxis: {
                     type: 'category',
                     show: true,
                     data: dataX
                 },
                 series: [{
                     data: _series
                 }]
             })
         }
     })
 }

 //获取该等级的代理名称
 function getLevelName(aim, level) {
     $.ajax({
         url: getAgent,
         data: {
             level: level
         },
         async: false,
         success: function(data) {
             if(data != 'none') {
                 var text = '';
                 $.each(data, function(index, array) {
                     text += '<option class="' + array["id"] + '" value="' + array["id"] + '">' + array["name"] + '</option>';
                 })
                 aim.html(text);
             } else {
                 return;
             }
             form.render()
         }
     })
 }