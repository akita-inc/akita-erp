import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from 'vue2-datepicker';
import { Core } from '../package/yubinbango-core';
import moment from 'moment';
import * as AutoKana from "vanilla-autokana";

var ctrCustomersVl = new Vue({
    el: '#ctrCustomersVl',
    data: {
        lang:lang_date_picker,
        loading:false,
        field:{
            adhibition_start_dt:"",
            adhibition_end_dt:$("#hd_adhibition_end_dt_default").val(),
            adhibition_start_dt_edit:"",
            adhibition_end_dt_edit:$("#hd_adhibition_end_dt_default").val(),
            adhibition_start_dt_history:"",
            adhibition_end_dt_history:$("#hd_adhibition_end_dt_default").val(),
            mst_customers_cd:"",
            customer_nm:"",
            customer_nm_kana:"",
            customer_nm_formal:"",
            customer_nm_kana_formal:"",
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
            mst_bill_issue_destinations:[{
                prefectures_cd:"",
                address1:"",
                address2:"",
                address3:"",
                phone_number:"",
                fax_number:""
            }],
            deposit_bank_cd:"",
            mst_account_titles_id: "",
            mst_account_titles_id_2: "",
            mst_account_titles_id_3: "",
            notes:"",
        },
        errors:{},
        dateFormat: {
            stringify: (date) => {
                return date ? moment(date).format('YYYY MM DD') : null
            },
            parse: (value) => {
                return value ? moment(value, 'YYYY MM DD').toDate() : null
            }
        },
        autokana:[],
    },
    methods : {
        submit: function(){
            let that = this;
            that.loading = true;
            customers_service.submit(this.field).then((response) => {
                if(response.success == false){
                    that.errors = response.message;
                }else{
                    that.errors = [];
                    window.location.href = '/customers/list'
                }
                that.loading = false;
            });
        },
        showError: function ( errors ){
            return errors.join("<br/>");
        },
        addRows: function () {
            this.field.mst_bill_issue_destinations.push({
                prefectures_cd:"",
                address1:"",
                address2:"",
                address3:"",
                phone_number:"",
                fax_number:""
            });
        },
        convertKana: function (input , destination) {
            this.field[destination] = this.autokana[input.target.id].getFurigana();
        },
        getAddrFromZipCode: function() {
            let that = this;
            let zip = $('#zip_cd').val();
            if(zip==''){
                alert(messages['MSG07001']);
                return;
            }else{
                if(isNaN(zip)){
                    alert(messages['MSG07002']);
                    return;
                }
            }
            new Core(zip, function (addr) {
                if(addr.region_id=="" || addr.locality=="" || addr.street==""){
                    alert(messages['MSG07002']);
                }else {
                    that.field.prefectures_cd = addr.region_id;
                    that.field.address1 = addr.locality;
                    that.field.address2 = addr.street;
                }
            });
        },
        getAddrFromZipCodeCollapse:function(index)
        {
            let that = this;
            let zip = this.field.mst_bill_issue_destinations[index].zip_cd;
            if(zip==''){
                alert(messages['MSG07001']);
                return;
            }else{
                if(isNaN(zip)){
                    alert(messages['MSG07002']);
                    return;
                }
            }
            new Core(zip, function (addr) {
                if(addr.region_id=="" || addr.locality=="" || addr.street==""){
                    alert(messages['MSG07002']);
                }else {
                    that.field.mst_bill_issue_destinations[index].prefectures_cd = addr.region_id;// 都道府県ID
                    that.field.mst_bill_issue_destinations[index].address1 = addr.locality;// 市区町村
                    that.field.mst_bill_issue_destinations[index].address2 = addr.street;// 町域
                    that.field.mst_bill_issue_destinations.push({});
                    that.field.mst_bill_issue_destinations.splice(that.field.mst_bill_issue_destinations.length - 1, 1);
                }
            });
        },
        removeRows: function (index) {
            this.field.mst_bill_issue_destinations.splice(index, 1);
        },
        backHistory: function () {
            window.history.back();
        }
    },
    mounted () {
        let that = this;
        $.each(this.field,function (key,value) {
            if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined && key != 'mst_bill_issue_destinations'){
                if(key == "hd_adhibition_start_dt" || key == "hd_adhibition_end_dt"){
                    that.field[key + "_edit"] = $("#hd_"+key).val();
                }
                that.field[key] = $("#hd_"+key).val();
            }
        });

        this.autokana ['customer_nm'] = AutoKana.bind('#customer_nm', '#customer_nm_kana', { katakana: true });
        this.autokana ['customer_nm_formal'] = AutoKana.bind('#customer_nm_formal', '#customer_nm_kana_formal', { katakana: true });
        this.autokana ['person_in_charge_last_nm'] = AutoKana.bind('#person_in_charge_last_nm', '#person_in_charge_last_nm_kana', { katakana: true });
        this.autokana ['person_in_charge_first_nm'] = AutoKana.bind('#person_in_charge_first_nm', '#person_in_charge_first_nm_kana', { katakana: true });
    },
    components: {
        DatePicker,
        PulseLoader
    }
});
