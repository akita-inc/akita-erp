import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'
import { Core } from '../package/yubinbango-core';
import moment from 'moment';
import * as AutoKana from "vanilla-autokana";

var ctrEmptyInfoVl = new Vue({
    el: '#ctrEmptyInfoVl',
    data: {
        lang:lang_date_picker,
        loading:false,
        customer_edit:0,
        customer_id:null,
        field:{
            id:"",
            status:"",
            regist_staff:"",
            regist_office_id:"",
            email_address:"",
            vehicle_kb:1,
            registration_numbers:"",
            vehicle_size:"",
            vehicle_body_shape:"",
            max_load_capacity:"",
            equipment:[],
            start_date:"",
            start_time:"",
            start_pref_cd:"",
            start_address:"",
            asking_price:"",
            asking_baggage:"",
            arrive_pref_cd:"",
            arrive_address:"",
            arrive_date:"",
            ask_date:"",
            ask_office:"",
            ask_staff:"",
            ask_staff_email_address:"",
            apr_date:"",
        },
        registration_numbers:"",
        errors:{},
    },
    methods : {
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
                    window.location.href = listRoute;
                }
                this.field["clone"] = null;
                that.loading = false;
            });
        },
        showError: function ( errors ){
            return errors.join("<br/>");
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
        backHistory: function () {
            if(this.customer_edit == 1){
                customers_service.backHistory().then(function () {
                    window.location.href = listRoute;
                });
            }else{
                window.location.href = listRoute;
            }
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
        delete: function(id){
            var that = this;
            empty_info_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    that.backHistory();
                    return false;
                } else {
                    if (confirm(messages["MSG06001"])) {
                        customers_service.delete(id).then((response) => {
                            window.location.href = listRoute;
                        });
                    }
                }
            });
        },
        searchVehicle: function () {
            var that = this;
            if(that.registration_numbers==''){
                alert(messages['MSG10009']);
                return;
            }else{
                if(isNaN(that.registration_numbers)){
                    alert(messages['MSG10009']);
                    return;
                }
            }
            if(that.field.vehicle_kb==1){
                empty_info_service.searchVehicle({registration_numbers: that.registration_numbers,mst_business_office_id:that.field.regist_office_id}).then((response) => {
                    if (!response.success) {
                        alert(response.msg);
                        return false;
                    } else {
                        let result = response.info;
                        that.field.registration_numbers = result.registration_numbers;
                        that.field.vehicle_size = result.vehicle_size_kb;
                        that.field.vehicle_body_shape = result.car_body_shape;
                        that.field.max_load_capacity = result.max_loading_capacity;
                    }
                });
            }

        },
        check: function (e) {
            let index = e.target.getAttribute('index');
            if(typeof this.field.equipment[index]== "undefined") {
                this.field.equipment[index] = {
                    id: e.target.value,
                    value: $('#equipment_value' + index).val()
                };
            }else{
                this.field.equipment.splice(index);
            }
        },
        input: function (e) {
            let index = e.target.getAttribute('index');
            if(typeof this.field.equipment[index]!= "undefined"){
                this.field.equipment[index].value = e.target.value;
            }
        }
    },
    mounted () {
        this.loadFormEdit();
    },
    components: {
        DatePicker,
        PulseLoader
    }
});
