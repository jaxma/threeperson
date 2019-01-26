// swiper 

$(document).ready(function(){
	var header = $('.header');
	var hambActive = false;
	var menuActive = false;
	var flagswitch = false;
	setHeader();

	$(window).on('resize', function()
	{
		setHeader();
	});

	$(document).on('scroll', function()
	{
		flagswitch = true;
		setHeader();
	});

	initMenu();
	initIsotope();
	initSearch();

	function setHeader()
	{
		var header_ = $("#header");
		var windowsWidth =  $(window).width();
		if(windowsWidth < 668){
			$('#logo_').addClass('logoBlack');
			return false;
		}
		if($(window).scrollTop() > 100)
		{
			header.addClass('scrolled');
			if(header_.hasClass('header_container_backgroundnone') && typeof(flagSwitch) != 'undefined'){
				header_.removeClass('header_container_backgroundnone');	
				$(".search_icon").removeClass('search_ff');
				if(typeof(flagSwitch) != 'undefined'){
					$('#logo_').addClass('logoBlack');
				}
			}
		}
		else if($(window).scrollTop() == 0  && flagswitch && typeof(flagSwitch) != 'undefined'){
			header_.addClass('header_container_backgroundnone');	
			header.removeClass('scrolled');
			$(".search_icon").addClass('search_ff');
			if(typeof(flagSwitch) != 'undefined'){
				$('#logo_').removeClass('logoBlack');
			}
		}
		else
		{
			header.removeClass('scrolled');

		}
	}

	function initSearch(){
		if($('.search').length && $('.search_panel').length)
		{
			var search = $('.search');
			var panel = $('.search_panel');
			var header = $("#header");
			search.on('click', function()
			{	
				$(this).toggleClass('search_active');
				header.toggleClass('header_container_backgroundnone');
				panel.toggleClass('active');
			});
		}
	}

	function initMenu(){
		if($('.hamburger').length)
		{
			var hamb = $('.hamburger');

			hamb.on('click', function(event)
			{
				event.stopPropagation();

				if(!menuActive)
				{
					openMenu();
					
					$(document).one('click', function cls(e)
					{
						if($(e.target).hasClass('menu_mm'))
						{
							$(document).one('click', cls);
						}
						else
						{
							closeMenu();
						}
					});
				}
				else
				{
					$('.menu').removeClass('active');
					menuActive = false;
				}
			});

			if($('.page_menu_item').length)
			{
				var items = $('.page_menu_item');
				items.each(function()
				{
					var item = $(this);

					item.on('click', function(evt)
					{
						if(item.hasClass('has-children'))
						{
							evt.preventDefault();
							evt.stopPropagation();
							var subItem = item.find('> ul');
						    if(subItem.hasClass('active'))
						    {
						    	subItem.toggleClass('active');
								//TweenMax.to(subItem, 0.3, {height:0});
						    }
						    else
						    {
						    	subItem.toggleClass('active');
						    	//TweenMax.set(subItem, {height:"auto"});
								//TweenMax.from(subItem, 0.3, {height:0});
						    }
						}
						else
						{
							evt.stopPropagation();
						}
					});
				});
			}
		}
	}

	function openMenu()
	{
		var fs = $('.menu');
		fs.addClass('active');
		hambActive = true;
		menuActive = true;
	}

	function closeMenu()
	{
		var fs = $('.menu');
		fs.removeClass('active');
		hambActive = false;
		menuActive = false;
	}

	function initIsotope()
	{
		var sortingButtons = $('.product_sorting_btn');
		var sortNums = $('.num_sorting_btn');

		if($('.product_grid').length)
		{
			var grid = $('.product_grid').isotope({
				itemSelector: '.product',
				layoutMode: 'fitRows',
				fitRows:
				{
					gutter: 30
				},
	            getSortData:
	            {
	            	price: function(itemElement)
	            	{
	            		var priceEle = $(itemElement).find('.product_price').text().replace( '$', '' );
	            		return parseFloat(priceEle);
	            	},
	            	name: '.product_name',
	            	stars: function(itemElement)
	            	{
	            		var starsEle = $(itemElement).find('.rating');
	            		var stars = starsEle.attr("data-rating");
	            		return stars;
	            	}
	            },
	            animationOptions:
	            {
	                duration: 750,
	                easing: 'linear',
	                queue: false
	            }
	        });
		}
	}
});


//搜索事件
$('.search_input').bind('keyup', function(event) {
	var word = $(this).val();
	if(word){
		if (event.keyCode == "13") {
			location.href = '/search/index.html?word='+ word
		}
	}
});
