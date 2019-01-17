<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo ($title); ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
    <meta name="viewport" content="initial-scale=1">
    <link rel="stylesheet" href="__PUBLIC__/Admin_v3/css/static/baguetteBox.css">
    <link rel="stylesheet" href="__PUBLIC__/Admin_v3/css/static/swiper.min.4.6.css">
    <link rel="stylesheet" href="__PUBLIC__/Admin_v3/css/static/style.css">
	<meta name="msapplication-TileColor">
    <meta name="theme-color">
</head>
    <body class="near-bottom clippings-mobile-no-edit site-loaded clippings-instruction-on-hover" id="warp">
        <div class="warp">
            <div class="page-context home outgoing" id="home">
                <!-- banner图 star-->
                <div class="home-cover-image">
                    <!-- 轮播实现--介绍 -->
                    <div class="cover-images mod-slideshow swiper-container" id="swiperIndexImage">
                        <div class="swiper-wrapper">
                            <!-- 单独首页轮播图 item star-->
                            <?php if(is_array($mobile_photo)): foreach($mobile_photo as $key=>$i): ?><div class="swiper-slide">
                                <img class="mod-slideshow__image_1 lazy-load" src="__APP__<?php echo ($i); ?>">
                            </div><?php endforeach; endif; ?>
                        </div>
                    </div>
                    <!-- 轮播实现--介绍 -->

                    <!-- pc状态下的图片显示 -->
                    <img src="__APP__<?php echo ($index_photo["image"]); ?>" class="cover-image spacer">
                    <img src="__APP__<?php echo ($index_photo["image"]); ?>" class="cover-image transformer lazy-load landscape ready">
                    <!-- pc状态下的图片显示 -->
                    <img class="oka-wordmark" src="__PUBLIC__/Admin_v3/image/ok-wordmark-white-med.png">
                </div>
                <!-- banner图 end-->
                
                <!-- 介绍--TOPOS -->
                <div class="content-wrap">
                    <div class="site-strapline">
                        <!-- 介绍公司 -->
                        <?php if($lang == 1): echo ($introduct["content_en"]); else: echo ($introduct["content"]); endif; ?>
                        <!-- 介绍公司 -->
                        <div class="section-anchor-links">
                            <!-- 项目btn 需要中英文标注一下-->
                            <div class="projects-section-anchor homepage-section-link" onclick="handleProject()" data-section="featured-collections">
                                <?php if($lang == 1): ?>project<?php else: ?>项目<?php endif; ?><span class="down-arrow">&or;</span>
                            </div>
                            <div class="news-section-anchor homepage-section-link" onclick="handleNew()" data-section="news">
                                <?php if($lang == 1): ?>news<?php else: ?>新闻<?php endif; ?><span class="down-arrow">&or;</span>
                            </div>
                            <!-- 项目btn -->
                        </div>
                        <div class="olson-kundig-monogram"></div>
                    </div>
        
                    <div class="featured-collections" id="featured-collections">
                        <!-- 项目分类 -->
                        <div class="slider-wrap collection-">
                            <div class="slider-title-wrap">
                                <ul id="portfolio-filter">
                                    <li  class="active">
                                        <a href="javascript:void(0);" class="filter" data-type="0" data-filter=""><?php if($lang == 1): ?>classical project<?php else: ?>经典项目<?php endif; ?></a>
                                    </li> 
                                    <?php if(is_array($cats["0"]["cat"])): foreach($cats["0"]["cat"] as $k=>$i): ?><li class="">
                                            <a href="javascript:void(0);" class="filter" data-type="<?php echo ($k+1); ?>" data-filter=""><?php if($lang == 1): echo ($i["name_en"]); else: echo ($i["name"]); endif; ?></a>
                                        </li><?php endforeach; endif; ?>
                                </ul>
                            </div>
                            <!-- 经典项目 -->
                            <div class="project-content-left classicProject" data-type="classicProject">
                                <div class="slider collection- swiper-container project-collections-warp pageContent-ui">
                                    <div class="swiper-wrapper swiper-wrapper-project">
                                        <!-- 项目中的列子 item -->
                                    <?php if(is_array($cats["0"]["classical"])): foreach($cats["0"]["classical"] as $k=>$i): ?><div class="tile type-project swiper-slide">
                                            <a href="<?php echo U(__GROUP__.'/Detail/project',array('cat_id'=>$i['cat2'],'a_id'=>$i['id'],'lang'=>$lang));?>" class="history-link "
                                             data-collection-id="">
                                                <div class="tile-inner-wrap preload-image">
                                                    <div class="accent-colour" style="background-color: rgb(102, 10, 10);"></div>
                                                    <img src="__APP__<?php echo ($i["image"]); ?>">
                                                    <div class="type-icon">
                                                        <div class="icon-wrap"></div>
                                                    </div>
                                                </div>
                                                <div class="mask"></div>
                                                <div class="text">
                                                    <p class="head"><?php if($lang == 1): echo ($i["title_en"]); else: echo ($i["title"]); endif; ?></p>
                                                    <p class="subhead"><?php if($lang == 1): echo ($i["position_en"]); else: echo ($i["position"]); endif; ?></p>
                                                </div>
                                            </a>
                                        </div><?php endforeach; endif; ?>
                                    </div>
                                    <!-- <div class="slider-more-arrow"></div>
                                    <div class="slider-less-arrow"></div> -->
                                     <!-- 如果需要导航按钮 -->
                                    <!-- <div class="swiper-button-prev">
                                        <div class="prevBtn"></div>
                                    </div>
                                    <div class="swiper-button-next">
                                        <div class="nextBtn"></div>
                                    </div> -->
                                </div>
                            </div>
                            <!-- 经典项目 -->

                            <?php if(is_array($cats["0"]["cat"])): foreach($cats["0"]["cat"] as $k=>$i): ?><!-- 其他类别项目 for item star-->
                            <div class="project-content-left otherProject" data-type="otherProject">
                                <div class="slider collection- swiper-container project-collections-warp2 pageContent-ui">
                                    <div class="swiper-wrapper swiper-wrapper-project">
                                        <!-- 项目中的列子 item -->
                                        <?php if(is_array($i["items"])): foreach($i["items"] as $kk=>$ii): ?><div class="tile type-project swiper-slide">
                                            <a href="<?php echo U(__GROUP__.'/Detail/project',array('cat_id'=>$ii['cat2'],'a_id'=>$ii['id'],'lang'=>$lang));?>" class="history-link "
                                             data-collection-id="">
                                                <div class="tile-inner-wrap preload-image">
                                                    <div class="accent-colour" style="background-color: rgb(102, 10, 10);"></div>
                                                    <img src="__APP__<?php echo ($ii["image"]); ?>">
                                                    <div class="type-icon">
                                                        <div class="icon-wrap"></div>
                                                    </div>
                                                </div>
                                                <div class="mask"></div>
                                                <div class="text">
                                                    <p class="head"><?php if($lang == 1): echo ($ii["title_en"]); else: echo ($ii["title"]); endif; ?></p>
                                                    <p class="subhead"><?php if($lang == 1): echo ($ii["position_en"]); else: echo ($ii["position"]); endif; ?></p>
                                                </div>
                                            </a>
                                        </div><?php endforeach; endif; ?>
                                        <!-- 项目中的列子 -->
                                    </div>
                                    <!-- <div class="slider-more-arrow"></div>
                                    <div class="slider-less-arrow"></div> -->
                                     <!-- 如果需要导航按钮 -->
                                    <div class="swiper-button-prev">
                                        <div class="prevBtn"></div>
                                    </div>
                                    <div class="swiper-button-next">
                                        <div class="nextBtn"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- 其他类别项目 for item end --><?php endforeach; endif; ?>
                        </div>
                    </div>
                    <!-- 项目 -->
                    <?php if(is_array($cats["1"]["cat"])): foreach($cats["1"]["cat"] as $key=>$i): ?><!-- 新闻 -->
                    <div class="news" id="featured-new">
                        <h2><?php if($lang == 1): echo ($i["name_en"]); else: echo ($i["name"]); endif; ?></h2>
                        <div class="slider swiper-container project-collections-warp2 homepage-news__slider" style="overflow:visible">
                            <div class="swiper-wrapper">
                                <ul>
                                    <!-- 新闻文章 -->
                                    <?php if(is_array($i["items"])): foreach($i["items"] as $key=>$ii): ?><li class="homepage-news__tile homepage-news__tile--internal swiper-slide homepage-news__tile--portrait">
                                        <a href="">
                                            <img class="homepage-news__image" src="__APP__<?php echo ($ii["image"]); ?>" ?="">
                                            <div class="homepage-news__text-wrap">
                                                <span class="homepage-news__date"><?php if($lang == 1): echo ($ii["publish_time"]); else: echo ($ii["publish_time"]); endif; ?></span>
                                                <p class="homepage-news__title"><?php if($lang == 1): echo ($ii["title_en"]); else: echo ($ii["title"]); endif; ?></p>
                                            </div>
                                        </a>
                                    </li><?php endforeach; endif; ?>
                                        <!-- 新闻文章 -->
                                </ul>
                            </div>
                            <!-- <div class="slider-more-arrow"></div>
                            <div class="slider-less-arrow"></div> -->
                             <!-- 如果需要导航按钮 -->
                             <div class="swiper-button-prev" style="left: 0vw;">
                                <div class="prevBtn"></div>
                            </div>
                            <div class="swiper-button-next" style="right: 0vw;">
                                <div class="nextBtn"></div>
                            </div>
                        </div>
                    </div>
                     <!-- 新闻 end--><?php endforeach; endif; ?>
                    <div class="instagram">
                        <h2><?php if($lang == 1): ?>RECRUIT<?php else: ?>招聘<?php endif; ?></h2>
                        <!-- 招聘 -->
                        <div class="slider instagram-slider at-end at-start">
                            <ul style="transform: translate(0px, 0px) translateZ(0px);">

                            <?php if(is_array($cats["2"]["cat"]["1"]["items"])): foreach($cats["2"]["cat"]["1"]["items"] as $key=>$i): ?><li class="instagram-tile">
                                    <!-- <img width="450" height="450" src="http://www.toposla.com/rsc/images/1515202805-450x450.jpg" class="attachment-thumbnail size-thumbnail wp-post-image" alt=""  sizes="(max-width: 450px) 100vw, 450px">  -->
                                    <a href="javascript:void(0);">
                                        <div class="instagram-text">
                                            <span class="news-date"><?php if($lang == 1): echo ($i["publish_time"]); else: echo ($i["publish_time"]); endif; ?></span>
                                            <p><?php if($lang == 1): echo ($i["title_en"]); else: echo ($i["title"]); endif; ?></p>
                                        </div>
                                    </a>
                                </li><?php endforeach; endif; ?>
                            </ul>
                            <div class="slider-more-arrow"></div>
                            <div class="slider-less-arrow"></div>
                        </div>
                    </div>
                    <div class="footer">
                        <div class="col one">
                            <ul>
                                <li>
                                    <a href="mailto:info@toposla.com" target="blank">
                                        <?php if($lang == 1): ?>EMAIL<?php else: ?>邮箱<?php endif; ?> </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col two">
                            <ul>
                                <!-- <li><a href="http://www.facebook.com" target='blank'>Facebook</a></li> -->
                                <li><a href="https://www.facebook.com/Topos-Landscape-Architects-144788566223632/?modal=admin_todo_tour" target="blank">Facebook</a></li>
                                <li><a href="https://www.instagram.com/toposlandscapearchitects/" target="blank">Instagram</a></li>
                            </ul>
                        </div>
                        <div class="col three">
                            <p>
                                <?php echo ($address); ?><br>
                            </p>
                            <p>tel:<?php echo ($tel); ?></p>
                            <p><?php echo ($email); ?></p>
                        </div>
                        <div class="col four">
                            <span>
                                <?php echo ($en_address); ?>
                            </span>
                            <span>
                                tel: <?php echo ($en_tel); ?>
                            </span><span><?php echo ($en_email); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="darken-shim" id="darken-shim"></div>

        <!-- logo -->
        <div class="page-nav no-anchors" id="page-nav">
            <ul class="desktop-nav">
                <a href="/" class="">
                    <li class="olson-kundig-wordmark"></li>
                </a>
            </ul>
            <div class="mobile-nav">
                <span class="jump-to-section">Jump to Section<span class="down-arrow">▼</span></span>
                <select class="mobile-nav-select">
                </select>
            </div>
        </div>

        <!-- 侧边栏菜单 可共用-->
        <div class="left-context site-loaded" style="display:block;">
            <div class="cant-touch-this"></div>
            <div id="open-menu-button" class="open-menu-button"></div>
            <!-- 切换英文按钮 -->
            <div class="open2-menu2-button2" onclick="window.open('__GROUP__/Index/index?lang=1','_self')"></div>
            <!-- 切换英文按钮 -->
            <div class="go-back-button"></div>
            <div class="god-mode-button" style="display: none;"></div>
    
            <div class="clippings-here-instruction">
                View, edit and share your clippings here
            </div>
            <div class="clippings-context-toggle hidden">
                <div class="outline"></div>
    
                <span class="clippings-count current">0</span>
            </div>
            <div class="main-menu" id="main-menu" style="transform: translate3d(0px, 0px, 0px);">
                <div class="top">
                    <!-- 改成首页的链接 -->
                    <a href="http://www.toposla.com/">
                        <img class="oka-wordmark" src="__PUBLIC__/Admin_v3/image/toposla_black.png">
                    </a>
                    <div class="show-search-button">
                    </div>
                </div>
                <div class="right">
                    <!-- 地址 -->
                    <div class="menu-section">
                        <div class="contact-section address">
                            <p><?php echo ($en_position); ?></p>
                            <p>
                                <?php echo ($en_address); ?><br> </p>
                            <p>
                                tel: <?php echo ($en_tel); ?><br> </p>
                            <p>
                                <?php echo ($en_email); ?><br> </p>
                        </div>
    
                        <div class="contact-section address">
                            <p><?php echo ($position); ?></p>
                            <p>
                                <?php echo ($address); ?><br> </p>
                            <p>
                                tel:<?php echo ($tel); ?><br> </p>
                            <p>
                                <?php echo ($email); ?><br> </p>
                        </div>
    
                        <nav class="contact-section">
                            <ul class="social-links">
                                <li class="facebook"><a href="https://www.facebook.com/Topos-Landscape-Architects-144788566223632/?modal=admin_todo_tour" target="blank"></a></li>
                                <li class="instagram"><a href="https://www.instagram.com/toposlandscapearchitects/" target="blank"></a></li>
                            </ul>
                        </nav>
                        <a href="http://www.toposla.com/#" class="toggle-contact"><span class="see-more">More Contact Details</span><span class="see-less">Hide</span></a>
                    </div>
                </div>
                <div class="left">
                    <?php if(is_array($cats)): foreach($cats as $key=>$i): ?><nav class="menu-section">
                            <h1><?php if($lang == 1): echo ($i["name_en"]); else: echo ($i["name"]); endif; ?></h1>
                            <ul>
                                <?php if($i["id"] == 1): ?><li>
                                        <a class="menu-link" href="<?php echo U(__GROUP__.'/Detail/projectlist',array('cat_id'=>'classical','lang'=>$lang));?>">
                                            &nbsp;&nbsp;<?php if($lang == 1): ?>classical project<?php else: ?>经典项目<?php endif; ?> 
                                        </a>
                                    </li><?php endif; ?> 
                                <?php if(is_array($i["cat"])): foreach($i["cat"] as $key=>$ii): ?><li>
                                        <a class="menu-link" href="<?php echo U(__GROUP__.'/Detail/projectlist',array('cat_id'=>$ii['id'],'lang'=>$lang));?>">
                                            &nbsp;&nbsp;<?php if($lang == 1): echo ($ii["name_en"]); else: echo ($ii["name"]); endif; ?> 
                                        </a>
                                    </li><?php endforeach; endif; ?>
                            </ul>
                        </nav><?php endforeach; endif; ?>
                </div>
    
            </div>
    
        </div>
        <script type='text/javascript' src='__PUBLIC__/Admin_v3/js/jquery-2.1.1.min.js' charset='utf-8'></script>
        <script src="__PUBLIC__/Admin_v3/js/static/swiper.min.4.6.js"></script>
        <script src="__PUBLIC__/Admin_v3/js/static/baguetteBox.js"></script>
        <script src="__PUBLIC__/Admin_v3/js/static/jquery.lazyload.min.js"></script>
        <script src="__PUBLIC__/Admin_v3/js/static/main.js"></script>
        <script>
            //显示logo
            $(window).on('scroll',function(){
                var winTop = $(window).scrollTop();//滚动条滚动高度
                var featuredCollections = $("#featured-collections");
                var featuredCollectionsTop = featuredCollections.offset().top;
                if((featuredCollectionsTop - winTop) <= 0){
                    $("#page-nav").show(0);
                }
                else{
                    $("#page-nav").hide(0);
                }
            })
        </script>
    </body>
</html>