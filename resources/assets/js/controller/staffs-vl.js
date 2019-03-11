import { Core } from '../package/yubinbango-core';
import DatePicker from 'vue2-datepicker';
var ctrStaffsVl = new Vue({
    el: '#ctrStaffsVl',
    data: {
        adhibition_start_dt:$('#adhibition_start_dt').val(),
        business_start_dt:$('#business_start_dt').val(),
        lang:lang_date_picker,
    },
    methods : {
        convertKana: function (e , destination) {
            suppliers_service.convertKana({'data': e.target.value}).then(function (data) {
                $('#'+destination).val(data.info);
            });
        },
        getAddrFromZipCode: function() {
            var zip = $('#zip_cd').val();
            new Core(zip, function (addr) {
                $('#prefectures_cd').val(addr.region_id);// 都道府県ID
                $('#address1').val(addr.locality);// 市区町村
                $('#address2').val(addr.street);// 町域
            });
        },
        onChangeDatepicker1: function (input) {
            $('#adhibition_start_dt').val(input)
        },
        onChangeDatepicker2: function (input) {
            $('#business_start_dt').val(input)
        }
    },
    mounted () {
    },
    components: {
        DatePicker
    }
});
