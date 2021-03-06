import { Core } from '../package/yubinbango-core';
import DatePicker from '../component/vue2-datepicker-master'
import * as AutoKana from 'vanilla-autokana';

var ctrSupplierrsVl = new Vue({
    el: '#ctrSupplierrsVl',
    data: {
        business_start_dt:$('#business_start_dt').val(),
        lang:lang_date_picker,
    },
    methods : {
        submit: function(){
            if(supplier_id==""){
                $('#form1').submit();
            }else{
                var that = this;
                suppliers_service.checkIsExist(supplier_id,{'mode':'edit', 'modified_at': $('#modified_at').val()}).then((response) => {
                    if (!response.success) {
                        alert(response.msg);
                        that.backHistory();
                        return false;
                    } else {
                        $('#form1').submit();
                    }
                });
            }
        },
        getAddrFromZipCode: function() {
            var zip = $('#zip_cd').val();
            if(zip==''){
                alert(messages['MSG07001']);
            }else{
                if(isNaN(zip)){
                    alert(messages['MSG07002']);
                }
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
        deleteSupplier: function(id){
            var that = this;
            suppliers_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    that.backHistory();
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
            if(supplier_id==""){
                window.location.href = listRoute;
            }else{
                suppliers_service.backHistory().then(function () {
                    window.location.href = listRoute;
                });
            }
        }
    },
    mounted () {
        if(role==1 || (role==2 && supplier_id!='')) {
            AutoKana.bind('#supplier_nm', '#supplier_nm_kana', {katakana: true});
            AutoKana.bind('#supplier_nm_formal', '#supplier_nm_kana_formal', {katakana: true});
            AutoKana.bind('#dealing_person_in_charge_last_nm', '#dealing_person_in_charge_last_nm_kana', {katakana: true});
            AutoKana.bind('#dealing_person_in_charge_first_nm', '#dealing_person_in_charge_first_nm_kana', {katakana: true});
            AutoKana.bind('#accounting_person_in_charge_last_nm', '#accounting_person_in_charge_last_nm_kana', {katakana: true});
            AutoKana.bind('#accounting_person_in_charge_first_nm', '#accounting_person_in_charge_first_nm_kana', {katakana: true});
        }
    },
    components: {
        DatePicker
    }
});
