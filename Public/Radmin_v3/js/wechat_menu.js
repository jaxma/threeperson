$(document).ready(function () {
    $('#menu').on('click', 'span', function () {
        $(this).parent().siblings().find('.menu-list').fadeOut();
        if ($(this).hasClass('active')) {
            $(this).removeClass('active').next().fadeOut();
        } else {
            $('#menu .menu-title').removeClass('active');
            $(this).addClass('active').next().fadeIn();    
        }
    })


    $('#menu .menu-list').on('click', 'li:not(.add-item)', function () {
        $('#menu .menu-list li').removeClass('active');
        $(this).addClass('active');
    })

    $('#menu .menu-list').on('click','.add-item',function () {
        $(this).parent().prepend('<li>菜单名称</li>');
    })

    form.render();
    form.on('radio(menu-content)', function (data) {
        // console.log(data.elem); //得到radio原始DOM对象
        // console.log(data.value); //被点击的radio的value值
        if (data.value == 1) {
            $('.editor-wrapper').show();
            $('.redirect').hide();
        } else if (data.value == 2) {
            $('.editor-wrapper').hide();
            $('.redirect').show();
        }
    });
})