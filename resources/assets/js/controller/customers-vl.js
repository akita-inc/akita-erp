import DatePicker from 'vue2-datepicker';
import { Core } from '../package/yubinbango-core';
import moment from 'moment';
import historykana from "historykana";

var ctrCustomersVl = new Vue({
    el: '#ctrCustomersVl',
    data: {
        lang:lang_date_picker,
        furigana: '',
        history: [],
        field:{
            adhibition_start_dt:"",
            adhibition_end_dt:"2999/12/31",
            customer_nm:"",
            customer_nm_kana:"",
            customer_nm_formal:"",
            person_in_charge_last_nm:"",
            person_in_charge_first_nm:"",
            person_in_charge_last_nm_kana:"",
            person_in_charge_first_nm_kana:"",
            zip_cd:"",
            prefectures_cd:"",
            address1:"",
            address2:"",
            address3:"",
            phone_number:"",
            fax_number:"",
            hp_url:"",
            customer_category_id:"",
            prime_business_office_id:"",
            explanations_bill:"",
            bundle_dt:"",
            deposit_month_id:"",
            deposit_day:"",
            deposit_method_id:"",
            deposit_method_notes:"",
            consumption_tax_calc_unit_id:"",
            rounding_method_id:"",
            discount_rate:"",
            except_g_drive_bill_fg:"",
            mst_bill_issue_destinations:[{}],
            deposit_bank_cd:"",
            mst_account_titles_id: "",
            mst_account_titles_id_2: "",
            mst_account_titles_id_3: "",
            notes:"",
        }
    },
    methods : {
        dateFormat: {
            stringify: (date) => {
                return date ? moment(date).format('YYYY MM DD') : null
            },
            parse: (value) => {
                return value ? moment(value, 'YYYY MM DD').toDate() : null
            }
        },
        addRows: function () {
            this.field.mst_bill_issue_destinations.push({});
        },
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
            new Core(zip, function (addr) {
                $('#prefectures_cd').val(addr.region_id);// 都道府県ID
                $('#address1').val(addr.locality);// 市区町村
                $('#address2').val(addr.street);// 町域
            });
        },
        getAddrFromZipCodeCollapse:function(index)
        {
            var zip_cd='#mst_bill_issue_destinations_zip_cd'+index;
            var prefectures_cd='#mst_bill_issue_destinations_prefectures_cd'+index;
            var address1='#mst_bill_issue_destinations_address1'+index;
            var address2='#mst_bill_issue_destinations_address2'+index;
            var zip = $(zip_cd).val();
            new Core(zip, function (addr) {
                $(prefectures_cd).val(addr.region_id);// 都道府県ID
                $(address1).val(addr.locality);// 市区町村
                $(address2).val(addr.street);// 町域
            });
        },
        removeRows: function (index) {
            this.field.mst_bill_issue_destinations.splice(index, 1);
        }
    },
    mounted () {
    },
    components: {
        DatePicker
    }
});
