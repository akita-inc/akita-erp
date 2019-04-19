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
            status:1,
            regist_office_id:user_login_mst_business_office_id,
            vehicle_kb:0,
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
            mode:$('#mode').val(),
        },
        registration_numbers:"",
        errors:{},
        checkOther:false,
        modified_at: "",

    },
    methods : {
        submit: function(status){
            let that = this;
            that.loading = true;
            if(this.field.mode != 'register'){
                this.field["id"] = this.empty_info_id;
            }
            this.removeComma('asking_price');
            this.removeComma('max_load_capacity');
            switch (this.field.mode) {
                case 'register':
                    empty_info_service.submit(that.field).then((response) => {
                        if(response.success == false){
                            that.addComma('asking_price');
                            that.addComma('max_load_capacity');
                            that.errors = response.message;
                        }else{
                            that.errors = [];
                            window.location.href = listRoute;
                        }
                        that.loading = false;
                        that.cursorWhenError();
                    });
                break;
                case 'edit':
                    empty_info_service.checkIsExist(that.empty_info_id, {'mode' : this.field.mode,'status': status,'modified_at': that.modified_at }).then((response) => {
                        if (!response.success) {
                            that.loading = false;
                            alert(response.msg);
                            that.backHistory();
                            return false;
                        } else {
                            empty_info_service.submit(that.field).then((response) => {
                                if(response.success == false){
                                    that.addComma('asking_price');
                                    that.addComma('max_load_capacity');
                                    that.errors = response.message;
                                }else{
                                    that.errors = [];
                                    window.location.href = listRoute;
                                }
                                that.loading = false;
                                that.cursorWhenError();
                            });
                        }
                    });
                    break;
                case 'reservation':
                case 'reservation_approval':
                    empty_info_service.checkIsExist(that.empty_info_id, {'status': status,'modified_at': that.modified_at}).then((response) => {
                        if (!response.success) {
                            that.loading = false;
                            alert(response.msg);
                            that.backHistory();
                            return false;
                        } else {
                            empty_info_service.updateStatus(that.empty_info_id,{status:status}).then((response) => {
                                that.loading = false;
                                window.location.href = listRoute;
                            });
                        }
                    });
                    break;
            }
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
            if(this.field.mode != 'register'){
                this.loading = true;
                that.empty_info_edit = 1;
                that.empty_info_id = $("#hd_id").val();
                $.each(this.field,function (key,value) {
                    if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined && key != 'mst_bill_issue_destinations'){
                        that.field[key] = $("#hd_"+key).val();
                        if(key == "asking_price"){
                            that.field[key] = '¥ '+$("#hd_"+key).val().toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                        }
                        if(key == "max_load_capacity"){
                            that.field[key] = $("#hd_"+key).val().toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                        }
                    }
                });
                if(this.field.mode=='reservation_approval'){
                    this.field.application_office_id = $("#hd_ask_office").val();;
                    this.field.reservation_person =$("#hd_reservation_person").val();
                }
                this.modified_at = $('#hd_modified_at').val();
                this.loading = false;
            }
        },
        deleteInfo: function(id){
            var that = this;
            empty_info_service.checkIsExist(id,{'mode' : 'delete'}).then((response) => {
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
            if(that.field.vehicle_kb==0){
                if(that.registration_numbers==''){
                    alert(messages['MSG10009']);
                    return;
                }else{
                    if(isNaN(that.registration_numbers)){
                        alert(messages['MSG10010']);
                        return;
                    }
                }
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
                        that.addComma('max_load_capacity');
                    }
                });
            }else{
                alert(messages['MSG10012']);
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
                }else{
                    this.field.equipment.push( {
                        id: id,
                        value: e.target.value,
                    });
                    $('#equipment'+id).prop('checked', true);
                }
            }
        },
        addComma: function (id) {
            this.field[id] = (id=='asking_price' && this.field[id]!='' ? '¥ ' : '' )+this.field[id].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        removeComma: function (id) {
            this.field[id] = this.field[id].toString().replace(/,/g, '').replace('¥ ','');
        },
        resetForm: function () {
            this.registration_numbers = "";
            this.errors = {};
            this.checkOther = false;
            if($("#hd_empty_info_edit").val() == 1){
                this.loadFormEdit();
            }else{
                this.field = {
                    status:1,
                    regist_staff:"",
                    regist_office_id:user_login_mst_business_office_id,
                    email_address:"",
                    vehicle_kb:0,
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
                $('input:text').val('');
                $('input[type="tel"]').val('');
                $('textarea').val('');
            }
            $("#search_vehicle").focus();
        },
        setInputFilter: function (textbox, inputFilter) {
            ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
                textbox.addEventListener(event, function() {
                    if (inputFilter(this.value)) {
                        this.oldValue = this.value;
                        this.oldSelectionStart = this.selectionStart;
                        this.oldSelectionEnd = this.selectionEnd;
                    } else if (this.hasOwnProperty("oldValue")) {
                        this.value = this.oldValue;
                        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                    }
                });
            });
        },
        cursorWhenError: function () {
            var that = this;
            $.each( that.field, function( key, value ) {
                if(key=='equipment'){
                    if(typeof that.errors['equipment'] != "undefined"){
                        $('#equipment_value1').focus();
                        return false;
                    }
                    if(typeof that.errors['equipment_value'] != "undefined"){
                        $.each( that.errors['equipment_value'][0], function( key1, value1 ) {
                            $('#equipment_value'+key1).focus();
                            return false;
                        });
                    }
                }else if(key=='start_date' || key=='start_time' || key=='arrive_date'){
                    if(typeof that.errors[key] != "undefined") {
                        $("#" + key + ' input').focus();
                        return false;
                    }
                }else{
                    if(typeof that.errors[key] !== "undefined") {
                        $("#" + key).focus();
                        return false;
                    }
                }
            });
        }
    },
    mounted () {
        this.loadFormEdit();
        if(this.field.mode == 'register' || this.field.mode == 'edit' ){
            $("#search_vehicle").focus();
        }
    },
    components: {
        DatePicker,
        PulseLoader
    }
});
