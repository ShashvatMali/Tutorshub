(function ($) {

	'use strict';

	const $body = $('body');
	const $cart = $('.thegem-te-cart');
	const $cartWidget = $('.thegem-te-cart').parents('.elementor-widget');

	const _helpers = {
		getScrollY : () => {
			return window.pageYOffset || document.documentElement.scrollTop;
		},
		setBodyLocked: (el) => {
			$body.data('scroll-position', _helpers.getScrollY());
			$body.addClass('cart-scroll-locked');

			let isNoStickyItem = $(el).closest("#site-header").length;
			if (isNoStickyItem){
				$body.addClass('is-no-sticky');
			} else {
				$body.removeClass('is-no-sticky');
			}
		},
		unsetBodyLocked: () => {
			setTimeout(function (){
				$body.removeClass('cart-scroll-locked is-no-sticky');
			}, 1000);

			if ($body.data('scroll-position')) {
				window.scrollTo(0, $body.data('scroll-position'))
			}
		},
		isMobileCart: () => {
			let result = false;
			if ($cart.hasClass('mobile-view')) {
				result = true;
			}

			return result;
		},
		setTranslateValues: (elem) => {
			if (!elem.length) return;
			elem.css('transform', '').css('margin-left', '').css('margin-top', '');
			const style = window.getComputedStyle(elem[0]);
			const matrix = style['transform'] || style.webkitTransform || style.mozTransform;
			let x = 0, y = 0;

			// No transform property. Simply return 0 values.
			if (matrix === 'none' || typeof matrix === 'undefined') {
				return;
			}

			// Can either be 2d or 3d transform
			const matrixType = matrix.includes('3d') ? '3d' : '2d';
			const matrixValues = matrix.match(/matrix.*\((.+)\)/)[1].split(', ');

			// 2d matrices have 6 values
			// Last 2 values are X and Y.
			// 2d matrices does not have Z value.
			if (matrixType === '2d') {
				x = matrixValues[4];
				y = matrixValues[5];
			}

			// 3d matrices have 16 values
			// The 13th, 14th, and 15th values are X, Y, and Z
			if (matrixType === '3d') {
				x = matrixValues[12];
				y = matrixValues[13];
			}

			elem.css('transform', 'none').css('margin-left', x + 'px').css('margin-top', y + 'px');
		}
	};

	const cartScripts = {
		init: () => {
			cartScripts.setLayoutView();
			cartScripts.invert();
			cartScripts.mobileCart();
			cartScripts.onResize();

			_helpers.setTranslateValues($cartWidget);

			$(window).on('resize', function (e) {
				setTimeout(function () {
					_helpers.setTranslateValues($cartWidget);
				}, 250);
			});
		},

		setLayoutView: () => {
			let viewportWidth = $(window).width();

			$cart.each(function (i, el) {
				if (viewportWidth < 992) {
					$(this).removeClass('desktop-view').addClass('mobile-view');
				} else {
					$(this).removeClass('mobile-view').addClass('desktop-view');
				}
			});
		},

		invert: () => {
			let $items = $('.minicart', $cart);
			$items.removeClass('invert');

			$items.each(function(i, el) {
				let clientRect = el.getBoundingClientRect();
				let itemWidth = $(el).width();
				let itemLeftPosition = clientRect.x + pageXOffset;

				if ($(window).width() - itemLeftPosition > itemWidth) {
					$(el).removeClass('invert');
				} else {
					$(el).addClass('invert');
				}
			});
		},

		mobileCart: () => {
			window.isMobileCart = _helpers.isMobileCart();

			$cart.each(function (i, el) {
				let $item = $(el);
				let $minicart = $('.minicart', $item);
				let $overlay = $('.mobile-minicart-overlay', $item);

				$item.off('click touchend', '.menu-item-cart > a')
				if (!window.isMobileCart) return;

				$item.on('click touchend', '.menu-item-cart > a', function(e) {
					e.preventDefault();

					_helpers.setBodyLocked(this);
					$overlay.addClass('active');
					$minicart.addClass('active');
				});

				$(document).on('click touchend', '.mobile-cart-header-close, .mobile-minicart-overlay, .thegem-popup-notification-sidebar .close', function(e) {
					e.preventDefault();

					_helpers.unsetBodyLocked();
					$overlay.removeClass('active');
					$minicart.removeClass('active');
				});

				$(document).on('click touchend', '.thegem-popup-notification-sidebar', function(e) {
					const wrap = $('.thegem-popup-notification-sidebar .notification-sidebar');
					if (!wrap.is(e.target) && wrap.has(e.target).length === 0) {
						_helpers.unsetBodyLocked();
						$overlay.removeClass('active');
						$minicart.removeClass('active');
					}
				});

			});
		},

		onResize: () => {
			let resizeTimer;
			$(window).on('resize', function(e) {
				clearTimeout(resizeTimer);

				resizeTimer = setTimeout(function() {
					cartScripts.setLayoutView();
					cartScripts.invert();
					cartScripts.mobileCart();
				}, 250);
			});
		},
	}

	$.fn.initCartScripts = function () {
		cartScripts.init();
	};

	// Run the function
	$(function () {
		if (!$('body').hasClass('elementor-editor-active')) {
			$().initCartScripts();
		}
	});
})(jQuery);
