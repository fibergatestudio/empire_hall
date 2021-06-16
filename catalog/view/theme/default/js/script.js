var Tabs = function () {
	function Tabs(options) {
		_classCallCheck(this, Tabs);
		
		var defaultOption = {
			selector: ".tabs-list",
			activeClass: "active",
			checkHash: true,
			tabLinks: "a",
			attribute: "href",
			event: "click",
			onChange: null
		};
		this.options = _extends({}, defaultOption, options);
		
		return this.init(this.options);
	}
	
	_createClass(Tabs, [{
		key: "init",
		value: function init(options) {
			var _this = this;
			
			var tabs = document.querySelectorAll(options.selector);
			tabs.forEach(function (element) {
				_this.setInitialState(element);
			});
		}
		}, {
		key: "update",
		value: function update(selector) {
			var _this2 = this;
			
			var tabs = document.querySelectorAll(selector || this.options.selector);
			tabs.forEach(function (element) {
				_this2.setInitialState(element);
			});
		}
		}, {
		key: "setInitialState",
		value: function setInitialState(element) {
			var _this3 = this;
			
			var links = element.querySelectorAll(this.options.tabLinks);
			this.addEvents(links);
			var historyLink = null;
			if (this.options.checkHash && window.location.hash) {
				historyLink = element.querySelector("[" + this.options.attribute + "=\"" + window.location.hash + "\"]");
			}
			if (historyLink) {
				this.setActiveTab(historyLink);
				} else {
				links.forEach(function (link, index) {
					if (index === 0) {
						_this3.setActiveTab(link);
					}
				});
			}
		}
		}, {
		key: "addEvents",
		value: function addEvents(links) {
			var _this4 = this;
			
			links.forEach(function (link) {
				link.addEventListener(_this4.options.event, function (event) {
					event.preventDefault();
					if (!event.currentTarget.classList.contains(_this4.options.activeClass)) {
						_this4.setActiveTab(link);
					}
				});
			});
		}
		}, {
		key: "setActiveTab",
		value: function setActiveTab(activeTab) {
			activeTab.classList.add(this.options.activeClass);
			var activeTabID = activeTab.getAttribute(this.options.attribute);
			if (activeTabID === "#") return;
			var activeTabBlock = document.querySelector(activeTabID);
			if (activeTabBlock) {
				activeTabBlock.classList.add("active");
			}
			this.removeTabs(activeTab);
			if (typeof this.options.onChange === "function") {
				this.options.onChange();
			}
		}
		}, {
		key: "removeTabs",
		value: function removeTabs(activeTab) {
			var _this5 = this;
			
			var tabNav = activeTab.closest(this.options.selector);
			tabNav.querySelectorAll(this.options.tabLinks).forEach(function (element) {
				if (element !== activeTab) {
					element.classList.remove("active");
					var tabID = element.getAttribute(_this5.options.attribute);
					var tabBlock = document.querySelector(tabID);
					if (tabBlock) {
						tabBlock.classList.remove("active");
					}
				}
			});
		}
	}]);
	
	return Tabs;
}();


function responseFunc() {
	var windowWidth = window.innerWidth;
	
	if (windowWidth < 768) {
		if ($('.header__right .language').length) {
			$('.mobile-language').append($('.header__right .language'));
		}
		} else if ($('.mobile-language .language').length) {
		$('.mobile-language .language').insertAfter($('.header-search'));
	}
	if (windowWidth < 1024) {
		$('.main-nav__item.main-nav__item--shop>a').on('click', function (e) {
			e.preventDefault();
		});
	}
}



$(document).ready(function () {
	$('.product-quantity').numberPicker();
	
	customSelect = new Select();
	
	$('.main-section__slider').slick({
        slidesToShow: 1,
        fade: true,
        speed: 800,
        arrows: true,
        infinite: true,
        dots: true,
        autoplay: false,
        prevArrow: '<div class="main-section__arrow prev-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M6.04651 12L7.10465 10.95L2.87209 6.75L50 6.75L50 5.25L2.87209 5.25L7.10465 1.05L6.04651 -3.84254e-06L5.24537e-07 6L6.04651 12Z" /></svg></div>',
        nextArrow: '<div class="main-section__arrow next-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M43.9535 0L42.8953 1.05L47.1279 5.25H0V6.75H47.1279L42.8953 10.95L43.9535 12L50 6L43.9535 0Z" /></svg></div>',
        rows: 0,
        responsive: [{
            breakpoint: 1024,
            settings: {
                arrows: false
			}
		}]
	});
	
	$('.main-brands__list').slick({
		slidesToShow: 5,
		speed: 800,
		arrows: true,
		infinite: true,
		dots: false,
		prevArrow: '<div class="main-brands__arrow prev-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M6.04651 12L7.10465 10.95L2.87209 6.75L50 6.75L50 5.25L2.87209 5.25L7.10465 1.05L6.04651 -3.84254e-06L5.24537e-07 6L6.04651 12Z" /></svg></div>',
		nextArrow: '<div class="main-brands__arrow next-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M43.9535 0L42.8953 1.05L47.1279 5.25H0V6.75H47.1279L42.8953 10.95L43.9535 12L50 6L43.9535 0Z" /></svg></div>',
		rows: 0,
		responsive: [{
			breakpoint: 1250,
			settings: {
				slidesToShow: 4
			}
			}, {
			breakpoint: 1024,
			settings: {
				slidesToShow: 3,
				variableWidth: false
			}
			}, {
			breakpoint: 768,
			settings: {
				slidesToShow: 2,
				variableWidth: false
			}
			}, {
			breakpoint: 520,
			settings: {
				slidesToShow: 1,
				variableWidth: false
			}
		}]
	});
	
	$('.popular-products__slider').slick({
		slidesToShow: 3,
		speed: 800,
		arrows: true,
		infinite: true,
		dots: false,
		prevArrow: $('.popular-products__navigation .prev-arrow').html('<div class="popular-products__arrow popular-products__prev-arrow"><svg viewBox="0 0 100 100" style="display: block; width: 100%;"><path d="M 50,50 m 0,-49.5 a 49.5,49.5 0 1 1 0,99 a 49.5,49.5 0 1 1 0,-99" stroke="#c4c4c4" stroke-width="1" fill-opacity="0" ></path></svg></div>'),
		nextArrow: $('.popular-products__navigation .next-arrow').html('<div class="popular-products__arrow popular-products__next-arrow"><svg viewBox="0 0 100 100" style="display: block; width: 100%;"><path d="M 50,50 m 0,-49.5 a 49.5,49.5 0 1 1 0,99 a 49.5,49.5 0 1 1 0,-99" stroke="#c4c4c4" stroke-width="1" fill-opacity="0" ></path></svg></div>'),
		rows: 0,
		responsive: [{
			breakpoint: 1024,
			settings: {
				slidesToShow: 2
			}
			}, {
			breakpoint: 600,
			settings: {
				slidesToShow: 1,
				arrows: false,
				dots: true
			}
		}]
	});
	
	if ($('.popular-products__slider .product__item').length <= 3) {
		$('.popular-products__slider').addClass('no-slider');
		$('.popular-products__navigation').hide();
		$('.popular-products__slider').slick('unslick');
	}
	
	$('.main-inspiration__nav-list').slick({
		slidesToShow: 3,
		speed: 800,
		arrows: true,
		infinite: true,
		dots: false,
		centerMode: true,
		focusOnSelect: true,
		prevArrow: '<div class="main-brands__arrow prev-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M6.04651 12L7.10465 10.95L2.87209 6.75L50 6.75L50 5.25L2.87209 5.25L7.10465 1.05L6.04651 -3.84254e-06L5.24537e-07 6L6.04651 12Z" /></svg></div>',
		nextArrow: '<div class="main-brands__arrow next-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M43.9535 0L42.8953 1.05L47.1279 5.25H0V6.75H47.1279L42.8953 10.95L43.9535 12L50 6L43.9535 0Z" /></svg></div>',
		rows: 0,
		asNavFor: '.main-inspiration__slider',
		responsive: [{
			breakpoint: 1024,
			settings: {
				variableWidth: true
			}
			}, {
			breakpoint: 768,
			settings: {
				slidesToShow: 1,
				variableWidth: false
			}
		}]
	});
	
	$('.main-inspiration__slider').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		asNavFor: '.main-inspiration__nav-list',
		dots: false,
		fade: true,
		rows: 0
	});
	
	$('.main-inspiration__products').each(function eachSliders() {
		$(this).slick({
			slidesToShow: 1,
			fade: true,
			speed: 800,
			arrows: true,
			infinite: true,
			autoplay: true,
			autoplaySpeed: 3000,
			dots: true,
			prevArrow: '<div class="popular-products__arrow popular-products__prev-arrow"><svg viewBox="0 0 100 100" style="display: block; width: 100%;"><path d="M 50,50 m 0,-49.5 a 49.5,49.5 0 1 1 0,99 a 49.5,49.5 0 1 1 0,-99" stroke="#c4c4c4" stroke-width="1" fill-opacity="0" ></path></svg></div>',
			nextArrow: '<div class="popular-products__arrow popular-products__next-arrow"><svg viewBox="0 0 100 100" style="display: block; width: 100%;"><path d="M 50,50 m 0,-49.5 a 49.5,49.5 0 1 1 0,99 a 49.5,49.5 0 1 1 0,-99" stroke="#c4c4c4" stroke-width="1" fill-opacity="0" ></path></svg></div>',
			rows: 0
		});
	});
	
	$('.main-advantages__list').slick({
		slidesToShow: 3,
		speed: 800,
		arrows: true,
		infinite: true,
		dots: true,
		prevArrow: $('.main-advantages__navigation .prev-arrow').html('<div class="popular-products__arrow popular-products__prev-arrow"><svg viewBox="0 0 100 100" style="display: block; width: 100%;"><path d="M 50,50 m 0,-49.5 a 49.5,49.5 0 1 1 0,99 a 49.5,49.5 0 1 1 0,-99" stroke="#C4C4C4" stroke-width="1" fill-opacity="0" ></path></svg></div>'),
		nextArrow: $('.main-advantages__navigation .next-arrow').html('<div class="popular-products__arrow popular-products__next-arrow"><svg viewBox="0 0 100 100" style="display: block; width: 100%;"><path d="M 50,50 m 0,-49.5 a 49.5,49.5 0 1 1 0,99 a 49.5,49.5 0 1 1 0,-99" stroke="#C4C4C4" stroke-width="1" fill-opacity="0" ></path></svg></div>'),
		rows: 0,
		responsive: [{
			breakpoint: 1024,
			settings: {
				slidesToShow: 2
			}
			}, {
			breakpoint: 520,
			settings: {
				slidesToShow: 1,
				arrows: false
			}
		}]
	});
	
	$('.article__slider').slick({
		slidesToShow: 1,
		fade: true,
		speed: 800,
		arrows: true,
		infinite: true,
		dots: true,
		prevArrow: '<div class="article__arrow prev-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M6.04651 12L7.10465 10.95L2.87209 6.75L50 6.75L50 5.25L2.87209 5.25L7.10465 1.05L6.04651 -3.84254e-06L5.24537e-07 6L6.04651 12Z" /></svg></div>',
		nextArrow: '<div class="article__arrow next-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M43.9535 0L42.8953 1.05L47.1279 5.25H0V6.75H47.1279L42.8953 10.95L43.9535 12L50 6L43.9535 0Z" /></svg></div>',
		rows: 0,
		responsive: [{
			breakpoint: 1250,
			settings: {
				arrows: false
			}
		}]
	});
	
	$('.about-brands__slider').slick({
		slidesToShow: 5,
		speed: 5000,
		autoplay: true,
		autoplaySpeed: 0,
		cssEase: 'linear',
		arrows: false,
		infinite: true,
		dots: false,
		prevArrow: '<div class="article__arrow prev-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M6.04651 12L7.10465 10.95L2.87209 6.75L50 6.75L50 5.25L2.87209 5.25L7.10465 1.05L6.04651 -3.84254e-06L5.24537e-07 6L6.04651 12Z" /></svg></div>',
		nextArrow: '<div class="article__arrow next-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M43.9535 0L42.8953 1.05L47.1279 5.25H0V6.75H47.1279L42.8953 10.95L43.9535 12L50 6L43.9535 0Z" /></svg></div>',
		rows: 0,
		responsive: [{
			breakpoint: 1250,
			settings: {
				slidesToShow: 4
			}
			}, {
			breakpoint: 1024,
			settings: {
				slidesToShow: 3
			}
			}, {
			breakpoint: 640,
			settings: {
				slidesToShow: 2
			}
			}, {
			breakpoint: 420,
			settings: {
				slidesToShow: 1
			}
		}]
	});
	
	$('.brands-section__slider').slick({
		slidesToShow: 1,
		fade: true,
		speed: 800,
		arrows: true,
		infinite: true,
		autoplay: false,
		// autoplaySpeed: 6000,
		dots: true,
		prevArrow: '<div class="main-section__arrow prev-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M6.04651 12L7.10465 10.95L2.87209 6.75L50 6.75L50 5.25L2.87209 5.25L7.10465 1.05L6.04651 -3.84254e-06L5.24537e-07 6L6.04651 12Z" /></svg></div>',
		nextArrow: '<div class="main-section__arrow next-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M43.9535 0L42.8953 1.05L47.1279 5.25H0V6.75H47.1279L42.8953 10.95L43.9535 12L50 6L43.9535 0Z" /></svg></div>',
		rows: 0,
		responsive: [{
			breakpoint: 768,
			settings: {
				arrows: false
			}
		}]
	});
	
	$('.popular-brands__slider').slick({
		slidesToShow: 3,
		speed: 800,
		arrows: true,
		infinite: true,
		dots: true,
		prevArrow: '<div class="popular-products__arrow popular-products__prev-arrow"><svg viewBox="0 0 100 100" style="display: block; width: 100%;"><path d="M 50,50 m 0,-49.5 a 49.5,49.5 0 1 1 0,99 a 49.5,49.5 0 1 1 0,-99" stroke="#c4c4c4" stroke-width="1" fill-opacity="0" ></path></svg></div>',
		nextArrow: '<div class="popular-products__arrow popular-products__next-arrow"><svg viewBox="0 0 100 100" style="display: block; width: 100%;"><path d="M 50,50 m 0,-49.5 a 49.5,49.5 0 1 1 0,99 a 49.5,49.5 0 1 1 0,-99" stroke="#c4c4c4" stroke-width="1" fill-opacity="0" ></path></svg></div>',
		responsive: [{
			breakpoint: 1024,
			settings: {
				slidesToShow: 2
			}
			}, {
			breakpoint: 520,
			settings: {
				slidesToShow: 1,
				arrows: false
			}
		}]
	});
	
	$('.collection-products__list').slick({
		slidesToShow: 5,
		speed: 800,
		arrows: true,
		infinite: true,
		dots: false,
		prevArrow: '<div class="popular-products__arrow popular-products__prev-arrow"><svg viewBox="0 0 100 100" style="display: block; width: 100%;"><path d="M 50,50 m 0,-49.5 a 49.5,49.5 0 1 1 0,99 a 49.5,49.5 0 1 1 0,-99" stroke="#c4c4c4" stroke-width="1" fill-opacity="0" ></path></svg></div>',
		nextArrow: '<div class="popular-products__arrow popular-products__next-arrow"><svg viewBox="0 0 100 100" style="display: block; width: 100%;"><path d="M 50,50 m 0,-49.5 a 49.5,49.5 0 1 1 0,99 a 49.5,49.5 0 1 1 0,-99" stroke="#c4c4c4" stroke-width="1" fill-opacity="0" ></path></svg></div>',
		rows: 0,
		responsive: [{
			breakpoint: 1600,
			settings: {
				slidesToShow: 4
			}
			}, {
			breakpoint: 1250,
			settings: {
				slidesToShow: 3,
				arrows: false,
				dots: true
			}
			}, {
			breakpoint: 768,
			settings: {
				slidesToShow: 2,
				arrows: false,
				dots: true
			}
			}, {
			breakpoint: 520,
			settings: {
				slidesToShow: 1,
				arrows: false,
				dots: true
			}
		}]
	});
	
	$('.product-page__sm-slider').slick({
		slidesToShow: 5,
		speed: 800,
		arrows: true,
		infinite: true,
		dots: false,
		focusOnSelect: true,
		variableWidth: true,
		prevArrow: '<div class="main-brands__arrow prev-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M6.04651 12L7.10465 10.95L2.87209 6.75L50 6.75L50 5.25L2.87209 5.25L7.10465 1.05L6.04651 -3.84254e-06L5.24537e-07 6L6.04651 12Z" /></svg></div>',
		nextArrow: '<div class="main-brands__arrow next-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M43.9535 0L42.8953 1.05L47.1279 5.25H0V6.75H47.1279L42.8953 10.95L43.9535 12L50 6L43.9535 0Z" /></svg></div>',
		rows: 0,
		asNavFor: '.product-page__lg-slider',
		responsive: [{
			breakpoint: 768,
			settings: {
				arrows: false
			}
		}]
	});
	
	$('.product-page__lg-slider').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		asNavFor: '.product-page__sm-slider',
		dots: false,
		arrows: false,
		fade: true,
		rows: 0
	});
	
	$('.gift-card__slider').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		dots: true,
		arrows: true,
		prevArrow: '<div class="main-brands__arrow prev-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M6.04651 12L7.10465 10.95L2.87209 6.75L50 6.75L50 5.25L2.87209 5.25L7.10465 1.05L6.04651 -3.84254e-06L5.24537e-07 6L6.04651 12Z" /></svg></div>',
		nextArrow: '<div class="main-brands__arrow next-arrow"><svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M43.9535 0L42.8953 1.05L47.1279 5.25H0V6.75H47.1279L42.8953 10.95L43.9535 12L50 6L43.9535 0Z" /></svg></div>',
		fade: true,
		rows: 0
	});
	


	$('.main-nav__item.main-nav__item--shop').on('click', function () {
		var windowWidth = window.innerWidth;
		
		if (windowWidth >= 1024) {
			if ($('body').hasClass('main-page')) {
				$('body').removeClass('main');
			}
			if ($('body').hasClass('custome-page')) {
				$('body').removeClass('custome');
			}
			$('.shop-menu').addClass('open');
		}
	});
	$('.main-nav__item.main-nav__item--shop').on('click', function (e) {
		var windowWidth = window.innerWidth;
		
		if (windowWidth < 1024) {
			if ($('body').hasClass('main-page')) {
				$('body').removeClass('main');
			}
			if ($('body').hasClass('custome-page')) {
				$('body').removeClass('custome');
			}
			$('.shop-menu').toggleClass('open');
		}
	});
	$('.shop-menu__inner').on('mouseleave', function () {
		$('.shop-menu').removeClass('open');
		$('html').removeClass('overflow');
	});

	$('.shop-menu').on('mouseleave', function () {
		$('.shop-menu').removeClass('open');
		if ($('body').hasClass('main-page')) {
			$('body').addClass('main');
		}
		if ($('body').hasClass('custome-page')) {
			$('body').addClass('custome');
		}
		$('html').removeClass('overflow');
	});

	var headerInput = $('#header-search-input'),
	headerInputVal = headerInput.val();
	headerInput.attr('value', headerInputVal);

	headerInput.on('keyup', function () {
		var value = $(this).val();
		$(this).attr('value', value);
	});

	headerInput.on('focus', function () {
		$('.header-search .icon-close').fadeIn(150);
		$('header-search').css({
			'cursor': 'auto'
		});
		}).focusout(function () {
		$('.header-search .icon-close').fadeOut(150);
		$('header-search').css({
			'cursor': 'pointer'
		});
		// setTimeout(() => {
		//   $('.header-search__found').removeClass('active');
		// },0)
	});

	$(document).on('click touchstart', function (e) {
		if (!$(e.target).closest('.header-search-input, .header-search__found').length) {
			$('.header-search__found').removeClass('active');
		}
		e.stopPropagation();
	});

	$(document).on('click', '.header-search .icon-close', function (e) {
		var closestInput = $(this).parents('.header-search').find('input');
		closestInput.focusout();
		closestInput.val('');
		closestInput.attr('value', '');
	});

	// $('.shop-menu').on('click', function(e){
	//   var windowWidth = window.innerWidth;
	//   console.log(e.target);
	// })
	$('.shop-menu__list.first-level a').on('mouseenter', function listHover() {
		var subList = $(this).data('sub');
		
		$('.shop-menu__list.second-level').hide();
		$('.shop-menu__list.' + subList).show();
	});
	$('.shop-menu__list.second-level a').on('mouseenter', function listHover() {
		var subList = $(this).data('sub');
		
		$('.shop-menu__list.third-level').hide();
		$('.shop-menu__list.' + subList).show();
	});
	$('.shop-menu__inner').on('mouseleave', function () {
		$('.shop-menu__list.third-level, .shop-menu__list.second-level').hide();
	});
	if ($(window).width() < 1100 && $(window).width() > 768) {
		$('.shop-menu__list.first-level .has-submenu').on('click', function openList(e) {
			e.preventDefault();
			$('.shop-menu__list.second-level').hide();
			var list = $(this).data('sub');
			$('.' + list).show();
		});
		$('.shop-menu__list.second-level .has-submenu').on('click', function openList(e) {
			e.preventDefault();
			$('.shop-menu__list.third-level').hide();
			var list = $(this).data('sub');
			$('.' + list).show();
		});
		$(document).on('click touchstart', function (e) {
			if (!$(e.target).closest('.shop-menu__list, .main-nav__item--shop').length) {
				$('.shop-menu').removeClass('open');
			}
			e.stopPropagation();
		});
	}
	$('.shop-menu').on('mouseenter', function (e) {
		
		if (!$(e.target).closest('.shop-menu__list, .main-nav__item--shop').length) {
			$(this).removeClass('open');
		}
		e.stopPropagation();
	});
	if ($(window).width() < 769) {
		$('.catalog-btn').on('click', function () {
			$('.shop-menu').toggleClass('open');
			$('html').addClass('overflow');
		});
		$('.shop-menu__list .has-submenu').on('click', function openList(e) {
			e.preventDefault();
			var list = $(this).data('sub');
			$('.' + list).addClass('active');
		});
		$('.shop-menu__back a').on('click', function closeList(e) {
			e.preventDefault();
			$(this).parents('.shop-menu__list').removeClass('active');
		});
	}
	$('.inspiration-image__button').on('click', function openCloseImg() {
		$(this).toggleClass('active');
		$(this).parent('.inspiration-image').toggleClass('open');
	});

	$('input, textarea').on('change', function checkValueInputs() {
		if ($(this).val() !== '') {
			$(this).addClass('value');
			} else {
			$(this).removeClass('value');
		}
	});

	$('input[type="date"]').each(function () {
		var el = this,
		type = $(el).attr('type');
		if ($(el).val() == '') $(el).attr('type', 'text');
		$(el).focus(function () {
			setTimeout(function () {
				$(el).attr('type', type);
				el.click();
			}, 150);
		});
		$(el).blur(function () {
			if ($(el).val() == '') $(el).attr('type', 'text');
		});
	});

	$('input, textarea').each(function eachInputs() {
		if ($(this).val() !== '') {
			$(this).addClass('value');
			} else {
			$(this).removeClass('value');
		}
	});

	$('.acardion__title').on('click', function openAcordion() {
		var thisItemParent = $(this).parent();
		if (thisItemParent.hasClass('active')) {
			thisItemParent.removeClass('active');
			thisItemParent.find('.acardion__inner').css('height', '0');
			} else {
			var itemHeight = thisItemParent.find('.acardion__content').outerHeight();
			$('.acardion__item').removeClass('active');
			$('.acardion__inner').css('height', '0');
			$('.acardion-second__item').removeClass('active');
			$('.acardion-second__inner').css('height', '0');
			thisItemParent.addClass('active');
			thisItemParent.find('.acardion__inner').css('height', itemHeight);
		}
	});


	responseFunc();
	$(window).resize(responseFunc);
	$('.catalog__sidebar-button').on('click', function () {
		$('.catalog__sidebar').slideToggle('500');
	});

	$(window).on('scroll', function () {
		if ($(window).scrollTop() > 200) {
			$('.header').addClass('fixed');
			} else {
			$('.header').removeClass('fixed');
		}
	});

	$('.no-login > a').on('click', function (e) {
		e.preventDefault();
		$('.login-popup').toggleClass('open');
	});
	$('.add-wishlist.no-login').on('click', function (e) {
		e.preventDefault();
		$('.login-popup').addClass('open');
	});

	$('#open-login').on('click', function (e) {
		e.preventDefault();
		$('.login-popup').toggleClass('open');
	});

	$(document).on('click touchstart', function (e) {
		if (!$(e.target).closest('.no-login > a, .login-popup, #open-login, .add-wishlist.no-login').length) {
			$('.login-popup').removeClass('open');
		}
		e.stopPropagation();
	});

	$('.header__link--basket').on('click', function () {
		$('.basket-popup').toggleClass('open');
		$('body, html').toggleClass('holder');
	});

	$('.basket-popup__close').on('click', function () {
		$('.basket-popup').removeClass('open');
		$('body, html').removeClass('holder');
	});

	if ($(window).width() > 1250) {
		$('.menu__open').on('click', function () {
			$('.menu').toggleClass('open');
			$('body, html').toggleClass('holder-block');
		});
		} else {
		$('.menu__open').on('click', function () {
			$('.menu').toggleClass('open');
			$('body, html').toggleClass('holder-block');
		});
	}

	$(document).on('click', '.menu__close', function () {
		$('.menu').removeClass('open');
		$('body, html').removeClass('holder-block');
		$('html').removeClass('overflow');
	});

	$('.language__current').on('click', function openCloseLang() {
		$(this).toggleClass('active');
		$('.language__list').toggleClass('open');
	});

	$(document).on('click touchstart', function (e) {
		if (!$(e.target).closest('.language__current, .language__list').length) {
			$('.language__current').removeClass('active');
			$('.language__list').removeClass('open');
		}
		e.stopPropagation();
	});

	// if (window.innerWidth <= 767) {
	//   const lang = $('.language').html();
	//   $('.mobile-language').html(lang)
	// }

	if (window.innerWidth <= 1023) {
		var title = $('.product-page').find('.head-block').html();
		$('.product-page').prepend(title);
	}

	$('.slider-buttons__button.zoom').on('click', function () {
		$('.product-page__lg-slide.slick-current.slick-active').trigger('click');
	});

	if (window.innerWidth > 1024) {
		$('.tooltip-icon').on('mouseenter', function hoverTooltip() {
			$(this).find('.tooltip').addClass('open');
		});
		
		$('.tooltip-icon, .tooltip').on('mouseleave', function hoverTooltip() {
			$(this).find('.tooltip').removeClass('open');
		});
		} else {
		$('.tooltip-icon, .tooltip').on('click', function toogleTooltip() {
			$(this).find('.tooltip').toggleClass('open');
		});
		
		$(document).on('click touchstart', function (e) {
			if (!$(e.target).closest('.tooltip-icon, .tooltip').length) {
				$('.tooltip').removeClass('open');
			}
			e.stopPropagation();
		});
	}

	$('.checkout-block__link').on('click', function showInfo() {
		$('.discount-block').removeClass('active');
		if ($(this).attr('href') === '#login') {
			$('.discount-block').addClass('active');
		}
	});

	$(document).on('click touchstart', function (e) {
		if (!$(e.target).closest('.cart-add-popup').length) {
			$('.cart-add-popup').removeClass('active');
		}
		e.stopPropagation();
	});
	
	var malihuObj = {
			axis: "y",
			scrollInertia: 0,
			mouseWheelPixels: 0,
			mouseWheel: {
			enable: true
		},
		documentTouchScroll: true,
		contentTouchScroll: true
	};
	
	$('.scroll-text').mCustomScrollbar(malihuObj);
	if (window.innerWidth >= 767) {
		$('.scroll-menu').mCustomScrollbar(malihuObj);
	}
	$('.select__list').mCustomScrollbar(malihuObj);

	$('.collection-block__nav').mCustomScrollbar({
		scrollbarPosition: 'outside',
		axis: 'x'
	});

	$('.option-values').mCustomScrollbar(malihuObj);

	$('.select').on('click', function (e) {
		$('.select').not(this).removeClass('select--open');
	});


	$(window).on('load', function () {
		if ($('#preloader').length) {
			setTimeout(function () {
				$('#preloader').addClass('hide');
			}, 2000);
		}
		
		if ($('body').hasClass('main-page main')) {
			var time = $('.modal-banner').data('time') * 1000;
			setTimeout(function () {
				$('.modal-banner').fadeIn();
			}, time);
		}
	});
	$(document).on('click', '.modal-banner', function (event) {
		var target = $(event.target);
		
		if (!target.closest('.banner-content__image').length || target.closest('.close-banner').length) {
			$(this).fadeOut();
		}
	});
		
	
	
});






