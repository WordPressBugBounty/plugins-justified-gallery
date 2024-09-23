(function ($) {
	var SETTINGS_TOGGLE = {
		selectSel: 'dgwt-jg-options-toggle select',
		groupSel: 'dgwt_jg_settings-group',
		reloadChoices: function ($el) {
			var _this = this,
				$group = $el.closest('.' + _this.groupSel),
				value = $group.find('.' + _this.selectSel + ' option:selected').val(),
				currentClass = '';

			_this.hideAll($group);

			value = value.replace('_', '-');

			if (value.length > 0) {
				currentClass = 'opt-' + value;
			}

			if ($('.' + currentClass).length > 0) {
				$('.' + currentClass).fadeIn();
			}

			if (value === 'simple') {
				setTimeout(function () {
					CAPTION_POSITION_TOGGLE.toggle();
				}, 500);
			}
		},
		hideAll: function ($group) {
			$group.find('tr[class*="opt-"]').hide();
		},
		registerListeners: function () {
			var _this = this;

			$('.' + _this.selectSel).on('change', function () {
				_this.reloadChoices($(this));
			});

		},
		init: function () {
			var _this = this,
				$sel = $('.' + _this.selectSel);

			if ($sel.length > 0) {
				_this.registerListeners();

				$sel.each(function () {
					_this.reloadChoices($(this));
				});
			}
		}

	};

	var CAPTION_POSITION_TOGGLE = {
		toggle: function () {
			var position = $('.js-ts-simple-caption-position input:checked').val();
			if (position === 'bottom') {
				$('.js-ts-simple-caption-display-mode').show();
			} else {
				$('.js-ts-simple-caption-display-mode').hide();
			}
		},

		init: function () {
			var that = this;
			$(document).on('change', '.js-ts-simple-caption-position input', function (event) {
				that.toggle();
			});
		}
	}

	var CUSTOMIZE_FOR_MOBILE_DEVICES = {
		toggle: function () {
			var enabled = $('.js-customize-for-mobile-devices input:checked').val();
			if (enabled) {
				$('.js-customize-for-mobile-devices-suboption').show();
			} else {
				$('.js-customize-for-mobile-devices-suboption').hide();
			}
		},

		init: function () {
			var that = this;
			$(document).on('change', '.js-customize-for-mobile-devices input', function (event) {
				that.toggle();
			});
			that.toggle();
		}
	}

	function moveOuterBorderOption() {
		var $elToMove = $('.js-dgwt-jg-settings-margin-nob');

		if ($elToMove.length > 0) {
			var $label = $elToMove.find('td label');
			if ($label.length > 0) {
				$label.clone().addClass('dgwt-jg-settings-margin-nob').appendTo('.js-dgwt-jg-settings-margin td');
				$elToMove.remove();
			}

		}
	}

	$(document).ready(function () {
		CAPTION_POSITION_TOGGLE.init();
		SETTINGS_TOGGLE.init();
		CUSTOMIZE_FOR_MOBILE_DEVICES.init();

		moveOuterBorderOption();
	});

})(jQuery);
