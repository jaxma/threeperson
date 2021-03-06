<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo ($title); ?></title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/Admin_v3/css/newstatic/bootstrap.min.css">
	<link href="__PUBLIC__/Admin_v3/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="__PUBLIC__/Admin_v3/css/newstatic/swiper.min.css">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/Admin_v3/css/newstatic/main_styles.css?v=4.0.0">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/Admin_v3/css/newstatic/responsive.css">
	<style>
		.header_container_backgroundnone{
			background: none;
		}
		.header_container_backgroundnone .logo a,
		.header_container_backgroundnone .main_nav > ul > li.active > a,
		.header_container_backgroundnone .main_nav > ul > li > a,
		.header_container_backgroundnone .main_nav ul li.hassubs::after,
		.header_container_backgroundnone .hamburger i{
			color: #fff;
		}
		.logo a{
			background: url('__PUBLIC__/Admin_v3/image/white-logo.png') no-repeat center/ 100%;
		}
		.icon-round {
			position: relative;
			display: inline-block;
			vertical-align: middle;
			width: 24px;
			height: 24px;
			background-color: #a4afc2;
			border-radius: 50%;
			background-clip: padding-box;
			overflow: hidden;
			white-space: nowrap;
			transition: 250ms;
			line-height: 24px;
			text-align: center;
		}
		.footer_social ul li a.icon-round i {
			color: #fff;
		}
		.icon-round:hover {
			background-color: #2f4455
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
	<div class="warp">
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<!-- 循环经典项目 for item star-->
				<?php if(is_array($list)): foreach($list as $key=>$i): ?><div class="swiper-slide">
						<a class="handle-a" href="<?php echo ($i["href"]); ?>?lang=<?php echo ($lang); ?>">
							<img class="img swiper-lazy" data-src="__APP__<?php echo ($i["image"]); ?>" alt="">
							<div class="swiper-lazy-preloader"></div>
							<!-- 项目名称和地点 -->
							<p class="title"><?php if($lang == 1): echo ($i["title_en"]); else: echo ($i["title"]); endif; ?></span></p>
						</a>
					</div><?php endforeach; endif; ?>
				<!-- 循环经典项目 for item end-->
				<!-- <div class="swiper-slide">
					<a class="handle-a" href="#">
						<img class="img" src="__PUBLIC__/Admin_v3/image/test/7.jpg" alt="">
						<p class="title">项目1</p>
					</a>
				</div>
				<div class="swiper-slide">
					<a class="handle-a" href="#">
						<img class="img" src="__PUBLIC__/Admin_v3/image/test/3.jpg" alt="">
						<p class="title">项目1</p>
					</a>
				</div>
				<div class="swiper-slide">
					<a class="handle-a" href="#">
						<img class="img" src="__PUBLIC__/Admin_v3/image/test/4.jpg" alt="">
						<p class="title">项目1</p>
					</a>
				</div>
				<div class="swiper-slide">
					<a class="handle-a" href="#">
						<img class="img" src="__PUBLIC__/Admin_v3/image/test/5.jpg" alt="">
						<p class="title">项目1</p>
					</a>
				</div>
				<div class="swiper-slide">
					<a class="handle-a" href="#">
						<img class="img" src="__PUBLIC__/Admin_v3/image/test/6.jpg" alt="">
						<p class="title">项目1</p>
					</a>
				</div>
				<div class="swiper-slide">
					<a class="handle-a" href="#">
						<img class="img" src="__PUBLIC__/Admin_v3/image/test/8.jpg" alt="">
						<p class="title">项目1</p>
					</a>
				</div>
				<div class="swiper-slide">
					<a class="handle-a" href="#">
						<img class="img" src="__PUBLIC__/Admin_v3/image/test/9.jpg" alt="">
						<p class="title">项目1</p>
					</a>
				</div> -->
			</div>
			<!-- 如果需要分页器 -->
			<div class="swiper-pagination"></div>
			
			<!-- 如果需要导航按钮 -->
			<div class="swiper-button-prev swiper-button-white"></div>
			<div class="swiper-button-next swiper-button-white"></div>
		</div>

		
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