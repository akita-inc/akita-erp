import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'
import { Core } from '../package/yubinbango-core';
import moment from 'moment';
import * as AutoKana from "vanilla-autokana";

var ctrCustomersVl = new Vue({
    el: '#ctrCustomersVl',
    data: {
        lang:lang_date_picker,
        loading:false,
        customer_edit:0,
        customer_id:null,
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
            business_start_dt:"",
            consumption_tax_calc_unit_id:"",
            rounding_method_id:"",
            discount_rate:"",
            except_g_drive_bill_fg:false,
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
        testblur: function(event){
            const keyCode = event.keyCode
            console.log(keyCode);
            // Tab 9 or Enter 13
            if (keyCode === 9 || keyCode === 13) {
                this.popupVisible = false
                event.stopPropagation()
            }
        },
        clone: function(){
            this.field["clone"] = true;
            this.submit();
        },
        submit: function(){
            let that = this;
            that.loading = true;
            if(this.customer_edit == 1){
                this.field["id"] = this.customer_id;
                if(this.field["clone"] == true){
                    this.field["adhibition_start_dt"] = this.field["adhibition_start_dt_history"];
                    this.field["adhibition_end_dt"] = this.field["adhibition_end_dt_history"];
                }else{
                    this.field["adhibition_start_dt"] = this.field["adhibition_start_dt_edit"];
                    this.field["adhibition_end_dt"] = this.field["adhibition_end_dt_edit"];
                }
            }
            customers_service.submit(this.field).then((response) => {
                if(response.success == false){
                    that.errors = response.message;
                }else{
                    that.errors = [];
                    window.location.href = '/customers/list'
                }
                this.field["clone"] = null;
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
            if(this.field[input.target.id] == ""){
                this.field[destination] = "";
            }else{
                var furigana = this.autokana[input.target.id].getFurigana();
                var baseKana =  this.autokana[input.target.id].baseKana;
                this.field[destination] = furigana=='' ? baseKana : furigana;
            }
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
        },
        loadFormEdit: function () {
            let that = this;
            if($("#hd_customer_edit").val() == 1){
                this.loading = true;
                that.customer_edit = 1;
                that.customer_id = $("#hd_id").val();
                $.each(this.field,function (key,value) {
                    if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined && key != 'mst_bill_issue_destinations'){
                        if(key == "adhibition_start_dt" || key == "adhibition_end_dt"){
                            that.field[key + "_edit"] = $("#hd_"+key).val();
                        }
                        if(key == "except_g_drive_bill_fg"){
                            if($("#hd_"+key).val() == 1){
                                that.field[key] = true;
                            }
                        }else{
                            that.field[key] = $("#hd_"+key).val();
                        }
                    }

                });
                customers_service.getListBill(that.customer_id).then((response) => {
                    if(response.data != null && response.data.length > 0){
                        that.field.mst_bill_issue_destinations = response.data;
                    }
                    that.loading = false;
                });
            }
        },
        backToList: function () {
            customers_service.backHistory().then(function () {
                window.location.href = listRoute;
            });
        },
        deleteCustomer: function(id){
            var that = this;
            customers_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    that.backToList();
                    return false;
                } else {
                    if (confirm(messages["MSG06001"])) {
                        customers_service.deleteCustomer(id).then((response) => {
                            window.location.href = listRoute;
                        });
                    }
                }
            });
        },
    },
    mounted () {
        if(role!=9) {
            this.loadFormEdit();

            this.autokana ['customer_nm'] = AutoKana.bind('#customer_nm', '#customer_nm_kana', {katakana: true});
            this.autokana ['customer_nm_formal'] = AutoKana.bind('#customer_nm_formal', '#customer_nm_kana_formal', {katakana: true});
            this.autokana ['person_in_charge_last_nm'] = AutoKana.bind('#person_in_charge_last_nm', '#person_in_charge_last_nm_kana', {katakana: true});
            this.autokana ['person_in_charge_first_nm'] = AutoKana.bind('#person_in_charge_first_nm', '#person_in_charge_first_nm_kana', {katakana: true});
        }
    },
    components: {
        DatePicker,
        PulseLoader
    }
});
