//<!-- 测试代码 -->
$(function() {
    fixedTable();
    function fixedTable() {
        var table_td = $('.table-wrapper .layui-table thead tr:first-child td');
        var table_th = $('.table-wrapper .layui-table thead tr:first-child th');
        var sample_td = $('.table-wrapper .layui-table tbody tr:first-child td');
        if(sample_td.length <= 1) {
            $('.table-wrapper .layui-table thead tr:first-child').css('position', 'static');
        } else {
            $('.table-wrapper .layui-table thead tr:first-child').css({
                'width': $('.table-wrapper .layui-table tr:nth-child(2)').width() + 'px',
                'transform': 'translateY(-28px)',
                'position': 'fixed'
            });
            $('.table-wrapper .layui-table tbody tr:first-child').css('borderTop', 'solid 40px transparent')
            if(table_th.length > 0) {
                var count = 0;
                $.each(sample_td, function(key, value) {
                    count += $(value).get(0).offsetWidth;
                    table_th.eq(key).css('width', ($(value).get(0).offsetWidth + 2) + 'px');
                });
            } else if(table_th.length > 0) {
                $.each(sample_td, function(key, value) {
                    table_td.eq(key).css('width', $(value).get(0).offsetWidth + 'px');
                });
            }
        }

    }
    
    $('.table-wrapper').scroll(function(){
        if($(this).scrollLeft()>0){
            $('.table-wrapper .layui-table thead tr:first-child').css('transform','translate(-'+$(this).scrollLeft()+'px,-28px)')
        }
    });

    $(window).resize(function() {
        fixedTable();
    });
});