import { Core } from '../package/yubinbango-core';
import DatePicker from '../component/vue2-datepicker-master'

var ctrVehiclesVl = new Vue({
    el: '#ctrVehiclesVl',
    data: {
        adhibition_start_dt:$('#adhibition_start_dt').val(),
        adhibition_end_dt:$('#adhibition_end_dt').val(),
        adhibition_start_dt_new:$('#adhibition_start_dt_new').val(),
        business_start_dt:$('#business_start_dt').val(),
        registration_dt:$('#registration_dt').val(),
        expiry_dt:$('#expiry_dt').val(),
        vehicle_delivery_dt:$('#vehicle_delivery_dt').val(),
        dispose_dt:$('#dispose_dt').val(),
        lang:lang_date_picker,
    },
    methods : {
        onBlur: function(){
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
        deleteVehicle: function(id){
            vehicles_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.backHistory();
                    return false;
                } else {
                    if (confirm(messages["MSG06001"])) {
                        $('#form1').attr('action',deleteRoute);
                        $('#form1').submit();
                    }
                }
            });
        },
        backHistory: function () {
            vehicles_service.backHistory().then(function () {
                window.location.href = listRoute;
            });
        }
    },
    mounted () {

    },
    components: {
        DatePicker
    }
});
