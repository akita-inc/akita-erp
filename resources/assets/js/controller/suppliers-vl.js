import { Core } from '../package/yubinbango-core';
import DatePicker from 'vue2-datepicker'
import historykana from 'historykana'

var ctrSupplierrsVl = new Vue({
    el: '#ctrSupplierrsVl',
    data: {
        adhibition_start_dt:$('#adhibition_start_dt').val(),
        adhibition_end_dt:$('#adhibition_end_dt').val(),
        business_start_dt:$('#business_start_dt').val(),
        adhibition_start_dt_new:$('#adhibition_start_dt_new').val(),
        lang:lang_date_picker,
        name: '',
        furigana: '',
        history: []
    },
    methods : {
        convertKana: function (input , destination) {
            this.history.push(input.target.value);
            this.furigana = historykana(this.history);
            suppliers_service.convertKana({'data': this.furigana}).then(function (data) {
                $('#'+destination).val(data.info);
            });
        },
        onBlur: function(){
            this.history = [];
            this.furigana = '';
        },
        getAddrFromZipCode: function() {
            var zip = $('#zip_cd').val();
            if(zip==''){
                alert(messages['MSG07001']);
            }
            new Core(zip, function (addr) {
                if(addr.region_id=="" || addr.locality=="" || addr.street==""){
                    alert(messages['MSG07002']);
                }else{
                    $('#prefectures_cd').val(addr.region_id);// 都道府県ID
                    $('#address1').val(addr.locality);// 市区町村
                    $('#address2').val(addr.street);// 町域
                }
            });
        },
    },
    mounted () {

    },
    components: {
        DatePicker
    }
});
