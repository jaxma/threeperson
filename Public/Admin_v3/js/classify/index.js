$(function(){
	var mySwiper = new Swiper('.swiper-container', {
          pagination: '.swiper-pagination',
          paginationClickable: true,
          zoom: true,
          preloadImages: true,
          lazyLoading: true,
          autoplay: 3500,
          effect: 'fade'
  });
})