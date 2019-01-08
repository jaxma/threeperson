/// <reference path="jquery-1.7.2.min.js" />
/* 
jQueryPlugin.js  基于 jQuery 的插件扩展包基类;
本js文件为插件包的基类包;
*/
if (!window["UI"]) window["UI"] = new Object();
if (!window["console"]) window["consloe"] = { log: function (msg) { } }

//鼠标经过切换 list:经过的列表; contents:对应切换的列表
var Tab = function (list, contents) {
    if (!contents) {
        list.bind("mouseover", function () {
            list.removeClass("current");
            $(this).addClass("current");
        })
        return;
    }
    contents.css("display", "none");
    for (var i = 0; i < list.length; i++) {
        var link = $(list[i]);
        if (contents[i] == undefined) { continue; }
        if (link.hasClass("current")) {
            contents.eq(i).css("display", "");
        }
        link.data("content", contents.eq(i));
        link.bind("mouseover", function () { var t = $(this); list.removeClass("current"); t.addClass("current"); contents.css("display", "none"); t.data("content").css('display', ''); });
    }
}
//鼠标经过切换 list:经过的列表; contents:对应切换的列表
var Tab_click = function (list, contents) {
    if (!contents) {
        list.bind("mouseover", function () {
            list.removeClass("current");
            $(this).addClass("current");
        })
        return;
    }
    contents.css("display", "none");
    for (var i = 0; i < list.length; i++) {
        var link = $(list[i]);
        if (contents[i] == undefined) { continue; }
        if (link.hasClass("current")) {
            contents.eq(i).css("display", "");
        }
        link.data("content", contents.eq(i));
        link.bind("click", function () { var t = $(this); list.removeClass("current"); t.addClass("current"); contents.css("display", "none"); t.data("content").css('display', ''); });
    }
}
// 焦点广告效果 list : 轮换列表; box ：外层容器;  timer : 广告自动切换时间，默认为5000ms
function FxPic(list, box, timer) {
    if (!list) { console.log("list is undefined,return!"); return; }
    box.css("position", "relative");
    list.css({
        "position": "absolute",
        "left": 0,
        "top": 0
    });
    var html = "<div class=\"No\">";
    for (var i = 0; i < list.length; i++) {
        html += "<a href=\"javascript:\">" + (i + 1) + "</a>";
    }
    html += "</div>";

    box.append(html);
    var link = box.find(".No > a");
    link.bind("mouseover", function () {
        link.removeClass("current");
        $(this).addClass("current");
        index = Number($(this).text()) - 1;
        list.fadeOut();
        list.eq(index).fadeIn();
    });
    link.eq(0).mouseover();
    var index = 0;
    setInterval(function () {
        index++;
        link.eq(index % link.length).mouseover();
    }, timer ? timer : 5000);
}
//按Name名字选择 返回数组
var $F = function (name) {
    /// <summary>
    /// 查找名字为name的控件;
    /// </summary>
    /// <param name="name">String 名字</param>
    ///<returns>返回第一个值 first();</returns>
    return $("*[name=" + name + "]").first();
};

UI.getSize = function () {
    /// <summary>
    /// 获取屏幕宽高 返回JSON对象 {x : ,y : , height , top }
    /// x 、 y 为网页可视区域的宽高。 height为网页全部内容的高  top为当前网页被卷去的高度;
    /// </summary>
    var height = Math.max(document.documentElement.scrollHeight, document.documentElement.clientHeight);
    if (height > window.screen.availHeight) height = document.documentElement.clientHeight;
    var width = document.documentElement.clientWidth;
    return {
        x: width,
        y: height,
        height: height,
        top: Math.max(document.body.scrollTop, document.documentElement.scrollTop)
    };
};

UI.center = function (obj, container) {
    ///<summary>
    ///设置obj居中;
    ///</summary>
    ///<param name="obj"></param>
    ///<param name="container">相对居中的容器。可选项，不填默认为body</param>
    obj = $(obj);
    if (container == undefined) container = document.body;
    container = $(container);
    var body = UI.getSize();
    //var position = obj.offset();
    var objWidth = obj.width();
    var objHeight = obj.height();
    obj.css({
        left: (body.x - objWidth) / 2,
        top: (body.height - objHeight) / 2 < 0 ? 0 :
            ($.browser.version < 7 ? (body.height - objHeight) / 2 + body.top : (body.height - objHeight) / 2)
    });
    return { x: (body.x - objWidth) / 2, y: (body.height - objHeight) / 2 };

};

//设为首页;
var SetHome = function (obj, url) {
    if (url == undefined) url = location.host;
    try {
        obj.style.behavior = "url(#default#homepage)";
        obj.SetHomePage(url);
    } catch (e) {
        if (window.netscape) {
            try {
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
            } catch (e) {
                alert("\u62B1\u6B49\uFF01\u60A8\u7684\u6D4F\u89C8\u5668\u4E0D\u652F\u6301\u76F4\u63A5\u8BBE\u4E3A\u9996\u9875\u3002\u8BF7\u5728\u6D4F\u89C8\u5668\u5730\u5740\u680F\u8F93\u5165\u201Cabout:config\u201D\u5E76\u56DE\u8F66\u7136\u540E\u5C06[signed.applets.codebase_principal_support]\u8BBE\u7F6E\u4E3A\u201Ctrue\u201D\uFF0C\u70B9\u51FB\u201C\u52A0\u5165\u6536\u85CF\u201D\u540E\u5FFD\u7565\u5B89\u5168\u63D0\u793A\uFF0C\u5373\u53EF\u8BBE\u7F6E\u6210\u529F\u3002");
            }
            var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
            prefs.setCharPref("browser.startup.homepage", url);
        }
    }
}

// 加入收藏夹;
var addBookmark = function (title) {
    if (title == undefined) title = document.title;
    var url = parent.location.href;

    try {
        //IE 
        window.external.addFavorite(url, title);
    } catch (e) {
        try {
            //Firefox;
            window.sidebar.addPanel(title, url, "");
        } catch (e) {
            alert("您的浏览器不支持自动加入收藏，请手动设置！", "提示信息");
        }
    }

}

//设置iframe的高度为自适应自身高度;
function setIframeHeight(iframe) {
    parent.document.all(iframe).height = parent.document.all(iframe).style.height = $(document.body).height();
}
String.prototype.toDate = function () {
    /// <summary>
    /// 把字符串转化成为日期对象 字符串格式为 yyyy(-|/)MM(-|/)dd ;
    /// </summary>
    var str = this;
    var regex = /^(\d{4})[\-|\/](\d{1,2})[\-|\/](\d{1,2}).*?/;
    if (!regex.test(str)) return null;
    var matchs = str.match(regex);
    return new Date(matchs[1], matchs[2].toInt() - 1, matchs[3]);
};

String.prototype.StartWith = function (str, ignoreCase) {
    /// <summary>
    /// 确定此字符串实例的开头是否与指定的字符串匹配;
    /// </summary>
    /// <param name="str">String 要比较的字符串</param>
    /// <param name="ignoreCase">Boolean 是否区分大小写 可选参数，默认为false</param>
    if (ignoreCase == undefined) ignoreCase = false;
    var string = this;
    if (!ignoreCase) { string = string.toLowerCase(); str = str.toLowerCase(); }
    return string.indexOf(str) == 0;
}

String.prototype.EndWith = function (str, ignoreCase) {
    /// <summary>
    /// 确定此字符串实例的结尾是否与指定的字符串匹配;
    /// </summary>
    /// <param name="str">String 要比较的字符串</param>
    /// <param name="ignoreCase">Boolean 是否区分大小写 可选参数，默认为false</param>
    if (ignoreCase == undefined) ignoreCase = false;
    var string = this;
    if (!ignoreCase) { string = string.toLowerCase(); str = str.toLowerCase(); }
    return string.length == string.indexOf(str) + str.length;
}
String.prototype.toHtml = function (data) {
    /// <summary>
    /// 把HTMl模板内容与对象内容进行替换;
    /// </summary>
    /// <param name="data">Object 含值的对象</param>
    var str = $(this);
    return str.replace(/\${(.*?)}/igm, function ($, $1) {
        var obj = data[$1];
        if (obj == undefined) return $;
        if (!isNaN(obj.parseFloat())) obj = obj.parseFloat();
        return obj != undefined ? obj : $;
    });
}
