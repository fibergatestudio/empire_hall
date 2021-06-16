function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

// live search
$('#header-search-input').on('input', function() {
    var _this_ = $(this);
    var input_value = $(this).val();

    $('.search__list').remove();

    if (input_value.length <= 2) {
        return false;
    }

    var search_url = 'index.php?route=product/search/liveSearch&search=' + encodeURIComponent(input_value);

    $.get(search_url, function(response) {
        if (response.length !== 0) {
            var html = '';

            $.each(response, function(i, item) {


                html += '<a href="' + item.href + '" class="found-content__card">';
                html += '<div class="img">';
                html += '<img src="' + item.image + '" alt="' + item.href + '">';
                html += '</div>';
                html += '<div class="text">' + item.name + '</div>';
                html += '</a>';

            });

$('.res_serch').attr('href','index.php?route=product/search&search='+input_value);
            $('.header-search__found').addClass('active');
            $('.header-search__found .found-content .mCSB_container').html(html); 
            $('.search__list').mCustomScrollbar();
      
            $('.found-content').mCustomScrollbar('update');
        }
    });
});

$(document).on('click', '.incart_dec', function() {
	var id = $(this).attr('data-prod-id');
	var q = +$(this).attr('data-quant') - 1;
	cart.updatesmcart(id,q);
	setTimeout(function() {
		$('.scroll-text').mCustomScrollbar();
	}, 300);
});

$(document).on('click', '.incart_inc', function() {
	var id = $(this).attr('data-prod-id');
	var q = +$(this).attr('data-quant') + 1;
	cart.updatesmcart(id,q);
	setTimeout(function() {
		$('.scroll-text').mCustomScrollbar();
	}, 300);
});
$(document).on('click','.loginza',function () {
	setTimeout(function () {
		$('.login-popup').addClass('open')
	},500)

})

$(document).ready(function() {
    setTimeout(function () {
        $('.main-section__slide .main-section__content').removeAttr('style');
        $('.brands-section__slide .brands-section__content').removeAttr('style');
    }, 300);

	// Popups
	$('#open-callback').on('click', function(event){
		event.preventDefault();
		$('.popup-callback').addClass('active');
	});

	// Newsletter subscribe
	$(document).on('click', '#subscribe-send', function(event){
		event.preventDefault();

		var data = $('input[name=\'subscribe_email\']');
		$('.error.newsleter').remove();
		$.ajax({
			url: 'index.php?route=common/forms/newsletter',
			type: 'post',
			data: data,
			dataType: 'json',
			beforeSend: function(){
				$('#subscribe-form input').removeClass('error');
			},
			success: function(json) {
				if (json['error']) {
					$('input[name=\'subscribe_email\']').addClass('error');
					$('input[name=\'subscribe_email\']').after('<p class="error newsleter">'+json['error']['subscribe_email']+'</p>')

					setTimeout(function () {
						$('.error.newsleter').remove();
						$('input[name=\'subscribe_email\']').removeClass('error');
					},4000)
				}
				if (json['success']) {
					$('#subscribe-form input').val('');
					$('.popup-subscribe').addClass('active');
				}
			},
			error:function (xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});

	});

	// Callback
	$(document).on('click', '#callback-send', function(event){
		event.preventDefault();

		var data = $('input[name=\'callback_name\'], input[name=\'callback_phone\']');

		$.ajax({
			url: 'index.php?route=common/forms/callback',
			type: 'post',
			data: data,
			dataType: 'json',
			beforeSend: function() {
				$('#callback-form input').removeClass('error');
				$('p.error').remove();
			},
			success: function(json) {
				if (json['error']) {
					$.each(json['error'], function(key, value) {
						$("input[name='" + key + "']").addClass('error');
						$("input[name='" + key + "']").parent().append('<p class="error">' + value + '</p>');
					});
				}
				if (json['success']) {
					$('#callback-form input').val('');
					$('.popup-callback').removeClass('active');
					$('.popup-success').addClass('active');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	// Request a price
	$(document).on('click', '#product-send', function(event){
		event.preventDefault();

		var data = $('input[name=\'product_name\'], input[name=\'product_phone\'], input[name=\'product_href\']');

		$.ajax({
			url: 'index.php?route=common/forms/product',
			type: 'post',
			data: data,
			dataType: 'json',
			beforeSend: function() {
				$('#product-form input').removeClass('error');
				$('p.error').remove();
			},
			success: function(json) {
				if (json['error']) {
					$.each(json['error'], function(key, value) {
						$("input[name='" + key + "']").addClass('error');
						$("input[name='" + key + "']").parent().append('<p class="error">' + value + '</p>');
					});
				}
				if (json['success']) {
					$('#product-form input').val('');
					$('.popup-product').removeClass('active');
					$('.popup-success').addClass('active');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	// Login
	$(document).on('click', '#login-send', function(event) {
		event.preventDefault();

		var data = $('#login-form input[name=\'email\'], #login-form input[name=\'password\']');

		$.ajax({
			url: 'index.php?route=common/forms/login',
			type: 'post',
			data: data,
			dataType: 'json',
			beforeSend: function() {
				$('#login-form input').removeClass('error');
			},
			success: function(json) {
				if (json['error']) {
					$.each(json['error'], function(key, value) {
						$("input[name='" + key + "']").addClass('error');
					});

				}

				if (json['success']) {
					$('#login-form input').val('');
					window.location.href = json['to_account'];
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});

	});


	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

	// Currency
	$('#form-currency .currency-select').on('click', function(e) {
		e.preventDefault();

		$('#form-currency input[name=\'code\']').val($(this).attr('id'));

		$('#form-currency').submit();
	});

	// Language
	$('#form-language .language-select').on('click', function(e) {
		e.preventDefault();

		$('#form-language input[name=\'code\']').val($(this).attr('id'));

		$('#form-language').submit();
	});

	/* Search */
	$('#search input[name=\'search\']').parent().find('button').on('click', function() {
		var url = $('base').attr('href') + 'index.php?route=product/search';

		var value = $('header #search input[name=\'search\']').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}

		location = url;
	});

	$('#search input[name=\'search\']').on('keydown', function(e) {

		if (e.keyCode == 13) {
			//console.log(e.keyCode);
		//	$('#search input[name=\'search\']').parent().find('button').trigger('click');
			var url = $('base').attr('href') + 'index.php?route=product/search';

			var value = $('header #search input[name=\'search\']').val();

			if (value) {
				url += '&search=' + encodeURIComponent(value);
			}

			location = url;

		}
	});

	// Menu
	$('#menu .dropdown-menu').each(function() {
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();

		var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 10) + 'px');
		}
	});

	$('.menu__open').unbind('mouseenter mouseleave');
	$('.menu__open').click(function() {
		$('.menu').addClass('open');
	});
	$('.main-nav__item--shop').unbind('mouseenter mouseleave');
	$('.main-nav__item--shop').click(function(e) {
		e.preventDefault();
		$('.shop-menu').addClass('open');
	});

	$('.shop-menu__list.first-level').on('mouseover', function() {
		$('.shop-menu__list.third-level').hide();
	});

	// Product List
	$('#list-view').click(function() {
		$('#content .product-grid > .clearfix').remove();

		$('#content .row > .product-grid').attr('class', 'product-layout product-list col-xs-12');
		$('#grid-view').removeClass('active');
		$('#list-view').addClass('active');

		localStorage.setItem('display', 'list');
	});

	// Product Grid
	$('#grid-view').click(function() {
		// What a shame bootstrap does not take into account dynamically loaded columns
		var cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}

		$('#list-view').removeClass('active');
		$('#grid-view').addClass('active');

		localStorage.setItem('display', 'grid');
	});

	if (localStorage.getItem('display') == 'list') {
		$('#list-view').trigger('click');
		$('#list-view').addClass('active');
	} else {
		$('#grid-view').trigger('click');
		$('#grid-view').addClass('active');
	}

	// Checkout
	$(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
		if (e.keyCode == 13) {
			$('#collapse-checkout-option #button-login').trigger('click');
		}
	});

    if($('.main-section__slider').length) {
        window.mainSliderInitInterval = setInterval(function () {
            if ($('.main-section__slider.slick-initialized').length){
                clearInterval(window.mainSliderInitInterval);
                $('.main-section__slider').slick('slickSetOption', {
                    autoplay: false,
                   // autoplaySpeed: 6000
                });
                //$('.main-section__slider').slick('slickPlay');
            }
        }, 100);
    }

    // price

    function numberWithSpaces(x) {
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }

    $('.product-page__new-price').each(function() {
        var str = $(this).text();
        str = str.split(' ')[0];
        if (str.length >= 4) {
            $(this).html(numberWithSpaces(str) + ' UAH');
        }
    });

    $('.product-page__old-price').each(function() {
        var str1 = $(this).text();
        str1 = str1.split(' ')[0];
        if (str1.length >= 4) {
            $(this).html(numberWithSpaces(str1) + ' UAH');
        }
    });

    $('.product__price-new').each(function() {
        var str2 = $(this).text();
        str2 = str2.split(' ')[0];
        if (str2.length >= 4) {
            $(this).html(numberWithSpaces(str2) + ' UAH');
        }
    });

    $('.product__price-old').each(function() {
        var str3 = $(this).text();
        str3 = str3.split(' ')[0];
        if (str3.length >= 4) {
            $(this).html(numberWithSpaces(str3) + ' UAH');
        }
    });

    $('.basket-block__price').each(function() {
        var str4 = $(this).text();
        str4 = str4.split(' ')[0];
        if (str4.length >= 4) {
            $(this).html(numberWithSpaces(str4) + ' UAH');
        }
    });

    $('.basket-popup__total-price').each(function() {
        var str5 = $(this).text();
        str5 = str5.split(' ')[0];
        if (str5.length >= 4) {
            $(this).html(numberWithSpaces(str5) + ' UAH');
        }
    });

    $('.basket-block__total span').each(function() {
        var str6 = $(this).text();
        str6 = str6.split(' ')[0];
        if (str6.length >= 4) {
            $(this).html(numberWithSpaces(str6) + ' UAH');
        }
    });

	$('input[type="tel"]').mask('+38 (999) 999-99-99');

    setTimeout(function () {
        $('.left-nav__list').mCustomScrollbar('destroy');
        $('.right-nav__list').mCustomScrollbar('destroy');
        $('.left-nav').addClass('no-bg');
    }, 1000);
    if($(window).width > 991) {
        $('.menu__open').click(function() {
			setTimeout(function () {
				$('.left-nav__list').mCustomScrollbar('destroy');
				$('.right-nav__list').mCustomScrollbar('destroy');
                $('.left-nav').addClass('no-bg');
			}, 1000);
        });
	}


    /*$(document).delegate('.basket-block__delete', 'click', function(e) {
        e.preventDefault();
        setTimeout(function () {
            $('.scroll-text').mCustomScrollbar();
        }, 1500);
    });*/
});

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			// beforeSend: function() {
			// 	$('#cart > button').button('loading');
			// },
			// complete: function() {
			// 	$('#cart > button').button('reset');
			// },
			success: function(json) {
				// $('.alert-dismissible, .text-danger').remove();
				//
				// if (json['redirect']) {
				// 	location = json['redirect'];
				// }
				//
				// if (json['success']) {
				// 	$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				//
				// 	// Need to set timeout otherwise it wont update the total
				// 	setTimeout(function () {
				// 		$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				// 	}, 100);
				//
				// 	$('html, body').animate({ scrollTop: 0 }, 'slow');
				//
				// 	$('#cart > ul').load('index.php?route=common/cart/info ul li');
				// }
				if (json['success']) {
					$('#cart-total').html(json['total']);
					$('.cart-add-popup').addClass('active');
					$('#cart').load('index.php?route=common/cart/info');
                    setTimeout(function () {
                        $('.scroll-text').mCustomScrollbar();
                    }, 300);
                    setTimeout(function() {
                        $('.cart-add-popup.active').removeClass('active');
                    }, 3000);
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}

                setTimeout(function () {
                    $('.scroll-text').mCustomScrollbar();
                }, 300);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			success: function(json) {
				// Need to set timeout otherwise it wont update the total

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/step2checkout') {
					location = 'index.php?route=checkout/step2checkout';
				} else {
					setTimeout(function() {
						$('#cart-total').html(json['total']);
						$('#cart .basket-popup__content').load('index.php?route=common/cart/info .basket-popup .basket-popup__content .basket-block__list');
						$('.basket-popup__top-text').html(json['header_items']);
						$('.basket-popup__total-price').html(json['total_price']);
                        setTimeout(function () {
                            $('.scroll-text').mCustomScrollbar();
                        }, 300);
					},100);

                    setTimeout(function () {
                        $('.scroll-text').mCustomScrollbar();
                    }, 300);
				}
				if (json['total'] == 0) {
					window.location.reload();
				}

                setTimeout(function () {
                    $('.scroll-text').mCustomScrollbar();
                }, 300);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'updatesmcart': function(key, quantity) {
		$.ajaxSetup ({cache: false});
		$.ajax({
			url: 'index.php?route=checkout/cart/editsmcart',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				var timer = 100;
				if (json.error) {
					$('.amount-input-block').addClass('error');
					timer = 1000;
				}
				setTimeout(function() {
					$('#cart-total').html(json['total']);
					setTimeout(function() {
						$('#cart .basket-popup__content').load('index.php?route=common/cart/info .basket-popup .basket-popup__content .basket-block__list');
						$('.basket-popup__top-text').html(json['header_items']);
						$('.basket-popup__total-price').html(json['total_price']);

                        setTimeout(function () {
                            $('.scroll-text').mCustomScrollbar();
                        }, 300);
					},100);
				}, timer);

                setTimeout(function () {
                    $('.scroll-text').mCustomScrollbar();
                }, 300);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {

				if (json['not_logged']) {
					$('.popup--login').addClass('active');
				}

				if (json['success']) {
					$('.add-wishlist[data-id="'+product_id+'"]').addClass('active');
					$('.add-wishlist[data-id="'+product_id+'"]').attr('onclick', 'wishlist.remove('+product_id+')');

					$('.wishlist-add-popup').addClass('active');
                    $('.wishlist-add-popup').show('slow');
                    $('.header__links .header__link--wishlist .count').text(json['total'])


setTimeout(function () {
	$('.wishlist-add-popup').hide('slow');
	$('.wishlist-add-popup').removeClass('active');
},3000)


				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/remove',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				if (json['success']) {
					$('.add-wishlist[data-id="'+product_id+'"]').removeClass('active');
					$('.add-wishlist[data-id="'+product_id+'"]').attr('onclick', 'wishlist.add('+product_id+')');
					$('.header__links .header__link--wishlist .count').text(json['total'])
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'removeOnPage': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/remove',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				if (json['success']) {
					$('.product__item[data-id="'+product_id+'"]').remove();
					$('.header__links .header__link--wishlist .count').text(json['total'])
				}

			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=product/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible').remove();

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					$('#compare-total').html(json['total']);

					$('html, body').animate({ scrollTop: 0 }, 'slow');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();

	$('#modal-agree').remove();

	var element = this;

	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			$('body').append(html);

			$('#modal-agree').modal('show');
		}
	});
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $(this).position();

				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});

				$(this).siblings('ul.dropdown-menu').show();
			}

			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				html = '';

				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}

					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}

					// Get all the ones with a categories
					var category = new Array();

					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}

							category[json[i]['category']]['item'].push(json[i]);
						}
					}

					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$(this).siblings('ul.dropdown-menu').html(html);
			}

			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

		});
	}
    $('.ocf-option-values a').click(function(e){
        e.preventDefault();
        $(this).parent().click();
    });

    /*setTimeout(function () {

        s = document.createElement('script');
        s.src = '//code.jivosite.com/widget/G1NV320pyY';
        s.async = true;
        s.onload = function () {
            document.dispatchEvent(new CustomEvent('scroll'))
        }
        document.head.appendChild(s);

    }, 5000)*/
})(window.jQuery);
