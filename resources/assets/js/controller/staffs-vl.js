import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import { Core } from '../package/yubinbango-core';
import DatePicker from '../component/vue2-datepicker-master'
import moment from "moment";
import historykana from "historykana";
import Dropdown from 'vue-simple-search-dropdown';
import * as AutoKana from "vanilla-autokana";

var ctrStaffsVl = new Vue({
    el: '#ctrStaffsVl',
    data: {
        lang:lang_date_picker,
        furigana: '',
        history: [],
        loading:false,
        staff_edit:0,
        staff_id:null,
        field:{
            staff_cd:"",
            adhibition_start_dt:"",
            adhibition_end_dt:$("#hd_adhibition_end_dt_default").val(),
            adhibition_start_dt_edit:"",
            adhibition_end_dt_edit:$("#hd_adhibition_end_dt_default").val(),
            adhibition_start_dt_history:"",
            adhibition_end_dt_history:$("#hd_adhibition_end_dt_default").val(),
            password:"",
            employment_pattern_id:"",
            position_id:"",
            last_nm:"",
            first_nm:"",
            last_nm_kana:"",
            first_nm_kana:"",
            zip_cd:"",
            prefectures_cd:"",
            address1:"",
            address2:"",
            address3:"",
            landline_phone_number:"",
            cellular_phone_number:"",
            corp_cellular_phone_number:"",
            notes:"",
            sex_id:"",
            birthday:"",
            enter_date:"",
            retire_date:"",
            insurer_number:"",
            basic_pension_number:"",
            person_insured_number:"",
            health_insurance_class:"",
            welfare_annuity_class:"",
            relocation_municipal_office_cd:"",
            educational_background:"",
            educational_background_dt:"",
            mst_staff_job_experiences: [{
                job_duties: "",
                staff_tenure_start_dt: "",
                staff_tenure_end_dt: ""
            }],
            mst_staff_qualifications: [{
                qualification_kind_id: "",
                acquisition_dt: "",
                period_validity_start_dt: "",
                period_validity_end_dt: "",
                qualifications_notes: "",
                amounts: "",
                payday: "",
                disp_number: "",
            }],
            mst_staff_dependents:[{
                dept_dependent_kb:"",
                dept_last_nm:"",
                dept_last_nm_kana:"",
                dept_first_nm:"",
                dept_first_nm_kana:"",
                dept_birthday:"",
                dept_sex_id:"",
                dept_social_security_number:"",
            }],
            drivers_license_number:"",
            drivers_license_color_id:"",
            drivers_license_issued_dt:"",
            drivers_license_period_validity:"",
            drivers_license_picture:"",
            drivers_license_divisions_1:"",
            drivers_license_divisions_2:"",
            drivers_license_divisions_3:"",
            drivers_license_divisions_4:"",
            drivers_license_divisions_5:"",
            drivers_license_divisions_6:"",
            drivers_license_divisions_7:"",
            drivers_license_divisions_8:"",
            drivers_license_divisions_9:"",
            drivers_license_divisions_10:"",
            drivers_license_divisions_11:"",
            drivers_license_divisions_12:"",
            drivers_license_divisions_13:"",
            drivers_license_divisions_14:"",
            retire_reasons:"",
            retire_dt:"",
            death_reasons:"",
            death_dt:"",
            belong_company_id:"",
            occupation_id:"",
            mst_business_office_id:"",
            depertment_id:"",
            driver_election_dt:"",
            medical_checkup_interval_id:"",
            employment_insurance_numbers:"",
            health_insurance_numbers:"",
            employees_pension_insurance_numbers:"",
            workmens_compensation_insurance_fg:"",
            admin_fg:"",
            mst_role_id:"",
            mst_staff_auths: {
                1: {
                    staffScreen: [],
                    screen_category_id:1,
                    accessible_kb: 9,
                },
                2: {
                    screen_category_id: 2,
                    accessible_kb: 9,
                },
                3: {
                    screen_category_id: 3,
                    accessible_kb: 9,
                },
                4: {
                    screen_category_id: 4,
                    accessible_kb: 9,
                },
            },
            deleteFile:''
        },
        image_drivers_license_picture:"",
        dropdown_relocate_municipal_office_nm:[],
        errors:{},
        dateFormat: {
            stringify: (date) => {
                return date ? moment(date).format('YYYY MM DD') : null
            },
            parse: (value) => {
                return value ? moment(value, 'YYYY MM DD').toDate() : null
            }
        },
        index:0,
        autokana:[],
    },
    methods : {
        clone: function(){
            this.field["clone"] = true;
            this.submit();
        },
        showError: function ( errors ){
            return errors.join("<br/>");
        },
        onFileChange:function(e) {
            this.field.drivers_license_picture = e.target.files[0];
            this.image_drivers_license_picture=e.target.files[0].name;

        },
        deleteFileUpload:function()
        {
            this.$refs.drivers_license_picture.value = '';
            this.image_drivers_license_picture="";
            this.field.drivers_license_picture="";
            this.field.deleteFile = 'drivers_license_picture';
        },
        submit:function()
        {
            let that = this;
            that.loading = true;
            if(this.staff_edit == 1){
                this.field["id"] = this.staff_id;
                if(this.field["clone"] == true){
                    this.field["adhibition_start_dt"] = this.field["adhibition_start_dt_history"];
                    this.field["adhibition_end_dt"] = this.field["adhibition_end_dt_history"];
                }else{
                    this.field["adhibition_start_dt"] = this.field["adhibition_start_dt_edit"];
                    this.field["adhibition_end_dt"] = this.field["adhibition_end_dt_edit"];
                }
            }
            let formData = new FormData();

            formData.append('data', JSON.stringify(this.field));
            formData.append('image', this.field.drivers_license_picture);


            staffs_service.submit(formData).then((response) => {
                if(response.success == false){
                    that.errors = response.message;
                }
                else
                {
                    that.errors = {};
                    window.location.href = '/staffs/list';
                }
                this.field["clone"] = null;
                that.loading = false;
            });
        },
        loadFormEdit: function () {
            let that = this;
            if($("#hd_staff_edit").val() == 1){
                this.loading = true;
                that.staff_edit = 1;
                that.staff_id = $("#hd_id").val();
                $.each(this.field,function (key,value) {
                    if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined && key != 'mst_staff_job_experiences'){
                        if(key == "adhibition_start_dt" || key == "adhibition_end_dt"){
                            that.field[key + "_edit"] = $("#hd_"+key).val();
                        }
                        that.field.workmens_compensation_insurance_fg=that.field.workmens_compensation_insurance_fg==0?"":1;
                        that.image_drivers_license_picture = $("#hd_drivers_license_picture").val();
                        that.field[key] = $("#hd_"+key).val();
                        that.field.drivers_license_picture ='';
                    }

                });
                that.getMstCollapses();

            }
        },
        getMstCollapses:function()
        {
            let that=this;
            staffs_service.getListStaffJobEx(that.staff_id).then((response) => {
                if(response.data != null && response.data.length > 0){
                    that.field.mst_staff_job_experiences = response.data;
                }
            });
            staffs_service.getListStaffQualifications(that.staff_id).then((response) => {
                if(response.data != null && response.data.length > 0){
                    that.field.mst_staff_qualifications = response.data;
                }
            });
            staffs_service.getStaffDependents(that.staff_id).then((response) => {
                if(response.data != null && response.data.length > 0){
                    that.field.mst_staff_dependents = response.data;
                }
            });
            staffs_service.getStaffAuths(that.staff_id).then((response) => {

                if(response.data != null){
                    $.each(response.data,function (key,value) {
                        if(key==1){
                            if(value.length > 0){
                                $.each(value,function (key1,value1) {
                                    that.field.mst_staff_auths[key].staffScreen.push(value1.mst_screen_id);
                                    that.field.mst_staff_auths[key].accessible_kb = value1.accessible_kb
                                });
                            }

                        }else{
                            that.field.mst_staff_auths[key].accessible_kb = value[0].accessible_kb
                        }

                    });
                }
            });
            that.loading = false;
        },
        onChange:function(event)
        {
            this.field.relocation_municipal_office_cd=event.target.value;
            // this.handleSelect({id:event.target.value,name:event.target.selectedOptions[0].text});
            // this.handleSelect({id:event.target.value,name:'abc2'});
        },
        addRows: function (block) {
            let value;
            let that=this;
            switch (block) {
                case 'mst_staff_job_experiences':
                    value = {
                        job_duties: "",
                        staff_tenure_start_dt: "",
                        staff_tenure_end_dt: ""
                    };
                    break;
                case 'mst_staff_qualifications':
                    value = {
                        qualification_kind_id: "",
                        acquisition_dt: "",
                        period_validity_start_dt: "",
                        period_validity_end_dt:"",
                        qualifications_notes:"",
                        amounts:"",
                        payday:"",
                        disp_number:"",
                    };
                    break;
                case 'mst_staff_dependents':
                    value = {
                        dept_dependent_kb:"",
                        dept_last_nm:"",
                        dept_last_nm_kana:"",
                        dept_first_nm:"",
                        dept_first_nm_kana:"",
                        dept_birthday:"",
                        dept_sex_id:"",
                        dept_social_security_number:"",
                    };
                    this.index+=1;
                    break;
            }
            this.field[block].push(value);
        },
        convertKana: function (input , destination) {
            if(this.field[input.target.id] == ""){
                this.field[destination] = "";
            }else{
                this.field[destination] = this.autokana[input.target.id].getFurigana();
            }
        },
        convertKanaBlock:function(input,destination){
            console.log(input.target.id);
            let kana="";
            if(this.field[input.target.id] == ""){
                kana = "";
            }else{
                kana = this.autokana[input.target.id].getFurigana();
                console.log(kana);
            }
            this.field.mst_staff_dependents[this.index][destination]=kana;

        },
        getAddrFromZipCode: function() {
            var that=this;
            var zip = that.field.zip_cd;
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
                }
                else{
                        that.field.prefectures_cd=addr.region_id;// 都道府県ID
                        that.field.address1=addr.locality;// 市区町村
                        that.field.address2=addr.street;// 町域
                }
            });
        },
        removeRows: function (block,index) {
            this.field[block].splice(index, 1);
        },
        backHistory: function () {
            staffs_service.backHistory().then(function () {
                window.location.href = listRoute;
            });
        },
        loadRoleConfig: function () {
            var that = this;
            that.field.mst_staff_auths = {
                1: {
                    staffScreen: [],
                    screen_category_id:1,
                    accessible_kb: 9,
                },
                2: {
                    screen_category_id: 2,
                    accessible_kb: 9,
                },
                3: {
                    screen_category_id: 3,
                    accessible_kb: 9,
                },
                4: {
                    screen_category_id: 4,
                    accessible_kb: 9,
                },
            }
            if(this.field.mst_role_id=='') return;
            staffs_service.loadRoleConfig(this.field.mst_role_id).then(function (result) {
                var data =  result.data;
                if(data.length > 0){
                    data.forEach(function(item) {
                        switch (item.screen_category_id) {
                            case 1:
                                that.field.mst_staff_auths[1].staffScreen.push(item.mst_screen_id);
                                that.field.mst_staff_auths[1].accessible_kb = item.accessible_kb;
                                break;
                            default:
                                that.field.mst_staff_auths[item.screen_category_id].accessible_kb = item.accessible_kb;
                        }
                    });
                }else{
                    that.field.mst_staff_auths = {
                        1: {
                            staffScreen: [],
                            screen_category_id:1,
                            accessible_kb: 9,
                        },
                        2: {
                            screen_category_id: 2,
                            accessible_kb: 9,
                        },
                        3: {
                            screen_category_id: 3,
                            accessible_kb: 9,
                        },
                        4: {
                            screen_category_id: 4,
                            accessible_kb: 9,
                        },
                    }
                }
            });
        },
        handleSelect: function (selected) {
            if(typeof selected.id!="undefined"){
                this.field.relocation_municipal_office_cd = selected.id;
            }
        },
        deleteStaff: function(id){
            var that = this;
            staffs_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    that.backToList();
                    return false;
                } else {
                    if (confirm(messages["MSG06001"])) {
                        staffs_service.deleteStaffs(id).then((response) => {
                            window.location.href = listRoute;
                        });
                    }
                }
            });
        },
        backToList: function () {
            staffs_service.backHistory().then(function () {
                window.location.href = listRoute;
            });
        },
        showKana:function (index) {
            this.autokana ['mst_staff_dependents_last_nm'+index] = AutoKana.bind('#mst_staff_dependents_last_nm'+index, '#mst_staff_dependents_last_nm_kana'+index, { katakana: true });
            this.autokana ['mst_staff_dependents_first_nm'+index] = AutoKana.bind('#mst_staff_dependents_first_nm'+index, '#mst_staff_dependents_first_nm_kana'+index, { katakana: true });
        },
    },
    mounted () {
        var that=this;
        that.loadFormEdit();
        staffs_service.loadListReMunicipalOffice().then((response) => {
            that.dropdown_relocate_municipal_office_nm =  response.data;
        });
        this.autokana ['last_nm'] = AutoKana.bind('#last_nm', '#last_nm_kana', { katakana: true });
        this.autokana ['first_nm'] = AutoKana.bind('#first_nm', '#first_nm_kana', { katakana: true });
        this.showKana(this.index);
    },
    components: {
        DatePicker,
        PulseLoader,
        Dropdown
    }
});
