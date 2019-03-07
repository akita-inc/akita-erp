$(function () {
    if ($.fn.datepicker !== undefined) {
        $.fn.datepicker.defaults.format = 'yyyy/mm/dd';
        $.fn.datepicker.defaults.language = 'ja';
        $.fn.datepicker.defaults.autoclose = true;
        $.fn.datepicker.defaults.todayHighlight = true;
    }
});