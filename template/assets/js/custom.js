(function() {
	'use strict';
	window.addEventListener('load', function() {

		var forms = document.getElementsByClassName('needs-validation');
		var validation = Array.prototype.filter.call(forms, function(form) {
			form.addEventListener('submit', function(event) {
				if (form.checkValidity() === false) {
					event.preventDefault();
					event.stopPropagation();
				}
				form.classList.add('was-validated');
			}, false);
		});
	}, false);
})();

$(document).ready(function(){

	$('[data-toggle="tooltip"]').tooltip();
	
	$('form.noSubmit').submit(function(){
		return false;
	});

	$('.mdb-select').materialSelect({
		destroy: true,
		BSsearchIn: true
	});

	$('.datepicker').pickadate({
		monthsFull: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10',
		'Tháng 11', 'Tháng 12'],
		monthsShort: ['thg 1', 'thg 2', 'thg 3', 'thg 4', 'thg 5', 'thg 6', 'thg 7', 'thg 8', 'thg 9', 'thg 10', 'thg 11', 'thg 12'],
		weekdaysFull: ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'],
		weekdaysShort: ['C.nhật', 'T.hai', 'T.ba', 'T.tư', 'T.năm', 'T.sáu', 'T.bảy'],

		// Buttons
		today: 'Hôm nay',
		clear: 'Đặt lại',
		close: 'Hủy bỏ',

		// Accessibility labels
		labelMonthNext: 'Tháng sau',
		labelMonthPrev: 'Tháng trước',
		labelMonthSelect: 'Chọn một tháng',
		labelYearSelect: 'Chọn một năm',

		// Formats
		format: 'dd/mm/yyyy',
		formatSubmit: 'dd/mm/yyyy',
		hiddenSuffix: '_submit',

		closeOnSelect: true,
		closeOnClear: true,

	});
});