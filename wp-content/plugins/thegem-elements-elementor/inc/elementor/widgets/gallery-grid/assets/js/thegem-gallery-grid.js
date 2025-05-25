(function ($) {
	$(function () {

		function gallery_images_loaded($box, image_selector, callback) {
			function check_image_loaded(img) {
				return img.getAttribute('loading') === 'lazy' || img.getAttribute('src') === '' || (img.complete && img.naturalWidth !== undefined && img.naturalWidth !== 0);
			}

			var $images = $(image_selector, $box).filter(function () {
					return !check_image_loaded(this);
				}),
				images_count = $images.length;

			if (images_count == 0) {
				return callback();
			}

			if (window.gemBrowser.name == 'ie' && !isNaN(parseInt(window.gemBrowser.version)) && parseInt(window.gemBrowser.version) <= 10) {
				function image_load_event() {
					images_count--;
					if (images_count == 0) {
						callback();
					}
				}

				$images.each(function () {
					if (check_image_loaded(this)) {
						return;
					}

					var proxyImage = new Image();
					proxyImage.addEventListener('load', image_load_event);
					proxyImage.addEventListener('error', image_load_event);
					proxyImage.src = this.src;
				});
				return;
			}

			$images.on('load error', function () {
				images_count--;
				if (images_count == 0) {
					callback();
				}
			});
		}

		function init_circular_overlay($gallery, $set) {
			if (!$gallery.hasClass('hover-circular')) {
				return;
			}

			$('.gallery-item', $set).on('mouseenter', function () {
				var overlayWidth = $('.overlay', this).width(),
					overlayHeight = $('.overlay', this).height(),
					$overlayCircle = $('.overlay-circle', this),
					maxSize = 0;

				if (overlayWidth > overlayHeight) {
					maxSize = overlayWidth;
					$overlayCircle.height(overlayWidth)
				} else {
					maxSize = overlayHeight;
					$overlayCircle.width(overlayHeight);
				}
				maxSize += overlayWidth * 0.3;

				$overlayCircle.css({
					marginLeft: -maxSize / 2,
					marginTop: -maxSize / 2
				});
			});
		}

		function initGalleryGrid() {
			var layoutMode = 'masonry-custom';
			if ($(this).hasClass('metro')) {
				layoutMode = 'metro'
			}

			if (window.tgpLazyItems !== undefined) {
				var isShowed = window.tgpLazyItems.checkGroupShowed(this, function (node) {
					initGalleryGrid.call(node);
				});
				if (!isShowed) {
					return;
				}
			}

			var $gallery = $(this);
			var $set = $('.gallery-set', this);

			gallery_images_loaded($set, '.image-wrap img', function () {
				$gallery.closest('.gallery-preloader-wrapper').prev('.preloader').remove();

				init_circular_overlay($gallery, $set);

				if ($gallery.hasClass('loading-animation')) {
					var itemsAnimations = $gallery.itemsAnimations({
						itemSelector: '.gallery-item',
						scrollMonitor: true
					});
				}

				var init_gallery = true;

				if (!$gallery.hasClass('disable-isotope')) {
					let size_container = $('.portfolio-item-size-container .gallery-item', $gallery);
					let active_filter = $gallery.data('filter');
					let filter = '';
					if (active_filter.length) {
						active_filter.forEach(function (item, index) {
							if (index != 0) {
								filter += ', ';
							}
							filter += '.' + item;
						});
					} else {
						filter = '*';
					}
					$set
						.on('arrangeComplete', function (event, filteredItems) {
							if (init_gallery) {
								init_gallery = false;

								var items = [];
								filteredItems.forEach(function (item) {
									items.push(item.element);
								});

								if ($gallery.hasClass('loading-animation')) {
									itemsAnimations.show($(items));
								}
							}
						}).on('layoutComplete', function (event, laidOutItems) {
							let items = [];
							laidOutItems.forEach(function (item) {
								items.push(item.element);
							});

							if (!init_gallery && $gallery.hasClass('loading-animation')) {
								itemsAnimations.reinitItems($(items));
								itemsAnimations.show($(items));
							}
						})
						.thegem_isotope({
							itemSelector: '.gallery-item',
							itemImageWrapperSelector: '.image-wrap',
							fixHeightDoubleItems: $gallery.hasClass('gallery-style-justified'),
							layoutMode: layoutMode,
							'masonry-custom': {
								columnWidth: (size_container.length > 0) ? size_container[0] : '.gallery-item:not(.double-item)'
							},
							filter: filter
						});
				} else {
					if ($gallery.hasClass('loading-animation')) {
						$gallery.itemsAnimations('instance').show($('.gallery-item', $gallery));
					}
				}
			});

			if ($('.portfolio-filters', $gallery).length) {
				$('.portfolio-filters, .portfolio-filters-resp ul li', $gallery).on('click', 'a', function (e) {
					let thisFilter = $(this).data('filter');
					let filtersPanel = $(this).parents('.portfolio-top-panel-left');
					let isMultiple = filtersPanel.hasClass('multiple');

					if (thisFilter) {
						e.preventDefault();
					} else {
						return;
					}

					let filtersArr = $gallery.data('gallery-filter') ? $gallery.data('gallery-filter') : [];

					if (thisFilter === '*') {
						filtersArr = [];
						filtersPanel.find('a').removeClass('active');
						$(this).addClass('active');
					} else if ($(this).hasClass('active')) {
						$(this).removeClass('active');
						if (filtersArr.includes(thisFilter)) {
							const index = filtersArr.indexOf(thisFilter);
							if (index > -1) {
								filtersArr.splice(index, 1);
							}
							if (filtersArr.length === 0) {
								filtersPanel.find('a.all').addClass('active');
							}
						}
					} else {
						if (!isMultiple) {
							filtersPanel.find('a').removeClass('active');
							filtersArr = [];
						} else {
							filtersPanel.find('a.all').removeClass('active');
						}
						$(this).addClass('active');
						filtersArr.push(thisFilter);
					}

					$gallery.data('gallery-filter', filtersArr);

					filterGallery($gallery, filtersArr);

					if ($('.portfolio-filters-resp', $gallery).length > 0 && typeof $.fn.dlmenu === 'function') {
						$('.portfolio-filters-resp', $gallery).dlmenu('closeMenu');
					}

					return false;
				});

				if (typeof $.fn.dlmenu === 'function') {
					$('.portfolio-filters-resp', $gallery).dlmenu({
						animationClasses: {
							classin: 'dl-animate-in',
							classout: 'dl-animate-out'
						}
					});
				}

				initFiltersMore($gallery);
			}

			if ($set.closest('.gem_tab').size() > 0) {
				$set.closest('.gem_tab').bind('tab-update', function () {
					if (!$gallery.hasClass('disable-isotope')) {
						$set.thegem_isotope('layout');
					}
				});
			}
			$(document).on('show.vc.tab', '[data-vc-tabs]', function () {
				var $tab = $(this).data('vc.tabs').getTarget();
				if ($tab.find($set).length && !$gallery.hasClass('disable-isotope')) {
					$set.thegem_isotope('layout');
				}
			});
			$(window).on('elementor/nested-tabs/activate', function (e, tab) {
				var $tab = $(tab);
				if ($tab.find($set).length && !$gallery.hasClass('disable-isotope')) {
					$set.thegem_isotope('layout');
				}
			});
		}

		function filterGallery($gallery, filtersArr) {

			let uid = $gallery.data('uid'),
				queryParams = new URLSearchParams(window.location.search),
				delArr = [];
			for (let p of queryParams) {
				if (p[0].includes(uid)) {
					delArr.push(p[0]);
				}
			}
			for (let del of delArr) {
				queryParams.delete(del);
			}

			if ($gallery.hasClass('loading-animation') && $gallery.itemsAnimations('instance').getAnimationName() != 'disabled') {
				$('.gallery-item', $gallery).addClass('item-animations-not-inited');
			} else {
				$('.gallery-item', $gallery).removeClass('item-animations-not-inited');
			}

			if (!$gallery.hasClass('disable-isotope')) {

				let $set = $('.gallery-set', $gallery);

				if (filtersArr.length) {
					queryParams.set('grid_' + uid + '-filter', filtersArr);
					let filter = '';
					filtersArr.forEach(function (item, index) {
						if (index != 0) {
							filter += ', ';
						}
						filter += '.' + item;
					});
					$set.thegem_isotope({filter: filter});
				} else {
					$set.thegem_isotope({filter: '*'});
				}
			} else {

				if (filtersArr.length === 0) {
					$gallery.find('.gallery-item').show();
				} else {
					queryParams.set('grid_' + uid + '-filter', filtersArr);

					$gallery.find('.gallery-item:not(.size-item)').hide();

					filtersArr.forEach(function (item, index) {
						$gallery.find('.gallery-item.' + item).show();
					});
				}

				if ($gallery.hasClass('loading-animation')) {
					if (filtersArr.length === 0) {
						$gallery.itemsAnimations('instance').reinitItems($('.gallery-item', $gallery));
						$gallery.itemsAnimations('instance').show($('.gallery-item', $gallery));
					} else {
						filtersArr.forEach(function (item, index) {
							$gallery.itemsAnimations('instance').reinitItems($('.gallery-item.' + item, $gallery));
							$gallery.itemsAnimations('instance').show($('.gallery-item.' + item, $gallery));
						});
					}
				}
			}

			if (queryParams.toString().length > 0) {
				history.replaceState(null, null, "?" + queryParams.toString());
			} else {
				history.replaceState(null, null, location.href.split("?")[0]);
			}
		}

		function initFiltersMore($gallery) {
			if (!$('.portfolio-filters-more', $gallery).length)
				return false;

			$('.portfolio-filters-more', $gallery).on('mouseover', function () {
				$(this).addClass('active');
			}).on('mouseout', function () {
				$(this).removeClass('active');
			});

			$('.portfolio-filters-more a', $gallery).on('click', function (e) {
				$('.portfolio-filters-more', $gallery).mouseout();
			});
		}

		if (typeof $.fn.scSticky === 'function') {
			$('.filters-top-sticky').scSticky({hideStickyHeader: true, fullWidth: true});
		}

		$.fn.initGalleriesGrid = function () {
			$(this).each(initGalleryGrid);
		};

		$(document).ready(function() {
			$('body:not(.elementor-editor-active) .gem-gallery-grid').initGalleriesGrid();
		});

		setTimeout(function () {
			if ($('body:not(.elementor-editor-active) .preloader + .gallery-preloader-wrapper').length) {
				$('.gem-gallery-grid').initGalleriesGrid();
			}
		}, 2000);

		$('.gem-gallery-grid').on('click', '.gallery-item', function () {
			$(this).mouseover();
		});
	});
})(jQuery);