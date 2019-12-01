<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo ($title); ?></title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="TOPOS project">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/Admin_v3/css/newstatic/bootstrap.min.css">
    <link href="__PUBLIC__/Admin_v3/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="__PUBLIC__/Admin_v3/css/newstatic/swiper.min.css">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/Admin_v3/css/newstatic/main2.css">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/Admin_v3/css/newstatic/main_styles.css">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/Admin_v3/css/newstatic/responsive.css">
	<style>
		.search_ff svg{
			fill: #a4afc2;
		}
	</style>
</head>

    
<body>
<div class="super_container">
	<!-- Header -->
	<header class="header">
		<div class="header_container header_container_backgroundnone" id="header">
			<div class="container-fluid">
				<div class="row">
					<div class="col">
						<div class="header_content d-flex flex-row align-items-center justify-content-start">
							<h1 class="logo">
								<a href="<?php echo U(__GROUP__.'/Index/index',array('lang'=>$lang));?>"></a>
							</h1>
							<nav class="main_nav">
								<ul>
									<!-- <li class="active">
										<a href="">首页/HOME</a>
									</li> -->
									<li <?php if($module_name == project): ?>class="active"<?php endif; ?>>
										<a href="<?php echo U(__GROUP__.'/Project/index',array('lang'=>$lang));?>"><?php if($lang == 1): ?>PROJECTS<?php else: ?>项目<?php endif; ?></a>
										<!-- <ul>
											<li><a href="categories.html">经典项目</a></li>
											<li><a href="categories.html">经典项目</a></li>
											<li><a href="categories.html">经典项目</a></li>
											<li><a href="categories.html">经典项目</a></li>
											<li><a href="categories.html">经典项目</a></li>
										</ul> -->
									</li>
									<li <?php if($module_name == about): ?>class="active"<?php endif; ?>><a href="<?php echo U(__GROUP__.'/About/index',array('lang'=>$lang));?>"><?php if($lang == 1): ?>ABOUT<?php else: ?>关于TOPOS<?php endif; ?></a></li>
									<li <?php if($module_name == news): ?>class="active"<?php endif; ?>><a href="<?php echo U(__GROUP__.'/News/index',array('lang'=>$lang));?>"><?php if($lang == 1): ?>NEWS<?php else: ?>新闻<?php endif; ?></a></li>
									<li <?php if($module_name == contact): ?>class="active"<?php endif; ?>><a href="<?php echo U(__GROUP__.'/Contact/index',array('lang'=>$lang));?>"><?php if($lang == 1): ?>CONTACT<?php else: ?>联系我们<?php endif; ?></a></li>
									<li style="margin-right: 0;"><a href="<?php echo ($lang_url); ?>"><?php if($lang == 1): ?>中文<?php else: ?>English<?php endif; ?></a></li>
									<li>
										<a href="javascript:void(0)" class="search">
											<div class="search_icon search_ff">
													<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
													viewBox="0 0 475.084 475.084" style="enable-background:new 0 0 475.084 475.084;"
														xml:space="preserve">
													<g>
														<path d="M464.524,412.846l-97.929-97.925c23.6-34.068,35.406-72.047,35.406-113.917c0-27.218-5.284-53.249-15.852-78.087
															c-10.561-24.842-24.838-46.254-42.825-64.241c-17.987-17.987-39.396-32.264-64.233-42.826
															C254.246,5.285,228.217,0.003,200.999,0.003c-27.216,0-53.247,5.282-78.085,15.847C98.072,26.412,76.66,40.689,58.673,58.676
															c-17.989,17.987-32.264,39.403-42.827,64.241C5.282,147.758,0,173.786,0,201.004c0,27.216,5.282,53.238,15.846,78.083
															c10.562,24.838,24.838,46.247,42.827,64.234c17.987,17.993,39.403,32.264,64.241,42.832c24.841,10.563,50.869,15.844,78.085,15.844
															c41.879,0,79.852-11.807,113.922-35.405l97.929,97.641c6.852,7.231,15.406,10.849,25.693,10.849
															c9.897,0,18.467-3.617,25.694-10.849c7.23-7.23,10.848-15.796,10.848-25.693C475.088,428.458,471.567,419.889,464.524,412.846z
																M291.363,291.358c-25.029,25.033-55.148,37.549-90.364,37.549c-35.21,0-65.329-12.519-90.36-37.549
															c-25.031-25.029-37.546-55.144-37.546-90.36c0-35.21,12.518-65.334,37.546-90.36c25.026-25.032,55.15-37.546,90.36-37.546
															c35.212,0,65.331,12.519,90.364,37.546c25.033,25.026,37.548,55.15,37.548,90.36C328.911,236.214,316.392,266.329,291.363,291.358z
															"/>
													</g>
												</svg>
											</div>
										</a>
									</li>
								</ul>
							</nav>
							<div class="header_extra ml-auto">
								<div class="hamburger"><i class="fa fa-bars" aria-hidden="true"></i></div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Social -->
		<!-- Search Panel -->
		<div class="search_panel trans_300">
				<div class="container-fluid">
					<div class="row">
						<div class="col">
							<div class="search_panel_content d-flex flex-row align-items-center justify-content-end">
								<form action="#">
									<input type="text" class="search_input" placeholder="Search" required="required">
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
	</header>

	<!-- Menu -->

	<div class="menu menu_mm trans_300">
		<div class="menu_container menu_mm">
			<div class="page_menu_content">
				<div class="page_menu_search menu_mm">
					<form action="#">
						<input type="search" required="required" class="page_menu_search_input menu_mm" placeholder="Search for products...">
					</form>
				</div>
				<ul class="page_menu_nav menu_mm">
					<li class="page_menu_item menu_mm">
						<a href="/?lang=<?php echo ($lang); ?>"><?php if($lang == 1): ?>HOME<?php else: ?>主页<?php endif; ?><i class="fa"></i></a>
					</li>
					<li class="page_menu_item menu_mm">
						<a href="<?php echo U(__GROUP__.'/Project/index',array('lang'=>$lang));?>"><?php if($lang == 1): ?>PROJECTS<?php else: ?>项目<?php endif; ?><i class="fa"></i></a>
						<!-- <ul class="page_menu_selection menu_mm">
							<li class="page_menu_item menu_mm"><a href="categories.html">经典项目<i class="fa fa-angle-down"></i></a></li>
							<li class="page_menu_item menu_mm"><a href="categories.html">经典项目<i class="fa fa-angle-down"></i></a></li>
							<li class="page_menu_item menu_mm"><a href="categories.html">经典项目<i class="fa fa-angle-down"></i></a></li>
							<li class="page_menu_item menu_mm"><a href="categories.html">经典项目<i class="fa fa-angle-down"></i></a></li>
						</ul> -->
					</li>
					<li class="page_menu_item menu_mm"><a href="<?php echo U(__GROUP__.'/About/index',array('lang'=>$lang));?>"><?php if($lang == 1): ?>ABOUT<?php else: ?>关于TOPOS<?php endif; ?><i class="fa fa-angle-down"></i></a></li>
					<li class="page_menu_item menu_mm"><a href="<?php echo U(__GROUP__.'/News/index',array('lang'=>$lang));?>"><?php if($lang == 1): ?>NEWS<?php else: ?>新闻<?php endif; ?><i class="fa fa-angle-down"></i></a></li>
					<li class="page_menu_item menu_mm"><a href="<?php echo U(__GROUP__.'/Contact/index',array('lang'=>$lang));?>"><?php if($lang == 1): ?>CONTACT<?php else: ?>联系我们<?php endif; ?><i class="fa fa-angle-down"></i></a></li>
					<li class="page_menu_item menu_mm"><a href="<?php echo ($lang_url); ?>"><?php if($lang == 1): ?>中文<?php else: ?>English<?php endif; ?><i class="fa fa-angle-down"></i></a></li>
				</ul>
			</div>
		</div>

		<div class="menu_close"><i class="fa fa-times" aria-hidden="true"></i></div>

		<div class="menu_social" style="display:none;">
			<ul>
				<li><a href="#"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
				<li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
				<li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
				<li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
			</ul>
		</div>
	</div>


    <!-- home -->
    <div id="container">
        <div class="content-bound standard-layout grid" id="news">
            <!-- <div class="page-title">
                <div class="breadcrumbs">
                    <a>
                        <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">目前</font>
                        </font>
                    </a>
                    <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;"> &gt;
                        </font>
                    </font>
                </div>
                <h1>
                    <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">新闻/news</font>
                    </font>
                </h1>
            </div> -->
            <div class="row">
                <div class="news-listing">
					<div class="news-partial">
						<h3>
							<!-- title 标题-->
							<a href="#">
								<font style="vertical-align: inherit;">
								<?php if($lang == 1): echo ($row4["name"]); else: echo ($row3["name"]); endif; ?>
								</font>
							</a>
						</h3>
						<a href="javascript:void(0);">
							<div class="image-srcset image-srcset_1 a2x1_1 a1x1">
								<img src="<?php echo ($contact_pic["content"]); ?>" >
							</div>
						</a>
						<div class="contact-content">
							<!-- <p><?php echo ($desc); ?></p>
							<p><?php echo ($address); ?></p>
							<p><?php echo ($tel); ?></p>
							<p><?php echo ($email); ?></p>
							<p><?php if($lang == 1): echo ($row2["name"]); else: echo ($row1["name"]); endif; ?></p>
							<p><?php if($lang == 1): echo ($row2["name_en"]); else: echo ($row1["name_en"]); endif; ?></p> -->
							<?php if($lang == 1): echo ($contact_en1); else: echo ($contact_cn1); endif; ?>
						</div>
					</div>

					<div class="news-partial">
						<h3>
							<!-- title 标题-->
							<a href="#">
								<font style="vertical-align: inherit;">
								<?php if($lang == 1): echo ($row4["name_en"]); else: echo ($row3["name_en"]); endif; ?>
								</font>
							</a>
						</h3>
						<a href="javascript:void(0);">
							<div class="image-srcset image-srcset_1 a2x1_1 a3x2">
								<img src="<?php echo ($contact_pic_aboard["content"]); ?>" >
							</div>
						</a>
						<div class="contact-content">
							<!-- <p><?php echo ($en_desc); ?></p>
							<p><?php echo ($en_address); ?></p>
							<p><?php echo ($en_tel); ?></p>
							<p><?php echo ($en_email); ?></p>
							<p><?php if($lang == 1): echo ($row2["content"]); else: echo ($row1["content"]); endif; ?></p>
							<p><?php if($lang == 1): echo ($row2["content_en"]); else: echo ($row1["content_en"]); endif; ?></p> -->
							<?php if($lang == 1): echo ($contact_en2); else: echo ($contact_cn2); endif; ?>
						</div>
					</div>

					<div class="news-partial" style="border-bottom: none;">
						<!-- <h3>
							<a href="#">
								<font style="vertical-align: inherit;">
									招聘
								</font>
							</a>
						</h3> -->
						<div class="contact-content">
							<!-- 描述 des -->
							<!-- address 地址 -->
							<!-- <p>想了解更多关于我们的信息，欢迎扫描下面的二维码</p>
							<p>我们会不定期在公众号里面发布招聘计划、相关信息。</p>
							<p>如有简历及作品集，欢迎将材料发送至邮箱： info@toposla.com</p>
							<p>微信公众号 TOPOS拓柏景观</p> -->
							<?php if($lang == 1): echo ($contact_recruitment_en); else: echo ($contact_recruitment); endif; ?>
						</div>
						<a href="javascript:void(0);">
							<div class="image-srcset image-srcset_1 a2x1_1 a3x2" style="background: #fff;">
								<img style="width: 150px;height: 150px;" src="<?php echo ($contact_pic_qrcode["content"]); ?>" >
							</div>
						</a>
					</div>
                </div>
            </div>
        </div>
    </div>
    
    <!--home-->

	<!-- Footer -->
	
    
		<div class="footer_overlay"></div>
		<footer class="footer">
			<div class="footer_background"></div>
			<div class="container">
				<div class="row">
					<div class="col">
						<div class="footer_content d-flex flex-lg-row flex-column align-items-center justify-content-lg-start justify-content-center">
							<div class="footer_logo"><a href="mailto:info@toposla.com">info@toposla.com</a></div>
							<div class="copyright ml-auto mr-auto">
								Copyright <script>document.write(new Date().getFullYear());</script> TOPOS Landscape Architects
							</div>
							<div class="footer_social ml-lg-auto" style="margin-right: inherit;">
								<ul style="padding-left: 0;margin-left: 0;">
									<!-- <li><a href="https://weibo.com/6329623101/manage"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
									<li><a href="https://www.instagram.com/topos_163/"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
									<li><a href="https://www.facebook.com/damin.pang.5"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
									<li><a href="https://twitter.com/TOPOS13757631?lang=en"><i class="fa fa-twitter" aria-hidden="true"></i></a></li> -->
									<?php if(is_array($foot_icons)): foreach($foot_icons as $key=>$i): ?><li><a class="icon-round" target="_blank" href="<?php echo ($i["href"]); ?>" ><i class="fa fa-<?php echo ($i["title"]); ?>" aria-hidden="true"></i></a></li><?php endforeach; endif; ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</div>

	<!-- Footer -->
	
	<!--  -->
	<!-- Footer -->
	
	<!-- Footer -->
</div>

<script src="__PUBLIC__/Admin_v3/js/jquery-1.11.2.min.js"></script>
<script src="__PUBLIC__/Admin_v3/js/swiper.min.js"></script>
<script src="__PUBLIC__/Admin_v3/js/static/popper.js"></script>
<script src="__PUBLIC__/Admin_v3/js/static/bootstrap.min.js"></script>
<script src="__PUBLIC__/Admin_v3/js/static/custom.js"></script>
<script src="__PUBLIC__/Admin_v3/js/static/jquery.lazyload.min.js"></script>
<script>
	var mySwiper = new Swiper ('.swiper-container', {
		pagination: '.swiper-pagination',
		speed: 500,
		autoplay : 4000,
		loop: true,
		effect: 'fade',
		paginationClickable: true,
		keyboardControl : true,
		height : window.innerHeight,
		nextButton: '.swiper-button-next',
		prevButton: '.swiper-button-prev',
		lazyLoading : true,
		lazyLoadingInPrevNext : true,
		lazyLoadingInPrevNextAmount : 3,
	})

	$(".image-srcset img").lazyload({
        effect: "fadeIn",
        threshold: 300
	});
	
	//搜索事件
	$('.search_input').bind('keyup', function(event) {
		var word = $(this).val();
		if(word){
			if (event.keyCode == "13") {
				location.href = '/search/index.html?word='+ word + '&lang=<?php echo ($lang); ?>'
			}
		}
	});
</script>
</body>
</html>