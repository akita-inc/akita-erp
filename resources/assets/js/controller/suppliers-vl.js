import { Core } from '../package/yubinbango-core';
import DatePicker from 'vue2-datepicker'
import historykana from 'historykana'

var ctrSupplierrsVl = new Vue({
    el: '#ctrSupplierrsVl',
    data: {
        adhibition_start_dt:$('#adhibition_start_dt').val(),
        business_start_dt:$('#business_start_dt').val(),
        lang:lang_date_picker,
        name: '',
        furigana: '',
        history: []
    },
    methods : {
        convertKana: function (input , destination) {
            this.history.push(input.target.value);
            this.furigana = historykana(this.history);
            // $('#'+destination).val(this.furigana);
            suppliers_service.convertKana({'data': this.furigana}).then(function (data) {
                $('#'+destination).val(data.info);
            });
        },
        onBlur: function(){
            console.log(1);
            this.history = [];
            this.furigana = '';
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
