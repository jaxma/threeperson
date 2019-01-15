//移动端首页图片切换效果--初始化
$(function(){
    var mySwiper = new Swiper('#swiperIndexImage', {
        pagination: '.swiper-pagination',
        // autoplay: true,
        effect: 'fade',
        speed: 1000,
        autoplay : true,
    });
    
    isScrollY();
    classicProjectSwiper();
    //初始化-放大图片
    baguetteBox.run('#lightbox-context', {
        // Custom options
    }); 
    //懒加载图片lazy-load
    $("img.lazy-load").lazyload({
        effect: "fadeIn",
        threshold: 180
    });
})

//点击打开侧边栏
$(document).on("click","#open-menu-button",function(){
    var main_menu = $("#main-menu");
    var warp = $("#warp");
    warp.addClass("show-menu");
    main_menu.animate({left:"0px"});
})

//关闭侧边栏
$(document).on("click","#darken-shim",function(){
    var main_menu = $("#main-menu");
    var warp = $("#warp");
    warp.removeClass("show-menu");
    main_menu.animate({left:"-540px"});
})

//执行动画操作--滚动条
function isScrollY(){
    var docmentWidth = $(document).width();
    // var windowsHeight = $(window).width();
    if(docmentWidth >= 1800){
        setTimeout(function() {
            $('html,body').animate({scrollTop: "350px"},500);
        }, 500);
    }
    else if(docmentWidth > 1250){
        setTimeout(function() {
            $('html,body').animate({scrollTop: "150px"},500);
        }, 500);
    }
}

//项目新闻轮播--经典项目
function classicProjectSwiper(){
    // var slidesOffsetAfterData =  $(".swiper-wrapper-project").css("padding-left");
    // var slidesOffsetAfterDataInt = slidesOffsetAfterData.substring(0,slidesOffsetAfterData.length - 2);
    // var docmentWidth = $(document).width();
    var swiper2 = new Swiper('.project-collections-warp2', {
        pagination: '.swiper-pagination',
        slidesPerView: "auto",
        paginationClickable: true,
        freeMode: true,
        slidesOffsetAfter: 0,
        slidesOffsetBefter: 0,
        prevButton:'.swiper-button-prev',
        nextButton:'.swiper-button-next',
        watchOverflow: true,
        observer: true,//修改swiper自己或子元素时，自动初始化swiper
        observeParents: true,//修改swiper的父元素时，自动初始化swipe
        //width: parseInt(slidesOffsetAfterDataInt) + docmentWidth,
        navigation: {
            nextEl: '.swiper-button-next',//自动隐藏
            prevEl: '.swiper-button-prev',//自动隐藏
        },
        pagination: {
            el: '.swiper-pagination',//自动隐藏
        },
        on: {
            init: function(){
                
            }
        },
        breakpoints: { 
            //当宽度小于等于640
            480: {
                slidesPerView: 1,//一行显示3个
                slidesPerColumn: 12,//显示2行
                spaceBetween: 8,
            },
            640: {
                slidesPerView: 2,//一行显示3个
                slidesPerColumn:6,//显示2行
                spaceBetween: 8,
            },
            768: {
                slidesPerView: 3,//一行显示3个
                slidesPerColumn: 4,//显示2行
                spaceBetween: 15,
            },
            //当宽度小于等于1366
            1366: {
                slidesPerView: 4,//一行显示3个
                slidesPerColumn: 3,//显示2行
                spaceBetween: 15,
            },
            //当宽度小于等于1980
            1980: {
                slidesPerView: 6,//一行显示3个
                slidesPerColumn: 2,//显示2行
                spaceBetween: 15,
            }
        }
    });
}

//项目新闻轮播--经典项目
function otherProjectSwiper(){
    // var slidesOffsetAfterData =  $(".swiper-wrapper-project").css("padding-left");
    // var slidesOffsetAfterDataInt = slidesOffsetAfterData.substring(0,slidesOffsetAfterData.length - 2);
    // var docmentWidth = $(document).width();
    var swiper2 = new Swiper('.project-collections-warp', {
        pagination: '.swiper-pagination',
        slidesPerView: "auto",
        paginationClickable: true,
        freeMode: true,
        slidesOffsetAfter: 0,
        slidesOffsetBefter: 0,
        prevButton:'.swiper-button-prev',
        nextButton:'.swiper-button-next',
        watchOverflow: true,
        observer: true,//修改swiper自己或子元素时，自动初始化swiper
        observeParents: true,//修改swiper的父元素时，自动初始化swipe
        //width: parseInt(slidesOffsetAfterDataInt) + docmentWidth,
        navigation: {
            nextEl: '.swiper-button-next',//自动隐藏
            prevEl: '.swiper-button-prev',//自动隐藏
        },
        pagination: {
            el: '.swiper-pagination',//自动隐藏
        },
        on: {
            init: function(){
                
            }
        },
    });
}

// 点击项目分类切换
$(document).unbind("click","#portfolio-filter li").on("click","#portfolio-filter li",function(){
    $(this).addClass("active").siblings().removeClass("active");
    var type = $(this).data('type');
    if(type == 0){
        $(".otherProject").hide(0);
        $(".classicProject").show(0);
    }
    else{
        var index_ = $(this).index();
        $(".classicProject").hide(0);
        $(".otherProject").eq(index_ -1).show(0);
        otherProjectSwiper();
    }
})

//项目 新闻点击、
function handleProject(){
    var featuredCollections = $("#featured-collections");
    var featuredCollectionsTop = featuredCollections.offset().top;
    $('html,body').animate({scrollTop: featuredCollectionsTop - 30},500);
}

function handleNew(){
    var newCollections = $("#featured-new");
    var newCollectionsTop = newCollections.offset().top;
    $('html,body').animate({scrollTop: newCollectionsTop - 30},500);
}

// 点击头图---
function handleClickIndexImage(){
    var projectWarp = $("#project-warp");
    var projectWarpTop = projectWarp.offset().top;
    $('html,body').animate({scrollTop: projectWarpTop - 150},500);
}

//缩放
function singleProject(){
    var singleProject = $("#single-project");
    var godModeBg = $("#god-mode-bg");
    if(singleProject.hasClass('project-transform')){
        resetView();
    }
    else{
        $('html,body').animate({scrollTop: 0},200,function(){
            singleProject.addClass('project-transform page-context-wrap');
            godModeBg.css("opacity",1).css("display","block");
        });
    }
}

//重置缩放
function resetView(){
    var singleProject = $("#single-project");
    var godModeBg = $("#god-mode-bg");
    $('html,body').animate({scrollTop: 0},200,function(){
        singleProject.removeClass('project-transform').addClass("project-transformSmall");
        godModeBg.css("opacity",0).css("display","none");   
    });
}

//背景跟随滚动条滚动
$(window).on('scroll',function(){
    var width = $(window).width();
    if(width > 1280){
        var winTop = $(window).scrollTop();//滚动条滚动高度
        var handleClickIndexImage = $("#handleClickIndexImage .landscape");
        handleClickIndexImage.animate({top:0 - winTop},20);
    }
})
