// +----------------------------------------------------------------------
// | Think.Admin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/Think.Admin
// +----------------------------------------------------------------------

// 当前资源URL目录
var baseUrl = (function () {
    var scripts = document.scripts, src = scripts[scripts.length - 1].src;
    return src.substring(0, src.lastIndexOf("/") + 1);
    console.log(src.substring(0, src.lastIndexOf("/") + 1));  //http://192.168.3.68/topos/Public/Radmin_v3/js/
})();

// RequireJs 配置参数
require.config({
    waitSeconds: 0,
    baseUrl: baseUrl,
    map: {'*': {css: baseUrl + '../plugs/require/require.css.js'}},
    paths: {
        // 自定义插件（源码自创建或已修改源码）
        'admin.plugs': ['plugs'],
        'admin.listen': ['listen'],
        'layui': ['../plugs/layui/layui'],
        //省市区联动插件
//        'pcasunzips': ['../plugs/jquery/pcasunzips'],
        //日期时间选择插件
//        'laydate': ['../plugs/layui/laydate/laydate'],
        // 开源插件（未修改源码）
        //进度加载条
        'pace': ['../plugs/jquery/pace.min'],
        'json': ['../plugs/jquery/json2.min'],
//        'citys': ['../plugs/jquery/jquery.citys'],
//        'print': ['../plugs/jquery/jquery.PrintArea'],
        //加密js
        'base64': ['../plugs/jquery/base64.min'],
        'jquery': ['../plugs/jquery/jquery.min'],
        'bootstrap': ['../plugs/bootstrap/js/bootstrap.min'],
        'bootstrap.typeahead': ['../plugs/bootstrap/js/bootstrap3-typeahead.min'],
        //复制剪切板插件
//         'zeroclipboard': ['../ueditor/third-party/zeroclipboard/ZeroClipboard.min'],
        'jquery.cookies': ['../plugs/jquery/jquery.cookie'],
        //瀑布流插件
        'jquery.masonry': ['../plugs/jquery/masonry.min'],
        'echarts': ['../plugs/echart/echarts'],
        'menu': ['../plugs/default/js/menu'],
        'topbar': ['../plugs/default/js/topbar'],
        'china': ['../plugs/echart/china'],
        'url': ['./url'],
        'page': ['./page'],
        //文本编辑器
        'ueditor.config': ['../plugs/ueditor/ueditor.config'],
        'ueditor.all': ['../plugs/ueditor/ueditor.all.min'],
        'zh-cn': ['../plugs/ueditor/lang/zh-cn/zh-cn'],
        'ZeroClipboard': ['../plugs/ueditor/third-party/zeroclipboard/ZeroClipboard'],
        'img_upload': ['./img_upload'],
        'lay_verify': ['./lay_verify'],
        'lunpanset': ['../../sale_v1/js/lunpanset'],
    },
    shim: {
        'layui': {deps: ['jquery']},
//        'laydate': {deps: ['jquery']},
        'bootstrap': {deps: ['jquery']},
//        'pcasunzips': {deps: ['jquery']},
        'jquery.cookies': {deps: ['jquery']},
        'jquery.masonry': {deps: ['jquery']},
        'url':{deps: ['jquery','layui']},
        'page':{deps: ['jquery','layui']},
        'admin.plugs': {deps: ['jquery', 'layui']},
        'bootstrap.typeahead': {deps: ['jquery', 'bootstrap']},
        'websocket': {deps: [baseUrl + '../plugs/socket/swfobject.min.js']},
        'admin.listen': {deps: ['jquery', 'jquery.cookies', 'admin.plugs']},
        'jquery.ztree': {deps: ['jquery', 'css!' + baseUrl + '../plugs/ztree/zTreeStyle/zTreeStyle.css']},
        'menu': {deps: ['jquery']},
        'topbar':{deps: ['jquery']},
        'echarts': {deps: ['jquery']},
        'china': {deps:['echarts']},
        'zh-cn': {deps:['ueditor.all']},
        'lay_verify': {deps:['layui']},
        'ueditor.all': {deps:['ueditor.config']},
    },
    deps: ['css!' + baseUrl + '../plugs/awesome/css/font-awesome.min.css'],
    // 开启debug模式，不缓存资源
    urlArgs: "ver=" + (new Date()).getTime()
});

window.WEB_SOCKET_SWF_LOCATION = baseUrl + "../plugs/socket/WebSocketMain.swf";
//window.UEDITOR_HOME_URL = (window.ROOT_URL ? window.ROOT_URL + '/static/' : baseUrl) + 'plugs/ueditor/';

// UI框架初始化
require(['pace', 'jquery', 'layui', 'bootstrap', 'jquery.cookies','menu'], function () {
    layui.config({dir: baseUrl + '../plugs/layui/'});
    layui.use(['layer', 'form', 'laypage'], function () {
        window.layer = layui.layer;
        window.form = layui.form;
        window.laypage = layui.laypage;
        form.render();
        require(['admin.listen','lay_verify']);
    });
});
