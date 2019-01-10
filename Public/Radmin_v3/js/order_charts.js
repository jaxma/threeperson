$(document).ready(function () {
    var date = new Date();
    var oOneDayTime = 24 * 60 * 60 * 1000;
    var oThreeDayTime = oOneDayTime * 2;
    var oOneWeekTime = oOneDayTime * 6;
    var oTwoWeekTime = oOneDayTime * 13;
    var oOneMonthTime = oOneDayTime * 29;
    var oNowTime = date.getTime(); // 当前时间
    var oTimeStart = ''; // 开始时间 2017-11-10
    var oTimeEnd = ''; // 结束时间 2017-11-10
    var oTimeArea = ''; //时间范围 2017-11-10 ~ 2017-11-10
    var filterTimeStart = ''; // 格式化开始时间,为上传后台用的 20171110
    var filterTimeEnd = '';// 格式化结束时间,为上传后台用的 20171110
    var productId = 1; // 产品ID

    require(['echarts'], function (echarts) {
        var option1 = {},
            option2 = {},
            option3 = {},
            option4 = {},
            option5 = {};
        var myChart1 = echarts.init($('#chart1').get(0));
        var myChart2 = echarts.init($('#chart2').get(0));
        var myChart3 = echarts.init($('#chart3').get(0));
        var myChart4 = echarts.init($('#chart4').get(0));
        var myChart5 = echarts.init($('#chart5').get(0));

        // 产品销量和销售额排行榜参数
        option1 = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'cross',
                    crossStyle: {
                        color: '#333'
                    }
                }
            },
            legend: {
                data: ['销售额', '销量']
            },
            grid: {
                top: 100
            },
            xAxis: [{
                type: 'category',
                axisPointer: {
                    type: 'shadow'
                },
                axisLine: {
                    // onZero: false,
                    lineStyle: {
                        color: '#333'
                    }
                }
            }],
            yAxis: [{
                    type: 'value',
                    name: '销售额',
                    axisLine: {
                        // onZero: false,
                        lineStyle: {
                            color: '#333'
                        }
                    }
                },
                {
                    type: 'value',
                    name: '销量',
                    axisLine: {
                        onZero: false,
                        lineStyle: {
                            color: '#333'
                        }
                    }
                },
            ],
            series: [{
                    name: '销售额',
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            color: '#28bb9c'
                        }
                    }
                },
                {
                    name: '销量',
                    type: 'line',
                    yAxisIndex: 1,
                    itemStyle: {
                        normal: {
                            color: '#009688'
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: '#009688'
                        }
                    }
                }
            ]
        };

        // 产品销量趋势图参数
        option2 = {
            tooltip: {
                show: true,
                trigger: 'axis',
                axisPointer: {
                    type: 'cross'
                }
            },
            legend: {
                type: 'scroll',
                width: '80%'
            },
            grid: {
                top: 100,
                bottom: 50,
                containLabel: true
            },
            xAxis: [{
                type: 'category',
                axisTick: {
                    alignWithLabel: true
                },
                axisLine: {
                    onZero: false,
                    lineStyle: {
                        color: '#333'
                    }
                }
            }],
            yAxis: [{
                type: 'value',
                axisLine: {
                    onZero: false,
                    lineStyle: {
                        color: '#333'
                    }
                },
            }]
        };

        // 省级发货地区排行参数
        option3 = {
            title : {
                text: '省发货排行',
                x: 'center',
                backgroundColor: '#1799F6',
                padding: [5, 50],
                textStyle: {
                    color: '#fff'
                }
                
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                type: 'scroll',
                orient: 'vertical',
                height: "60%",
                right: 0,
                top: 80,
                bottom: 10,
                // data: data.legendData
            },
            series : [
                {
                    name: '省发货详情',
                    type: 'pie',
                    radius : '55%',
                    center: ['40%', '50%'],
                    // data: data.seriesData,
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        // 市级发货地区排行参数
        option4 = {
            title : {
                text: '市发货排行',
                x: 'center',
                backgroundColor: '#1799F6',
                padding: [5, 50],
                textStyle: {
                    color: '#fff'
                }
                
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                type: 'scroll',
                orient: 'vertical',
                height: "60%",
                right: 0,
                top: 80,
                bottom: 10,
                // data: data.legendData
            },
            series : [
                {
                    name: '市发货详情',
                    type: 'pie',
                    radius : '55%',
                    center: ['40%', '50%'],
                    // data: data.seriesData,
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        // 区级发货地区排行参数
        option5 = {
            title : {
                text: '区发货排行',
                x: 'center',
                backgroundColor: '#1799F6',
                padding: [5, 50],
                textStyle: {
                    color: '#fff'
                }
                
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                type: 'scroll',
                orient: 'vertical',
                height: "60%",
                right: '30%',
                top: 120,
                bottom: 10,
                // data: data.legendData
            },
            series : [
                {
                    name: '区发货详情',
                    type: 'pie',
                    radius : '55%',
                    center: ['40%', '50%'],
                    // data: data.seriesData,
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        
        // 渲染图表
        myChart1.setOption(option1);
        myChart2.setOption(option2);
        myChart3.setOption(option3);
        myChart4.setOption(option4);
        myChart5.setOption(option5);
        
        layui.use(['element', 'laydate'], function () {
            var element = layui.element;
            var laydate = layui.laydate;

            // 渲染日期选择器
            laydate.render({
                elem: '#time-area1',
                range: '~',
                done: function (value) {
                    var _value = value.split('~').map(function (item) {
                        return item.trim()
                    });
                    oTimeStart = _value[0];
                    oTimeEnd = _value[1];
                    filterTimeStart = oTimeStart.split('-').join('');
                    filterTimeEnd = oTimeEnd.split('-').join('');
                    getProduct( myChart1, filterTimeStart, filterTimeEnd, productId, 'day');
                }
            });

            laydate.render({
                elem: '#time-area2',
                range: '~',
                done: function (value) {
                    var _value = value.split('~').map(function (item) {
                        return item.trim()
                    });
                    oTimeStart = _value[0];
                    oTimeEnd = _value[1];
                    filterTimeStart = oTimeStart.split('-').join('');
                    filterTimeEnd = oTimeEnd.split('-').join('');
                    saleAnalysis(myChart2, filterTimeStart, filterTimeEnd, 'day');
                }
            });
            laydate.render({
                elem: '#time-area3',
                range: '~',
                done: function (value) {
                    var _value = value.split('~').map(function (item) {
                        return item.trim()
                    });
                    oTimeStart = _value[0];
                    oTimeEnd = _value[1];
                    filterTimeStart = oTimeStart.split('-').join('');
                    filterTimeEnd = oTimeEnd.split('-').join('');
                    getProvinceData(myChart3, filterTimeStart, filterTimeEnd, productId, 'province');
                    getCityData(myChart4, filterTimeStart, filterTimeEnd, productId, 'city');
                    getCountyData(myChart5, filterTimeStart, filterTimeEnd, productId, 'county');
                }
            });

            // 产品下拉框选中产品后所发生的一系列悲剧
            form.on('select', function (data) {
                productId = data.value;
                if (typeof ($('#three-month1').attr('type')) == "undefined") {
                    getProduct(myChart1, filterTimeStart, filterTimeEnd, productId, 'day');
                } else {
                    getProduct(myChart1, filterTimeStart, filterTimeEnd, productId, 'month');
                }

                getProvinceData(myChart3, filterTimeStart, filterTimeEnd, productId, 'province');
                getCityData(myChart4, filterTimeStart, filterTimeEnd, productId, 'city');
                getCountyData(myChart5, filterTimeStart, filterTimeEnd, productId, 'county');
            });

            // 切换tab选项卡后所发生的一系列悲剧
            element.on('tab', function (data) {
                // 切换tab选项卡图表更新宽度
                myChart1.resize();
                myChart2.resize();
                myChart3.resize();
                myChart4.resize();
                myChart5.resize();

                if (data.index == 0) {
                    $('#three-day1').click();
                } else if (data.index == 1) {
                    $('#three-day2').click();
                } else if (data.index == 2) {
                    $('#three-day3').click();
                }

                
                //更新下拉框数据
                getAllProduct();

                getProvinceData(myChart3, filterTimeStart, filterTimeEnd, productId, 'province');
                getCityData(myChart4, filterTimeStart, filterTimeEnd, productId, 'city');
                getCountyData(myChart5, filterTimeStart, filterTimeEnd, productId, 'county');
            });
        });

        // 屏幕宽度改变时所发生的一系列悲剧
        $(window).resize(function () {
            myChart1.resize();
            myChart2.resize();
            myChart3.resize();
            myChart4.resize();
            myChart5.resize();
        })

        //tab1点击不同时间段触发事故
        $('#three-day1').on('click', function () {
            $('#three-month1').removeAttr('type');
            getTimeArea(oThreeDayTime, '#time-area1');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            getProduct( myChart1, filterTimeStart, filterTimeEnd, productId, 'day');
        })
        $('#one-week1').on('click', function () {
            $('#three-month1').removeAttr('type');
            getTimeArea(oOneWeekTime, '#time-area1');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            getProduct( myChart1, filterTimeStart, filterTimeEnd, productId, 'day');
        })
        $('#two-week1').on('click', function () {
            $('#three-month1').removeAttr('type');
            getTimeArea(oTwoWeekTime, '#time-area1');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            getProduct( myChart1, filterTimeStart, filterTimeEnd, productId, 'day');
        })
        $('#one-month1').on('click', function () {
            $('#three-month1').removeAttr('type');
            getTimeArea(oOneMonthTime, '#time-area1');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            getProduct( myChart1, filterTimeStart, filterTimeEnd, productId, 'day');
        })
        $('#three-month1').on('click', function () {
            $(this).attr('type', true);
            getThreeMonthArea(getThreeMonth(), '#time-area1');
            filterTimeStart = getThreeMonth(oTimeEnd)[0].split('-').join('');
            filterTimeEnd = getThreeMonth(oTimeEnd)[1].split('-').join('');
            getProduct( myChart1, filterTimeStart, filterTimeEnd, productId, 'month');
        })

        //tab2点击不同时间段触发事故
        $('#three-day2').on('click', function () {
            getTimeArea(oThreeDayTime, '#time-area2');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            saleAnalysis(myChart2, filterTimeStart, filterTimeEnd, 'day');
        })
        $('#one-week2').on('click', function () {
            getTimeArea(oOneWeekTime, '#time-area2');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            saleAnalysis(myChart2, filterTimeStart, filterTimeEnd, 'day');
        })
        $('#two-week2').on('click', function () {
            getTimeArea(oTwoWeekTime, '#time-area2');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            saleAnalysis(myChart2, filterTimeStart, filterTimeEnd, 'day');
        })
        $('#one-month2').on('click', function () {
            getTimeArea(oOneMonthTime, '#time-area2');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            saleAnalysis(myChart2, filterTimeStart, filterTimeEnd, 'day');
        })
        $('#three-month2').on('click', function () {
            getThreeMonthArea(getThreeMonth(), '#time-area2');
            filterTimeStart = getThreeMonth(oTimeEnd)[0].split('-').join('');
            filterTimeEnd = getThreeMonth(oTimeEnd)[1].split('-').join('');
            saleAnalysis(myChart2, filterTimeStart, filterTimeEnd, 'month');
        })

        //tab3点击不同时间段触发事故
        $('#three-day3').on('click', function () {
            getTimeArea(oThreeDayTime, '#time-area3');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            getProvinceData(myChart3, filterTimeStart, filterTimeEnd, productId, 'province');
            getCityData(myChart4, filterTimeStart, filterTimeEnd, productId, 'city');
            getCountyData(myChart5, filterTimeStart, filterTimeEnd, productId, 'county');
        })
        $('#one-week3').on('click', function () {
            getTimeArea(oOneWeekTime, '#time-area3');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            getProvinceData(myChart3, filterTimeStart, filterTimeEnd, productId, 'province');
            getCityData(myChart4, filterTimeStart, filterTimeEnd, productId, 'city');
            getCountyData(myChart5, filterTimeStart, filterTimeEnd, productId, 'county');
        })
        $('#two-week3').on('click', function () {
            getTimeArea(oTwoWeekTime, '#time-area3');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            getProvinceData(myChart3, filterTimeStart, filterTimeEnd, productId, 'province');
            getCityData(myChart4, filterTimeStart, filterTimeEnd, productId, 'city');
            getCountyData(myChart5, filterTimeStart, filterTimeEnd, productId, 'county');
        })
        $('#one-month3').on('click', function () {
            getTimeArea(oOneMonthTime, '#time-area3');
            filterTimeStart = oTimeStart.split('-').join('');
            filterTimeEnd = oTimeEnd.split('-').join('');
            getProvinceData(myChart3, filterTimeStart, filterTimeEnd, productId, 'province');
            getCityData(myChart4, filterTimeStart, filterTimeEnd, productId, 'city');
            getCountyData(myChart5, filterTimeStart, filterTimeEnd, productId, 'county');
        })
        $('#three-month3').on('click', function () {
            getThreeMonthArea(getThreeMonth(), '#time-area3');
            filterTimeStart = getThreeMonth(oTimeEnd)[0].split('-').join('');
            filterTimeEnd = getThreeMonth(oTimeEnd)[1].split('-').join('');
            getProvinceData(myChart3, filterTimeStart, filterTimeEnd, productId, 'province');
            getCityData(myChart4, filterTimeStart, filterTimeEnd, productId, 'city');
            getCountyData(myChart5, filterTimeStart, filterTimeEnd, productId, 'county');
        })

        // 初始化数据
        getProduct(myChart1, oTimeStart, oTimeEnd, productId, 'day');
        getAllProduct();
        myChart1.resize();
        $('#three-day1').click();
        // $('#three-day2').click();
        // $('#three-day3').click();
    })

    // 获取三个月
    function getThreeMonth() {
        var arr = [];
        var time = new Date();
        var nowDateFirstDay = new Date(time.getFullYear(), time.getMonth(), 1);
        var preMonthLastDay = new Date(nowDateFirstDay - 1000 * 60 * 60 * 24).format("yyyy-MM-dd");
        var preThreeMonth = new Date(time.getFullYear(), (time.getMonth() - 3), 1).format("yyyy-MM-dd");
        arr.push(preThreeMonth);
        arr.push(preMonthLastDay);
        return arr;
    }

    // 时间范围
    function getTimeArea(timeLimit, id) {
        oTimeStart = new Date(parseInt(oNowTime - timeLimit)).format("yyyy-MM-dd");
        oTimeEnd = new Date(parseInt(oNowTime)).format("yyyy-MM-dd");
        oTimeArea = oTimeStart + ' ~ ' + oTimeEnd;
        $(id).val(oTimeArea);
    }

    // 时间范围
    function getThreeMonthArea(data, id) {
        oTimeArea = data[0] + ' ~ ' + data[1];
        $(id).val(oTimeArea);
    }
})

// 产品销量趋势数据
function saleAnalysis(chart, start_time, end_time, type) {
    $.post(getSaleAnalysis, {
        start_time: start_time,
        end_time: end_time,
        type: type
    }, function (data) {
        // console.log(data)
        if (data.code == 1) {
            var oDataList = data.list.list;
            var oNameList = [];
            var seriesTemp = [];
            var oDateList = [];
            for (index in oDataList) {
                oNameList.push(index);
                var _oDateList = [];
                var _oMoneyList = [];
                var _seriesTemp = {};
                for (key in oDataList[index]) {
                    _oDateList.push(key);
                    _oMoneyList.push(oDataList[index][key]);
                }
                _seriesTemp = {
                    name: index,
                    type: 'line',
                    stack: '总量',
                    data: _oMoneyList,
                }
                seriesTemp.push(_seriesTemp);
                if (type == 'day') {
                    // 去除小于10的日期前的0
                    oDateList = _oDateList.map(function (item) {
                        if (item < 10 && item > 0) {
                            return item.substring(1, 2);
                        } else {
                            return item;
                        }
                    })
                } else if(type == 'month') {
                    oDateList = _oDateList
                }
            }
            chart.setOption({
                legend: {
                    data: oNameList
                },
                series: seriesTemp,
                xAxis: [{
                    data: oDateList
                }]
            })
        }
    })
}

// 获取全部产品
function getAllProduct() {
    $.ajax({
        url: getAllProductData,
        type: 'post',
        dataType: 'json',
        success: function (data) {
            var html = '<select name="city" lay-verify="" lay-search lay-filter="select1">'
                            '<option value="">请选择或搜索产品</option>';
            $(data).each(function (index, value){
                html += '<option value="'+ value.id +'">'+ value.name +'</option>'

            })
            html += '</select>';
            $('.get-product').empty().append(html)
            form.render();
        }
    })
}

// 获取产品数据
function getProduct(chart, startTime, endTime, id, type) {
    $.ajax({
        url: getProductData,
        type: 'post',
        dataType: 'json',
        data: { 
            start_time: startTime,
            end_time: endTime,
            templet_id: id,
            type: type
        },
        success: function (data) {
            chart.setOption({
                xAxis: [{
                    data: data.day
                }],
                series: [
                    {
                        data: data.sale_money
                    },
                    {
                        data: data.sale_num
                    }
                ]
            })
        }
    })
}

// 产品发货省级排行
function getProvinceData(chart, startTime, endTime, id, type) {
    $.ajax({
        url: getSaleAreaData,
        type: 'post',
        dataType: 'json',
        data: { 
            start_time: startTime,
            end_time: endTime,
            templet_id: id,
            type: type
        },
        success: function (data) {
            if (data.code == 1) {
                var oList = data.info.list;
                var aNameList = [];
                var aCountList = [];
                // console.log(oList);
                for (key in oList) {
                    var count = 0;
                    aNameList.push(key);
                    oList[key].forEach(function (value) {
                        // console.log(value)
                        count += parseInt(value.total_num);
                    })
                    aCountList.push({
                        name: key,
                        value: count 
                    });
                    // console.log(aCountList.length)
                }
                if (aCountList.length == 0) {
                    chart.setOption({
                        series : {
                            data: [{name: '没有数据',value: 1}],
                        }
                    })
                } else {
                    chart.setOption({
                        legend: {
                            data: aNameList
                        },
                        series : {
                            data: aCountList,
                        }
                    })
                }
            }
        }
    })
}

// 产品发货市级排行
function getCityData(chart, startTime, endTime, id, type) {
    $.ajax({
        url: getSaleAreaData,
        type: 'post',
        dataType: 'json',
        data: { 
            start_time: startTime,
            end_time: endTime,
            templet_id: id,
            type: type
        },
        success: function (data) {
            if (data.code == 1) {
                var oList = data.info.list;
                var aNameList = [];
                var aCountList = [];
                // console.log(oList);
                for (key in oList) {
                    var count = 0;
                    aNameList.push(key);
                    oList[key].forEach(function (value) {
                        // console.log(value)
                        count += parseInt(value.total_num);
                    })
                    aCountList.push({
                        name: key,
                        value: count 
                    });
                    // console.log(aCountList)
                }
                if (aCountList.length == 0) {
                    chart.setOption({
                        series : {
                            data: [{name: '没有数据',value: 1}],
                        }
                    })
                } else {
                    chart.setOption({
                        legend: {
                            data: aNameList
                        },
                        series : {
                            data: aCountList,
                        }
                    })
                }
            }
        }
    })
}

// 产品发货区级排行
function getCountyData(chart, startTime, endTime, id, type) {
    $.ajax({
        url: getSaleAreaData,
        type: 'post',
        dataType: 'json',
        data: { 
            start_time: startTime,
            end_time: endTime,
            templet_id: id,
            type: type
        },
        success: function (data) {
            if (data.code == 1) {
                var oList = data.info.list;
                var aNameList = [];
                var aCountList = [];
                // console.log(oList);
                for (key in oList) {
                    var count = 0;
                    aNameList.push(key);
                    oList[key].forEach(function (value) {
                        // console.log(value)
                        count += parseInt(value.total_num);
                    })
                    aCountList.push({
                        name: key,
                        value: count 
                    });
                    // console.log(aCountList)
                }
                if (aCountList.length == 0) {
                    chart.setOption({
                        series : {
                            data: [{name: '没有数据',value: 1}],
                        }
                    })
                } else {
                    chart.setOption({
                        legend: {
                            data: aNameList
                        },
                        series : {
                            data: aCountList,
                        }
                    })
                }
            }
        }
    })
}

// 日期格式化
Date.prototype.format = function (format) {
    let o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "H+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "f+": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(format))
        format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (let k in o)
        if (new RegExp("(" + k + ")").test(format))
            format = format.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k])
                .substr(("" + o[k]).length)));
    return format;
};