"use strict";

jQuery(window).on('elementor/frontend/init', function () {

	elementorFrontend.hooks.addAction('frontend/element_ready/thegem-template-countdown.default', function ($scope, $) {

		function countdown_init_time(timestamp) {

			var now = new Date();
			var currentTime = now.getTime();
			var eventTime = timestamp * 1000;

			if ((eventTime - currentTime) < 0) {
				eventTime = currentTime;
			}

			var remTime = eventTime - currentTime;
			var s = Math.floor(remTime / 1000);
			var m = Math.floor(s / 60);
			var h = Math.floor(m / 60);
			var d = Math.floor(h / 24);

			h %= 24;
			m %= 60;
			s %= 60;

			h = h + 100;
			m = m + 100;
			s = s + 100;

			return [d, h, m, s];

		}

		function countdown(elem, odometerDays, odometerHours, odometerMinutes, odometerSeconds, $rCanvas) {

			var elEventDate = elem.attr('data-eventdate'),
				elD = countdown_init_time(elEventDate)[0],
				elH = countdown_init_time(elEventDate)[1],
				elM = countdown_init_time(elEventDate)[2],
				elS = countdown_init_time(elEventDate)[3];

			if ($('.countdown-days', elem) && odometerDays) {
				$('.countdown-days', elem).text(elD);
				odometerDays.value = elD;
			}
			if ($('.countdown-hours', elem) && odometerHours) {
				$('.countdown-hours', elem).text(elH);
				odometerHours.value = elH;
			}
			if ($('.countdown-minutes', elem) && odometerMinutes) {
				$('.countdown-minutes', elem).text(elM);
				odometerMinutes.value = elM;
			}
			if ($('.countdown-seconds', elem) && odometerSeconds) {
				$('.countdown-seconds', elem).text(elS);
				odometerSeconds.value = elS;
			}

			setTimeout(function () {
				countdown(elem, odometerDays, odometerHours, odometerMinutes, odometerSeconds, $rCanvas);
			}, 1000);
		}

		$scope.find('.thegem-te-countdown').each(function () {

			if ($(this).data('initialized')) {
				return;
			}
			$(this).data('initialized', true);

			var odometerDays = null,
				odometerHours = null,
				odometerMinutes = null,
				odometerSeconds = null, $el;
			var $glEvent = $(this);

			$el = $('.countdown-days', $glEvent).get(0);
			if ($el) {
				odometerDays = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
			}
			$el = $('.countdown-hours', $glEvent).get(0);
			if ($el) odometerHours = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
			$el = $('.countdown-minutes', $glEvent).get(0);
			if ($el) odometerMinutes = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
			$el = $('.countdown-seconds', $glEvent).get(0);
			if ($el) odometerSeconds = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
			countdown($glEvent, odometerDays, odometerHours, odometerMinutes, odometerSeconds, '');
		});
	});
});