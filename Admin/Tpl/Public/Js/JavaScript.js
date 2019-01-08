/// <reference path="jquery-1.7.2.min.js" />
/// <reference path="jquery.treeview.js" />
/// <reference path="jQueryPlugin.js" />

jQuery(document).ready(function () {
    jQuery.error = console.error;

    autoHeight(jQuery('.autoHeight'));
    jQuery("tr:even").addClass('tdColor');
    //加载树形菜单
    jQuery(".browser").each(function () {
        var t = jQuery(this);
        if (t.length < 1) return;
        t.treeview();
    });
    jQuery(".mainAutoHeight").each(function () {
        var t = jQuery(this);
        if (t.length < 1) return;
        if (t.hasClass("wrapBox")) { autoHeight2(t, 2); } else { autoHeight2(t); }
    });
    jQuery(".column_Box").each(function () {
        var t = jQuery(this);
        if (t.length < 1) return;
        Tab_click(t.find('.tab ul li'), t.find(".wrapBox .body"));
    });
    jQuery('table').each(function () {
        var table = jQuery(this);
        checkBox_All(table.find(".checkBox_All"), table.find(".checkBox_list"));
    });
    setContainerMain();
    trClassFun();
});
//删除 tr:要删除的信息 id：对象的ID
var Delete = function (tr, id) {
    if (!confirm('确认要删除吗？')) return;
    alert("删除成功");
    //$.ajax({
    //    type: "POST",
    //    url: "...",
    //    data: "ID=" + id
    //});
}
//table 下层级打开或关闭下级
var trClassFun = function () {
    var tr = jQuery("table tbody tr");
    tr.each(function () {
        var t = jQuery(this);
        if (t.hasClass("tr")) {
            var img = t.find(".eil_right img");
            var nextTr = t.next("tr");
            var nnTr = nextTr.next("tr");
            img.click(function () {
                var src = img.attr("src");
                //如下级是关闭的
                if (nextTr.hasClass("hide")) {
                    nextTr.show();
                    nextTr.removeClass("hide");
                    img.attr("src", src.replace("open", "close"));
                    return;
                } else {//如果下级是打开的
                    nextTr.hide();
                    nextTr.addClass("hide");
                    img.attr("src", src.replace("close", "open"));
                    //如果下下级是关闭的，返回；否刚 下级增加点击事件
                    if (nnTr.hasClass("hide")) return;
                    nextTr.find(".eil_right img").click();
                }
            });
        }
    });
}

//全部選擇 obj:點擊全選對象 list:需要全選的選項(數組)
var checkBox_All = function (obj, list) {
    if (obj.length < 1) return;
    if (obj.attr("checked") == undefined) {
        obj.click(function () {
            list.each(function () {
                var c = jQuery(this);
                c.attr("checked", true);
            });
        });
    }

    list.each(function () {
        var t = jQuery(this);
        if (t.attr('checked') == undefined) obj.attr("checked", false);
        t.click(function () {
            if (t.attr('checked') == undefined) obj.attr("checked", false);
        });
    });

}

//自适应高度
var autoHeight = function (obj) {
    var list = jQuery(obj);//list ：有可能传入一組數組
    if (list.length < 1) return;
    var footHeight = jQuery('.Footer').height();
    list.each(function () {
        var t = jQuery(this);
        var t_top = t.offset().top + 4;
        Fun();
        jQuery(window).resize(function () { Fun(); });
        jQuery(window).scroll(function () { Fun(); });
        function Fun() {
            var docHeight = document.documentElement.clientHeight;//頁面可視高度
            t.css({ height: docHeight - t_top - footHeight });
            if (t.hasClass("Container")) { t.css({ height: docHeight - t_top - footHeight - 7 }); }
        }
    });
}
//自適應高度之2
var autoHeight2 = function (obj, cutHeight) {
    //主要區域的自適應高度
    var ContainerMian_autoHeight = jQuery(obj);
    if (ContainerMian_autoHeight.length < 1) return;
    var footHeight = jQuery('.Footer').height();
    ContainerMian_autoHeight.each(function () {
        var t = jQuery(this);
        var t_top = t.offset().top + 10;
        Fun();
        jQuery(window).resize(function () { Fun(); });
        jQuery(window).scroll(function () { Fun(); });
        function Fun() {
            var docHeight = document.documentElement.clientHeight;
            var h = docHeight - t_top - footHeight;
            if (cutHeight) h = h - cutHeight;
            t.css({ height: h });
            if (t.has('.autoHeight_scroll')) {
                if (h < t.children('.autoHeight_scroll').height()) { t.css("overflow-y", "scroll"); } else { t.css("overflow-y", "auto"); }
            }
        }
    });
}
//还原右侧
var setContainerMain = function () {
    var t = jQuery(".Container_index");
    if (t.length == 0) {
        var left = $(window.parent.document).find(".Container_Left");
        var main = $(window.parent.document).find(".ContainerMain");
        main.css("marginLeft", 120);
        left.show();
    }
}