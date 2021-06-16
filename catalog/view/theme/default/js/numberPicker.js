(function quantity($) {
	$.fn.numberPicker = function qunt() {
		var dis = 'disabled';
		
		return this.each(function fn2() {
			var picker = $(this);
			var p = picker.find('button:last-child');
			var m = picker.find('button:first-child');
			var input = picker.find('input');
			var min = parseInt(input.attr('min'), 10);
			var max = parseInt(input.attr('max'), 10);
			
			var inputFunc = function fn3(picker) {
				var i = parseInt(input.val(), 10);
				if (i <= min || !i) {
					input.val(min);
					// p.prop(dis, false);
					// m.prop(dis, true);
					} else if (i >= max) {
					input.val(max);
					// p.prop(dis, true);
					// m.prop(dis, false);
					} else {
					// p.prop(dis, false);
					// m.prop(dis, false);
				}
			};
			
			var changeFunc = function changeFn(picker, qty) {
				var q = parseInt(qty, 10);
				var i = parseInt(input.val(), 10);
				if (i < max && q > 0 || i > min && !(q > 0)) {
					input.val(i + q);
					inputFunc(picker);
				}
			};
			m.off('click');
			p.off('click');
			m.on('click', function () {
				changeFunc(picker, -1);
			});
			p.on('click', function () {
				changeFunc(picker, 1);
			});
			input.on('change', function () {
				inputFunc(picker);
			});
			inputFunc(picker);
		});
	};
})(jQuery);

