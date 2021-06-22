/* -----------------------------------------------------------------------------



File:           JS Core
Version:        1.0
Last change:    00/00/00 
-------------------------------------------------------------------------------- */
(function() {

	"use strict";

	var Wizard = {
		init: function() {
			this.Basic.init();  
		},

		Basic: {
			init: function() {

				this.preloader();
				this.countDown();
				this.timer();
				
			},
			preloader: function (){
				jQuery(window).on('load', function(){
					jQuery('#loading').fadeOut('slow',function(){
						jQuery(this).remove();
					});
				});
			},
			countDown:  function (){
				if ($('.quiz-countdown').length > 0) {
					var deadlineDate = new Date('june 28, 2021 06:59:59').getTime();
					var countdownDays = document.querySelector('.days .count-down-number');
					var countdownHours = document.querySelector('.hours .count-down-number');
					var countdownMinutes = document.querySelector('.minutes .count-down-number');
					var countdownSeconds = document.querySelector('.seconds .count-down-number');
					setInterval(function () {
						var currentDate = new Date().getTime();
						var distance = deadlineDate - currentDate;
						var days = Math.floor(distance / (1000 * 60 * 60 * 24));
						var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
						var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
						var seconds = Math.floor((distance % (1000 * 60)) / 1000);
						countdownDays.innerHTML = days;
						countdownHours.innerHTML = hours;
						countdownMinutes.innerHTML = minutes;
						countdownSeconds.innerHTML = seconds;
					}, 1000);

				};
			},
			timer: function () {
				if ($('.timer-page').length > 0) {
					var deadlineDate = new Date('june 28, 2021 10:30:00').getTime();
					// var countdownDays = document.querySelector('.timer-page .days');
					var countdownHours = document.querySelector('.timer-page .hours');
					var countdownMinutes = document.querySelector('.timer-page .minutes');
					var countdownSeconds = document.querySelector('.timer-page .seconds');
					setInterval(function () {
						var currentDate = new Date().getTime();
						var distance = deadlineDate - currentDate;
						// var days = Math.floor(distance / (1000 * 60 * 60 * 24));
						var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
						var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
						var seconds = Math.floor((distance % (1000 * 60)) / 1000);
						// countdownDays.innerHTML = days;
						countdownHours.innerHTML = hours+'Hrs';
						countdownMinutes.innerHTML = minutes+'Min';
						countdownSeconds.innerHTML = seconds+'Sec';
					}, 1000);
				};
			}
		}
	}
	jQuery(document).ready(function (){
		Wizard.init();
	});

})();

