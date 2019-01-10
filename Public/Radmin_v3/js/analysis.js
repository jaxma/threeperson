//当月日期
var time = new Date();
var timeStr = time.getFullYear().toString();
timeStr += '-' + ((time.getMonth() + 1)>9?"":"0") + (time.getMonth() + 1).toString();
//赋初值
$('#agent').val(timeStr);
$('#order').val(timeStr);

var timeStr = timeStr.replace(/-/g, '');

//x轴坐标和数据变量
var dataX = new Array(),
    datas = new Array();

require(['echarts'], function (echarts) {

    var myChart1 = echarts.init($('#charts1').get(0));
    var myChart2 = echarts.init($('#charts2').get(0));

    //获取人数增长图数据
    getChartData(distributor, timeStr, myChart1);
    var option1 = option = {
        title: {
            show: true,
            text: '经销商人数增长情况(包括未审核的人数)',
            textStyle: {
                color: '#303030',
                fontWeight: 'normal',
                fontSize: 14,
                align: 'right'
            },
            left: '5%'
        },
        lineStyle: {
            normal: {
                color: 'rgb(44, 189, 158)'
            }
        },
        color: ['#b2eadf'],
        areaStyle: {
            normal: {
                //颜色渐变函数 前四个参数分别表示四个位置依次为左、下、右、上
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{

                    offset: 0,
                    color: 'rgba(44, 189, 158,1)'
                }, {
                    offset: .2,
                    color: 'rgba(44, 189, 158,0.9)'
                }, {
                    offset: .4,
                    color: 'rgba(44, 189, 158,0.7)'
                }, {
                    offset: .6,
                    color: 'rgba(44, 189, 158,0.5)'
                }, {
                    offset: .8,
                    color: 'rgba(44, 189, 158,0.3)'
                }, {
                    offset: 1,
                    color: 'rgba(44, 189, 158,0.1)'
                }])

            }
        },
        "color": [
            "#3fb1e3"
        ],
        tooltip: {
            trigger: 'axis',
            textStyle: {
                color: '#303030'
            },
            backgroundColor: '#b2eadf',
            axisPointer: {
                type: 'cross',
                label: {
                    color: '#303030',
                    backgroundColor: 'rgba(44,189,159,0.4)',
                    shadowColor: 'rgba(0, 0, 0, 0.5)',
                    shadowBlur: 20,
                    shadowOffsetY: 3,
                    shadowOffsetX: 3
                }
            },
            extraCssText: 'box-shadow: 3px 3px 15px rgba(0, 0, 0, 0.3);',
            formatter: '{b0}号 <br />—<br />新增：{c0}人'
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: [{
            type: 'category',
            boundaryGap: false,
            name: '日期',
            splitLine: {
                show: false
            },
            axisTick: {
                show: false
            },
            nameTextStyle: {
                color: '#303030',
                fontSize: 16
            },
            data: dataX
        }],
        yAxis: [{
            type: 'value',
            axisTick: {
                show: true
            },
            axisLabel: {
                show: true
            },
            splitLine: {
                show: true
            }
        }],
        series: [{
            name: '新增',
            type: 'line',
            stack: '总量',
            areaStyle: {
                normal: {}
            },
            smooth: true,
            data: datas
        }]
    };

    //获取订单金额趋势图数据
    getChartData(order_money, timeStr, myChart2)
    var option2 = option = {
        title: {
            show: true,
            text: '订单金额趋势图(包括未审核的订单金额)',
            textStyle: {
                color: '#303030',
                fontWeight: 'normal',
                fontSize: 14,
                align: 'right'
            },
            left: '5%'
        },
        lineStyle: {
            normal: {
                color: 'rgb(45,189,230)'
            }
        },
        color: ['#b2eadf'],
        areaStyle: {
            normal: {
                //颜色渐变函数 前四个参数分别表示四个位置依次为左、下、右、上
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{

                    offset: 0,
                    color: 'rgba(45,189,230,1)'
                }, {
                    offset: .2,
                    color: 'rgba(45,189,230,0.9)'
                }, {
                    offset: .4,
                    color: 'rgba(45,189,230,0.7)'
                }, {
                    offset: .6,
                    color: 'rgba(45,189,230,0.5)'
                }, {
                    offset: .8,
                    color: 'rgba(45,189,230,0.3)'
                }, {
                    offset: 1,
                    color: 'rgba(45,189,230,0.1)'
                }])

            }
        },
        "color": [
            "#3fb1e3"
        ],
        tooltip: {
            trigger: 'axis',
            textStyle: {
                color: '#303030'
            },
            backgroundColor: '#b2e5f5',
            axisPointer: {
                type: 'cross',
                label: {
                    color: '#303030',
                    backgroundColor: 'rgba(45,189,230,0.4)',
                    shadowColor: 'rgba(0, 0, 0, 0.5)',
                    shadowBlur: 20,
                    shadowOffsetY: 3,
                    shadowOffsetX: 3
                }
            },
            extraCssText: 'box-shadow: 3px 3px 15px rgba(0, 0, 0, 0.3);',
            formatter: '{b0}号 <br />—<br />金额：{c0}元'
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: [{
            type: 'category',
            boundaryGap: false,
            name: '日期',
            splitLine: {
                show: false
            },
            axisTick: {
                show: false
            },
            nameTextStyle: {
                color: '#303030',
                fontSize: 16
            },
            data: dataX
        }],
        yAxis: [{
            type: 'value',
            axisTick: {
                show: true
            },
            axisLabel: {
                show: true
            },
            splitLine: {
                show: true
            }
        }],
        series: [{
            name: '新增',
            type: 'line',
            stack: '总量',
            areaStyle: {
                normal: {}
            },
            smooth: true,
            data: datas
        }]
    };

    //调整窗口大小
    $(window).resize(function () {
        myChart1.resize();
        myChart2.resize();
    })

    // 绘制图表
    myChart1.setOption(option1);
    myChart2.setOption(option2);

    //初始化日期选择器
    layui.use('laydate', function () {
        var laydate = layui.laydate;
        laydate.render({
            elem: '#agent',
            type: 'month',
            change: function (value, date, endDate) {
                timeStr = value.replace(/-/g, '');
                getChartData(distributor, timeStr, myChart1);
            }
        });
        laydate.render({
            elem: '#order',
            type: 'month',
            change: function (value, date, endDate) {
                timeStr = value.replace(/-/g, '');
                getChartData(order_money, timeStr, myChart2);
            }
        });
    })
});

function getChartData(urls, param, aim) {
    var dats = "";
    if (urls.indexOf('order') != -1) {
        dats = {month: param}
    } else {
        dats = {month: param, type: 'day', is_show_level: ''}
    }
    $.ajax({
        url: urls,
        data: dats,
        async: true,
        success: function (data) {
            var dataX = data.info.day;
            var datas = data.info.count;
            aim.setOption({
                xAxis: [{
                    type: 'category',
                    boundaryGap: false,
                    name: '日期',
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    nameTextStyle: {
                        color: '#303030',
                        fontSize: 16
                    },
                    data: dataX
                }],
                series: [{
                    name: '新增',
                    type: 'line',
                    stack: '总量',
                    areaStyle: {
                        normal: {}
                    },
                    smooth: true,
                    data: datas
                }]
            })
        }
    })
}
