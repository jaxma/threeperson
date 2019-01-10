var cityMap = {
  "北京市": "110100",
  "天津市": "120100",
  "上海市": "310100",
  "重庆市": "500100",

  "崇明县": "310200",
  "湖北省直辖县市": "429000",
  "铜仁市": "522200",
  "毕节市": "522400",

  "石家庄市": "130100",
  "唐山市": "130200",
  "秦皇岛市": "130300",
  "邯郸市": "130400",
  "邢台市": "130500",
  "保定市": "130600",
  "张家口市": "130700",
  "承德市": "130800",
  "沧州市": "130900",
  "廊坊市": "131000",
  "衡水市": "131100",
  "太原市": "140100",
  "大同市": "140200",
  "阳泉市": "140300",
  "长治市": "140400",
  "晋城市": "140500",
  "朔州市": "140600",
  "晋中市": "140700",
  "运城市": "140800",
  "忻州市": "140900",
  "临汾市": "141000",
  "吕梁市": "141100",
  "呼和浩特市": "150100",
  "包头市": "150200",
  "乌海市": "150300",
  "赤峰市": "150400",
  "通辽市": "150500",
  "鄂尔多斯市": "150600",
  "呼伦贝尔市": "150700",
  "巴彦淖尔市": "150800",
  "乌兰察布市": "150900",
  "兴安盟": "152200",
  "锡林郭勒盟": "152500",
  "阿拉善盟": "152900",
  "沈阳市": "210100",
  "大连市": "210200",
  "鞍山市": "210300",
  "抚顺市": "210400",
  "本溪市": "210500",
  "丹东市": "210600",
  "锦州市": "210700",
  "营口市": "210800",
  "阜新市": "210900",
  "辽阳市": "211000",
  "盘锦市": "211100",
  "铁岭市": "211200",
  "朝阳市": "211300",
  "葫芦岛市": "211400",
  "长春市": "220100",
  "吉林市": "220200",
  "四平市": "220300",
  "辽源市": "220400",
  "通化市": "220500",
  "白山市": "220600",
  "松原市": "220700",
  "白城市": "220800",
  "延边朝鲜族自治州": "222400",
  "哈尔滨市": "230100",
  "齐齐哈尔市": "230200",
  "鸡西市": "230300",
  "鹤岗市": "230400",
  "双鸭山市": "230500",
  "大庆市": "230600",
  "伊春市": "230700",
  "佳木斯市": "230800",
  "七台河市": "230900",
  "牡丹江市": "231000",
  "黑河市": "231100",
  "绥化市": "231200",
  "大兴安岭地区": "232700",
  "南京市": "320100",
  "无锡市": "320200",
  "徐州市": "320300",
  "常州市": "320400",
  "苏州市": "320500",
  "南通市": "320600",
  "连云港市": "320700",
  "淮安市": "320800",
  "盐城市": "320900",
  "扬州市": "321000",
  "镇江市": "321100",
  "泰州市": "321200",
  "宿迁市": "321300",
  "杭州市": "330100",
  "宁波市": "330200",
  "温州市": "330300",
  "嘉兴市": "330400",
  "湖州市": "330500",
  "绍兴市": "330600",
  "金华市": "330700",
  "衢州市": "330800",
  "舟山市": "330900",
  "台州市": "331000",
  "丽水市": "331100",
  "合肥市": "340100",
  "芜湖市": "340200",
  "蚌埠市": "340300",
  "淮南市": "340400",
  "马鞍山市": "340500",
  "淮北市": "340600",
  "铜陵市": "340700",
  "安庆市": "340800",
  "黄山市": "341000",
  "滁州市": "341100",
  "阜阳市": "341200",
  "宿州市": "341300",
  "六安市": "341500",
  "亳州市": "341600",
  "池州市": "341700",
  "宣城市": "341800",
  "福州市": "350100",
  "厦门市": "350200",
  "莆田市": "350300",
  "三明市": "350400",
  "泉州市": "350500",
  "漳州市": "350600",
  "南平市": "350700",
  "龙岩市": "350800",
  "宁德市": "350900",
  "南昌市": "360100",
  "景德镇市": "360200",
  "萍乡市": "360300",
  "九江市": "360400",
  "新余市": "360500",
  "鹰潭市": "360600",
  "赣州市": "360700",
  "吉安市": "360800",
  "宜春市": "360900",
  "抚州市": "361000",
  "上饶市": "361100",
  "济南市": "370100",
  "青岛市": "370200",
  "淄博市": "370300",
  "枣庄市": "370400",
  "东营市": "370500",
  "烟台市": "370600",
  "潍坊市": "370700",
  "济宁市": "370800",
  "泰安市": "370900",
  "威海市": "371000",
  "日照市": "371100",
  "莱芜市": "371200",
  "临沂市": "371300",
  "德州市": "371400",
  "聊城市": "371500",
  "滨州市": "371600",
  "菏泽市": "371700",
  "郑州市": "410100",
  "开封市": "410200",
  "洛阳市": "410300",
  "平顶山市": "410400",
  "安阳市": "410500",
  "鹤壁市": "410600",
  "新乡市": "410700",
  "焦作市": "410800",
  "濮阳市": "410900",
  "许昌市": "411000",
  "漯河市": "411100",
  "三门峡市": "411200",
  "南阳市": "411300",
  "商丘市": "411400",
  "信阳市": "411500",
  "周口市": "411600",
  "驻马店市": "411700",
  "省直辖县级行政区划": "469000",
  "武汉市": "420100",
  "黄石市": "420200",
  "十堰市": "420300",
  "宜昌市": "420500",
  "襄阳市": "420600",
  "鄂州市": "420700",
  "荆门市": "420800",
  "孝感市": "420900",
  "荆州市": "421000",
  "黄冈市": "421100",
  "咸宁市": "421200",
  "随州市": "421300",
  "恩施土家族苗族自治州": "422800",
  "长沙市": "430100",
  "株洲市": "430200",
  "湘潭市": "430300",
  "衡阳市": "430400",
  "邵阳市": "430500",
  "岳阳市": "430600",
  "常德市": "430700",
  "张家界市": "430800",
  "益阳市": "430900",
  "郴州市": "431000",
  "永州市": "431100",
  "怀化市": "431200",
  "娄底市": "431300",
  "湘西土家族苗族自治州": "433100",
  "广州市": "440100",
  "韶关市": "440200",
  "深圳市": "440300",
  "珠海市": "440400",
  "汕头市": "440500",
  "佛山市": "440600",
  "江门市": "440700",
  "湛江市": "440800",
  "茂名市": "440900",
  "肇庆市": "441200",
  "惠州市": "441300",
  "梅州市": "441400",
  "汕尾市": "441500",
  "河源市": "441600",
  "阳江市": "441700",
  "清远市": "441800",
  "东莞市": "441900",
  "中山市": "442000",
  "潮州市": "445100",
  "揭阳市": "445200",
  "云浮市": "445300",
  "南宁市": "450100",
  "柳州市": "450200",
  "桂林市": "450300",
  "梧州市": "450400",
  "北海市": "450500",
  "防城港市": "450600",
  "钦州市": "450700",
  "贵港市": "450800",
  "玉林市": "450900",
  "百色市": "451000",
  "贺州市": "451100",
  "河池市": "451200",
  "来宾市": "451300",
  "崇左市": "451400",
  "海口市": "460100",
  "三亚市": "460200",
  "三沙市": "460300",
  "成都市": "510100",
  "自贡市": "510300",
  "攀枝花市": "510400",
  "泸州市": "510500",
  "德阳市": "510600",
  "绵阳市": "510700",
  "广元市": "510800",
  "遂宁市": "510900",
  "内江市": "511000",
  "乐山市": "511100",
  "南充市": "511300",
  "眉山市": "511400",
  "宜宾市": "511500",
  "广安市": "511600",
  "达州市": "511700",
  "雅安市": "511800",
  "巴中市": "511900",
  "资阳市": "512000",
  "阿坝藏族羌族自治州": "513200",
  "甘孜藏族自治州": "513300",
  "凉山彝族自治州": "513400",
  "贵阳市": "520100",
  "六盘水市": "520200",
  "遵义市": "520300",
  "安顺市": "520400",
  "黔西南布依族苗族自治州": "522300",
  "黔东南苗族侗族自治州": "522600",
  "黔南布依族苗族自治州": "522700",
  "昆明市": "530100",
  "曲靖市": "530300",
  "玉溪市": "530400",
  "保山市": "530500",
  "昭通市": "530600",
  "丽江市": "530700",
  "普洱市": "530800",
  "临沧市": "530900",
  "楚雄彝族自治州": "532300",
  "红河哈尼族彝族自治州": "532500",
  "文山壮族苗族自治州": "532600",
  "西双版纳傣族自治州": "532800",
  "大理白族自治州": "532900",
  "德宏傣族景颇族自治州": "533100",
  "怒江傈僳族自治州": "533300",
  "迪庆藏族自治州": "533400",
  "拉萨市": "540100",
  "昌都地区": "542100",
  "山南地区": "542200",
  "日喀则地区": "542300",
  "那曲地区": "542400",
  "阿里地区": "542500",
  "林芝地区": "542600",
  "西安市": "610100",
  "铜川市": "610200",
  "宝鸡市": "610300",
  "咸阳市": "610400",
  "渭南市": "610500",
  "延安市": "610600",
  "汉中市": "610700",
  "榆林市": "610800",
  "安康市": "610900",
  "商洛市": "611000",
  "兰州市": "620100",
  "嘉峪关市": "620200",
  "金昌市": "620300",
  "白银市": "620400",
  "天水市": "620500",
  "武威市": "620600",
  "张掖市": "620700",
  "平凉市": "620800",
  "酒泉市": "620900",
  "庆阳市": "621000",
  "定西市": "621100",
  "陇南市": "621200",
  "临夏回族自治州": "622900",
  "甘南藏族自治州": "623000",
  "西宁市": "630100",
  "海东地区": "632100",
  "海北藏族自治州": "632200",
  "黄南藏族自治州": "632300",
  "海南藏族自治州": "632500",
  "果洛藏族自治州": "632600",
  "玉树藏族自治州": "632700",
  "海西蒙古族藏族自治州": "632800",
  "银川市": "640100",
  "石嘴山市": "640200",
  "吴忠市": "640300",
  "固原市": "640400",
  "中卫市": "640500",
  "乌鲁木齐市": "650100",
  "克拉玛依市": "650200",
  "吐鲁番地区": "652100",
  "哈密地区": "652200",
  "昌吉回族自治州": "652300",
  "博尔塔拉蒙古自治州": "652700",
  "巴音郭楞蒙古自治州": "652800",
  "阿克苏地区": "652900",
  "克孜勒苏柯尔克孜自治州": "653000",
  "喀什地区": "653100",
  "和田地区": "653200",
  "伊犁哈萨克自治州": "654000",
  "塔城地区": "654200",
  "阿勒泰地区": "654300",
  "自治区直辖县级行政区划": "659000",
  "台湾省": "710000",
  "香港特别行政区": "810100",
  "澳门特别行政区": "820000"
};

var data1 = new Array(),
  data2 = new Array(),
  data3 = new Array(),
  data4 = new Array(),
  data5 = new Array(),
  data6 = new Array();
//data1为地图数据，data2为省级分布的数据，data3为销量排行榜的数据，data4为层级分布的数据，data5为类别占比,data6为类别提示

//默认当前日期
var months;
//设置颜色背景数组
var bgcolor = ['#0cf', '#c23531', '#d48265', '#61a0a8', '#91c7ae', 'orange'];

data1 = [{
  name: '番禺区',
  value: Math.round(Math.random() * 1000)
}, {
  name: '白云区',
  value: Math.round(Math.random() * 1000)
}, {
  name: '花都区',
  value: Math.round(Math.random() * 1000)
}, {
  name: '天河区',
  value: Math.round(Math.random() * 1000)
}, {
  name: '越秀区',
  value: Math.round(Math.random() * 1000)
}, {
  name: '海珠区',
  value: Math.round(Math.random() * 1000)
}, {
  name: '萝岗区',
  value: Math.round(Math.random() * 1000)
}, {
  name: '黄埔区',
  value: Math.round(Math.random() * 1000)
}, {
  name: '增城市',
  value: Math.round(Math.random() * 1000)
}, {
  name: '从化市',
  value: Math.round(Math.random() * 1000)
}];

data11 = [{
  name: '广州市',
  value: Math.round(Math.random() * 1000)
}, {
  name: '东莞市',
  value: Math.round(Math.random() * 1000)
}, {
  name: '惠州市',
  value: Math.round(Math.random() * 1000)
}, {
  name: '河源市',
  value: Math.round(Math.random() * 1000)
}, {
  name: '梅州市',
  value: Math.round(Math.random() * 1000)
}, {
  name: '云浮市',
  value: Math.round(Math.random() * 1000)
}, {
  name: '江门市',
  value: Math.round(Math.random() * 1000)
}, {
  name: '湛江市',
  value: Math.round(Math.random() * 1000)
}, {
  name: '茂名市',
  value: Math.round(Math.random() * 1000)
}, {
  name: '阳江市',
  value: Math.round(Math.random() * 1000)
}];

data111 = [{
    name: '北京',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '天津',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '上海',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '重庆',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '河北',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '河南',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '云南',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '辽宁',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '黑龙江',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '湖南',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '安徽',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '山东',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '新疆',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '江苏',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '浙江',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '江西',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '湖北',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '广西',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '甘肃',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '山西',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '内蒙古',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '陕西',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '吉林',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '福建',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '贵州',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '广东',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '青海',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '西藏',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '四川',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '宁夏',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '海南',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '台湾',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '香港',
    value: Math.round(Math.random() * 1000)
  },
  {
    name: '澳门',
    value: Math.round(Math.random() * 1000)
  }
]

//  地图的所有信息和状态
var mapDetail = {
  iscity: false, //是否为城市
  isarea: false, //是否为区
  pinyin: '', //城市对应的拼音
  cityid: '', //城市对应的id
  urls: '', //请求的地址
  mapdata: [], //请求得到的数据
  mapname: '' //地图对应的名字
}

require(
  [
    'echarts',
    'echarts/chart/bar', // 按需加载
    'echarts/chart/map',
    'echarts/chart/pie'
  ],
  function(echarts) {

    //代理城市分布图
    var myChart1 = echarts.init($('#mychart1').get(0));
    //代理订单销量排行
    var myChart2 = echarts.init($('#mychart2').get(0));
    //商品类别占比
    var myChart3 = echarts.init($('#mychart3').get(0));
    //世界地图
    var myChart4 = echarts.init($('#mychart4').get(0));
    //代理层级分布图
    var myChart5 = echarts.init($('#mychart5').get(0));
    $(window).resize(function() {
      myChart1.resize();
      myChart2.resize();
      myChart3.resize();
      myChart4.resize();
      myChart5.resize();
    });

    //获取等级分布
    getlevel(myChart5);

    //获取商品类别占比
    var months = $("#select-time").val(getNowDay());
    months = changeDate(months.val())
    getkind(myChart3, months);

    //实时订单
    appendOrder();
    var wrap = $(".order-wrap");
    var flag = true;
    scrollOrder(wrap, flag);

    getSalecount(myChart2, months)
    //获取统计金额
    getTodayCount();

    //时间选择
    $(".search").bind("click", function() {
      var datetime = $("#select-time").val();
      if(datetime != '') {
        datetime = datetime.replace(/\-/g, '');
        months = datetime;
        getkind(myChart3, months);
        getSalecount(myChart2, months)
      }
    });
    //代理城市分布图1
    var myoption1 = option = {
      tooltip: {
        trigger: 'item',
        formatter: "{a} <br/>{b}: {c} ({d}%)"
      },
      title: {
        show: true,
        text: '经销商省级分布图',
        textStyle: {
          color: '#fff',
          align: 'center',
        },
        padding: [5, 200],
        x: 'center',
        backgroundColor: '#1c4947'
      },
      color: ['#c23531', '#2f4554', '#61a0a8', '#d48265', '#91c7ae', '#749f83', '#ca8622', '#bda29a', '#6e7074', '#546570', '#c4ccd3'],
      legend: {
        orient: 'horizontal',
        bottom: "0",
        x: 'center',
        y: 'bottom',
        tooltip: {
          show: true,
        },
        data: [{
          name: '广东',
          textStyle: {
            color: '#fff'
          }
        }, {
          name: '桂林',
          textStyle: {
            color: '#fff'
          }
        }, {
          name: '河南',
          textStyle: {
            color: '#fff'
          }
        }, {
          name: '北京',
          textStyle: {
            color: '#fff'
          }
        }]
      },
      series: [{
        name: '城市分布',
        type: 'pie',
        radius: ['40%', '60%'],
        avoidLabelOverlap: false,
        label: {
          normal: {
            textStyle: {
              color: 'rgba(255, 255, 255, 1)'
            }
          }
        },
        labelLine: {
          normal: {
            lineStyle: {
              color: 'rgba(255, 255, 255, 1)'
            },
            length: 12,
            length2: 5
          }
        },
        data: [{
            value: 335,
            name: '广东'
          },
          {
            value: 310,
            name: '桂林'
          },
          {
            value: 234,
            name: '北京'
          },
          {
            value: 135,
            name: '河南'
          }
        ]
      }]
    };

    /****************************************代理订单销量排行**********************************************************/

    var myoption2 = option = {
      title: {
        show: true,
        text: '经销商订单数量排行',
        textStyle: {
          color: '#fff',
          align: 'center',
          width: 300
        },
        padding: [5, 200],
        x: 'center',
        backgroundColor: '#1c4947'
      },
      tooltip: {
        trigger: 'axis',
        axisPointer: { // 坐标轴指示器，坐标轴触发有效
          type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
        }
      },
      color: ['#c23531', '#2f4554', '#61a0a8', '#d48265', '#91c7ae', '#749f83', '#ca8622', '#bda29a', '#6e7074', '#546570', '#c4ccd3'],
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
        axisLine: {
          lineStyle: {
            color: '#fff'
          }
        },
        data: ['test', 'sad']
        //      data: ['张婕', '苏凤丹', '刘楚飞', '陈洁可', '黄义达', '方绮雯']
      },
      series: [{
        name: '个人销量',
        type: 'bar',
        stack: '总量',
        label: {
          normal: {
            show: true,
            position: 'insideRight'
          }
        },
        data: [{
          value: 10
        }, {
          value: 1
        }]
        //      data: [{
        //        value: 95,
        //        itemStyle: {
        //          normal: {
        //            color: '#0cf'
        //          }
        //        }
        //      }, {
        //        value: 108,
        //        itemStyle: {
        //          normal: {
        //            color: '#c23531'
        //          }
        //        }
        //      }, {
        //        value: 156,
        //        itemStyle: {
        //          normal: {
        //            color: '#d48265'
        //          }
        //        }
        //      }, {
        //        value: 192,
        //        itemStyle: {
        //          normal: {
        //            color: '#61a0a8'
        //          }
        //        }
        //      }, {
        //        value: 210,
        //        itemStyle: {
        //          normal: {
        //            color: '#91c7ae'
        //          }
        //        }
        //      }, {
        //        value: 230,
        //        itemStyle: {
        //          normal: {
        //            color: 'orange'
        //          }
        //        }
        //      }]
      }]
    };

    /************************************************商品类别占比******************************************/
    var myoption3 = option = {
      tooltip: {
        trigger: 'item',
        formatter: "{a} <br/>{b}: {c} ({d}%)"
      },
      title: {
        show: true,
        text: '商品类别占比',
        textStyle: {
          color: '#fff',
          align: 'center',
          width: 300
        },
        padding: [5, 200],
        x: 'center',
        backgroundColor: '#1c4947'
      },
      color: ['#c23531', '#2f4554', '#61a0a8', '#d48265', '#91c7ae', '#749f83', '#ca8622', '#bda29a', '#6e7074', '#546570', '#c4ccd3'],
      legend: {
        orient: 'horizontal',
        bottom: "0",
        x: 'center',
        y: 'bottom',
        tooltip: {
          show: true,
        },
        data: data6
      },
      series: [{
        name: '商品类别',
        type: 'pie',
        radius: ['40%', '60%'],
        avoidLabelOverlap: true,
        label: {
          normal: {
            show: false,
            position: 'center'
          },
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
            show: false
          }
        },
        data: [{
          value: 0
        }]
      }]
    };

    /********************************世界地图开始***********************************/

    var myoption4 = option = {
      tooltip: {
        trigger: 'item'
      },
      legend: {
        orient: 'vertical',
        x: 'left',
        data: ['销量'],
        textStyle: {
          color: 'white'
        }
      },
      visualMap: {
        min: 0,
        max: 1000,
        left: 'left',
        top: 'bottom',
        inRange: {
          color: ['#8bd7f1', '#27b1df', "#2b7fd9"]
        },
        text: ['高', '低'], // 文本，默认为数值文本
        calculable: true
      },
      toolbox: {
        itemSize: 20,
        feature: {
          myTool2: {
            show: true,
            title: '返回',
            icon: 'image://' + mapimg + 'mapback.png',
            onclick: function(param) {
              mapDetail.mapdata = data111;
              if(mapDetail.iscity != false) {
                mapDetail.pinyin = pinYin(param.name);
              }
              mapDetail.cityid = 'cityMap.' + param.name;
              if(mapDetail.iscity) {
                backArea(mapDetail, param, echarts);
              } else if(mapDetail.isarea) {
                backArea(mapDetail, param, echarts);
              } else {
                alert("已经返回最顶层！");
              }
            }
          }
        }
      },
      series: [{
        name: '销量',
        type: 'map',
        mapType: 'china',
        //      roam: true,
        zoom: 1.2,
        label: {
          normal: {
            show: true,
            color: '#000'
          },
          emphasis: {
            show: true,
            color: '#fff'
          }
        },
        data: data111
      }]
    };
    myChart4.on('click', function(param) {
      //        console.log(param.value)
      if(!isNaN(param.value)) {
        //      console.log(pinYin(param.name));
        //拿到对应的json文件名
        if(mapDetail.iscity != true) {
          mapDetail.pinyin = pinYin(param.name);
        }

        mapDetail.cityid = 'cityMap.' + param.name;
        loadArea(mapDetail, param, echarts);
      } else {
        alert("该地区暂无更多数据！");
      }

    });

    /********************************世界地图结束***********************************/

    /*****************************************************代理层级分布图*********************************************/
    var myoption5 = option = {
      tooltip: {
        trigger: 'item',
        formatter: "{a} <br/>{b}: {c} ({d}%)"
      },
      title: {
        show: true,
        text: '经销商层级分布图',
        textStyle: {
          color: '#fff',
          align: 'center',
          width: 300
        },
        padding: [5, 200],
        x: 'center',
        backgroundColor: '#1c4947'
      },
      color: ['#c23531', '#2f4554', '#61a0a8', '#d48265', '#91c7ae', '#749f83', '#ca8622', '#bda29a', '#6e7074', '#546570', '#c4ccd3'],
      grid: {
        x: 500,
        containLabel: false
      },
      legend: {
        orient: 'horizontal',
        bottom: "0",
        x: 'center',
        y: 'bottom',
        itemGap: 3,
        padding: [0, 50],
        tooltip: {
          show: true
        },
        data: ['test']
      },
      series: [{
        name: '代理层级分布',
        type: 'pie',
        radius: ['40%', '60%'],
        avoidLabelOverlap: true,
        label: {
          normal: {
            textStyle: {
              color: 'rgba(255, 255, 255, 1)'
            }
          }
        },
        labelLine: {
          normal: {
            lineStyle: {
              color: 'rgba(255, 255, 255, 1)'
            },
            length: 12,
            length2: 5
          }
        },
        data: [{
          value: 0
        }]
      }]
    };

    //初始化图表
    myChart1.setOption(myoption1);
    myChart2.setOption(myoption2);
    myChart3.setOption(myoption3);
    myChart4.setOption(myoption4);
    myChart5.setOption(myoption5);
  }
);

//翻译成对应的拼音
function pinYin(name) {
  var chinese = ["河北", "山西", "内蒙古", "辽宁", "吉林", "黑龙江", "江苏", "浙江", "安徽", "福建", "江西", "山东", "河南", "湖北", "湖南", "广东", "广西", "海南", "四川", "贵州", "云南", "西藏", "陕西", "甘肃", "青海", "宁夏", "新疆", "北京", "天津", "上海", "重庆", "香港", "澳门", "台湾"];
  var pinyin = ["hebei", "shanxi", "neimenggu", "liaoning", "jilin", "heilongjiang", "jiangsu", "zhejiang", "anhui", "fujian", "jiangxi", "shandong", "henan", "hubei", "hunan", "guangdong", "guangxi", "hainan", "sichuan", "guizhou", "yunnan", "xizang", "xiaxi", "gansu", "qinghai", "ningxia", "xinjiang", "beijing", "tianjin", "shanghai", "chongqing", "xianggang", "aomen", "taiwan"];
  var flag = false;
  for(var i = 0; i < chinese.length; i++) {
    if(name == chinese[i]) {
      name = pinyin[i];
      flag = true;
      break;
    }
  }
  return name;
}

//将后台加载到的数据传到地图,并显示出来
function loadArea(mapDetail, param, echarts) {
  if(mapDetail.iscity != true) {
    mapDetail.urls = mapurl + mapDetail.pinyin + '.json';
    mapDetail.mapdata = data11;
    mapDetail.iscity = true;
    mapDetail.isarea = false;
    mapDetail.mapname = mapDetail.pinyin;
  } else if(mapDetail.isarea != true) {
    mapDetail.cityid = eval("(" + mapDetail.cityid + ")");
    mapDetail.urls = mapurl + mapDetail.cityid + '.json';
    //    console.log(mapDetail.cityid);
    mapDetail.iscity = false;
    mapDetail.isarea = true;
    mapDetail.mapname = mapDetail.cityid;
    mapDetail.mapdata = data1;
  }
  //  console.log(mapDetail)
  getDataMap(mapDetail.urls, mapDetail.mapname, mapDetail.mapdata, echarts);
}

//返回的方法
function backArea(mapDetail, param, echarts) {
  if(mapDetail.iscity == true) {
    //初始化
    mapDetail.urls = mapurl + 'china.json';
    mapDetail.cityid = '';
    mapDetail.isarea = false;
    mapDetail.iscity = false;
    mapDetail.pinyin = '';
    mapDetail.mapname = 'china';
    mapDetail.mapdata = data111;
  } else if(mapDetail.isarea == true) {
    mapDetail.urls = mapurl + mapDetail.pinyin + '.json';
    mapDetail.mapdata = data11;
    mapDetail.iscity = true;
    mapDetail.isarea = false;
    mapDetail.mapname = mapDetail.pinyin;
  }
  //  console.log(mapDetail)
  getDataMap(mapDetail.urls, mapDetail.mapname, mapDetail.mapdata, echarts);
}

//公用的加载地图方法
function getDataMap(urls, strs, mapdata) {
  //  console.log(mapdata)
  $.get(urls, function(data) {
    echarts.registerMap(strs, data);
    myChart4 = echarts.init(document.getElementById('mychart4'));
    myChart4.setOption({
      series: [{
        type: 'map',
        map: strs,
        data: mapdata
      }]
    });
  });
}
//实时订单滚动
function scrollOrder(aim, flag) {
  var ntop = aim.css("top").substring(0, aim.css("top").indexOf("px"));
  var stheight = aim.parent().height();
  var timer = setInterval(function() {
    ntop -= 5;
    var heights = aim.css("top").substring(0, aim.css("top").indexOf("px")) - stheight;
    if(Math.abs(heights) <= aim.height()) {
      if(flag == true) {
        aim.animate({
          top: ntop + 'px'
        }, 500, 'linear');
      } else {
        flag = false;
        clearInterval(timer);
      }
    }
  }, 500)

}
//加载实时订单
function appendOrder() {
  $.get(orderapi, function(data) {
    //  console.log(data)
    var items = new Array();
    $.each(data.info, function(key, value) {
      var str = '<li class="order-item"><span>' + value.u_name + '</span><span>' + value.order_num + '</span><span>' + value.templet.name + '</span><span>￥' + value.total_price + '</span></li>'
      items.push(str);
    });
    $(".order-wrap").append(items);
  })
}

//获取今日业绩金额
function getTodayCount() {
  $.post(todaytotalapi, {
      time: '20170904'
    },
    function(data) {
      //      console.log(data);
    });
}

//获取当前的时间
function getNowDay() {
  var time = new Date();
  var year = time.getFullYear();
  var month = time.getMonth() + 1;
  var day = time.getDate();
  var hour = time.getHours();
  var min = time.getMinutes();
  var str = year.toString();
  str += "-" + (month < 10 ? 0 : "") + month.toString() + "-";
  str += (day < 10 ? 0 : "") + day.toString()
  return str;
}

//转换日期格式
function changeDate(time) {
  return time.replace(/-/g, '');
}

//获取层级分布的数据
function getlevel(aim) {
  $.get(levelurl,
    function(data) {
      data6 = [];
      data4 = [];
      for(var i = 0; i < data.count.length; i++) {
        data4.push({
          name: data.name[i],
          value: Number(data.count[i])
        })
        data6.push({
          name: data.name[i],
          textStyle: {
            color: '#fff'
          }
        })
      }
      data4 = eval(data4);
      data6 = eval(data6);
      aim.setOption({
        legend: {
          data: data6
        },
        series: [{
          name: '代理层级分布',
          type: 'pie',
          radius: ['30%', '55%'],
          avoidLabelOverlap: true,
          label: {
            normal: {
              textStyle: {
                color: 'rgba(255, 255, 255, 1)'
              }
            }
          },
          labelLine: {
            normal: {
              show: false,
              lineStyle: {
                color: 'rgba(255, 255, 255, 1)'
              },
              length: 12,
              length2: 5
            }
          },
          data: data4
        }]
      });
    });
}

//获取商品类别
function getkind(aim, months) {
  $.post(kindurl, {
      month: months
    },
    function(data) {
      data6 = [];
      data5 = [];
      $.each(data.info.list, function(key, val) {
        data5.push({
          name: val.templet.name,
          value: Number(val.cost_num)
        })
        data6.push({
          name: val.templet.name,
          textStyle: {
            color: '#fff'
          }
        })
      });
      data5 = eval(data5);
      data6 = eval(data6);
      aim.setOption({
        legend: {
          data: data6
        },
        series: [{
          name: '商品类别',
          type: 'pie',
          radius: ['40%', '60%'],
          avoidLabelOverlap: true,
          label: {
            normal: {
              show: true,
              position: 'center'
            },
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
              show: false
            }
          },
          data: data5
        }]
      })
    });
}

//销售排行统计图
function getSalecount(aim, months) {
  $.post(todaytotalapi, {
      month: months
    },
    function(data) {
      data6 = [];
      data3 = [];
      $.each(data.info.list, function(key, val) {
        data3.push({
          //        name: val.dis_info.name,
          value: Number(val.buy_money)
        })
        data6.push(val.dis_info.name)
      });
      if(data6.length < 2 || false) {
        data3.push({
          name: "暂无数据",
          value: '',
          itemStyle: {
            normal: {
              color: bgcolor[2]
            }
          }
        })
        data6.push("暂无数据");
      }
      data3 = eval(data3.reverse());
      data6 = eval(data6.reverse());
      aim.setOption({
        yAxis: {
          //      data: ['张婕', '苏凤丹', '刘楚飞', '陈洁可', '黄义达', '方绮雯']
          data: ["测试", "李敏"]
        },
        series: [{
          data: data3
        }]
      })

    });
}