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
        empty_info_edit:0,
        empty_info_id:null,
        field:{
            regist_office_id:user_login_mst_business_office_id,
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
        },
        registration_numbers:"",
        errors:{},
        checkOther:false
    },
    methods : {
        submit: function(){
            let that = this;
            that.loading = true;
            if(this.empty_info_edit == 1){
                this.field["id"] = this.empty_info_id;
            }
            let data = this.field;
            let asking_price = data.asking_price;
            if(asking_price!=""){
                data.asking_price = asking_price.replace(/,/g, '');
            }
            empty_info_service.submit(this.field).then((response) => {
                if(response.success == false){
                    that.addComma();
                    that.errors = response.message;
                }else{
                    that.errors = [];
                    window.location.href = listRoute;
                }
                that.loading = false;
            });
        },
        showError: function ( errors ){
            return errors.join("<br/>");
        },
        backHistory: function () {
            if(this.empty_info_edit == 1){
                empty_info_service.backHistory().then(function () {
                    window.location.href = listRoute;
                });
            }else{
                window.location.href = listRoute;
            }
        },
        loadFormEdit: function () {
            let that = this;
            if($("#hd_empty_info_edit").val() == 1){
                this.loading = true;
                that.empty_info_edit = 1;
                that.empty_info_id = $("#hd_id").val();
                $.each(this.field,function (key,value) {
                    if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined && key != 'mst_bill_issue_destinations'){
                        that.field[key] = $("#hd_"+key).val();
                        if(key == "asking_price"){
                            that.field[key] = $("#hd_"+key).val().toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                        }
                    }

                });
                this.loading = false;
            }
        },
        deleteInfo: function(id){
            var that = this;
            empty_info_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    that.backHistory();
                    return false;
                } else {
                    if (confirm(messages["MSG06001"])) {
                        empty_info_service.delete(id).then((response) => {
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
            let id = e.target.getAttribute('index');
            var index = this.field.equipment.findIndex(function(elem){
                return (elem.id ==  id);
            });
            if(index==-1) {
                if(id==0){
                    this.checkOther =  true;
                }
                this.field.equipment.push({
                    id: e.target.value,
                    value: $('#equipment_value' + id).val(),
                });
            }else{
                if(id==0){
                    this.checkOther =  false;
                }
                this.field.equipment.splice(index,1);
            }
        },
        input: function (e) {
            let id = e.target.getAttribute('index');
            var index = this.field.equipment.findIndex(function(elem){
                return (elem.id ==  id);
            });
            if(index!=-1){
                this.field.equipment[index].value = e.target.value;
            }else{
                if(id==0){
                    if(e.target.value!=''){
                        this.checkOther =  true;
                        this.field.equipment.push( {
                            id: 0,
                            value: e.target.value,
                        });
                    }
                }
            }
        },
        addComma: function () {
            this.field.asking_price = this.field.asking_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        resetForm: function () {
            this.registration_numbers = "";
            this.errors = {};
            this.checkOther = false;
            if($("#hd_empty_info_edit").val() == 1){
                this.loadFormEdit();
            }else{
                this.field = {
                    id:"",
                    status:"",
                    regist_staff:"",
                    regist_office_id:user_login_mst_business_office_id,
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
                };
                $('input:checkbox').prop('checked',false);
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
