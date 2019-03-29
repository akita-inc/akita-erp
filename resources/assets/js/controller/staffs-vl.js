import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import { Core } from '../package/yubinbango-core';
import DatePicker from '../component/vue2-datepicker-master'
import moment from "moment";
import historykana from "historykana";
import Dropdown from 'vue-simple-search-dropdown';
import * as AutoKana from "vanilla-autokana";
import { VueAutosuggest }  from "vue-autosuggest";
var ctrStaffsVl = new Vue({
    el: '#ctrStaffsVl',
    data: {
        lang:lang_date_picker,
        furigana: '',
        history: [],
        loading:false,
        staff_edit:0,
        staff_id:null,
        roles_staff_screen:$("#roles_staff_screen").val(),
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
        selected_relocate_municipal_office_nm:"",
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
        selected: "",
        filteredOptions: [],
        dropdown_relocate_municipal_office_nm: [{
            data:[]
        }],
        limit: 10,

    },
    computed: {
        inputProps: function() {
            var cls_error = this.errors.relocation_municipal_office_cd != undefined ? 'form-control is-invalid':'';
            return {id:'autosuggest__input', onInputChange: this.onInputChange ,initialValue: this.field.relocation_municipal_office_cd,maxlength:6, class:cls_error}
        }
    },
    methods : {
        onInputChange(text) {
            this.field.relocation_municipal_office_cd = text;
            if (text === '' || text === undefined) {
                return;
            }
            /* Full control over filtering. Maybe fetch from API?! Up to you!!! */
            const filteredData = this.dropdown_relocate_municipal_office_nm[0].data.filter(item => {
                return item.toString().toLowerCase().indexOf(text.toLowerCase()) > -1;
            }).slice(0, this.limit);

            this.filteredOptions = [{
                data: filteredData
            }];
        },
        onSelected(option) {
            this.field.relocation_municipal_office_cd = option.item;
        },
        clone: function(){
            this.field["clone"] = true;
            let that=this;
            if (that.roles_staff_screen.indexOf(2)==-1)
            {
                that.field.educational_background="";
                that.field.educational_background_dt="";
            }
            if(that.roles_staff_screen.indexOf(3)==-1)
            {
                that.field.mst_staff_job_experiences=[];
            }
            if(that.roles_staff_screen.indexOf(4)==-1)
            {
                that.field.mst_staff_qualifications=[];
            }
            if(that.roles_staff_screen.indexOf(5)==-1)
            {
                that.field.mst_staff_dependents=[];
            }
            if(that.roles_staff_screen.indexOf(6)==-1)
            {
                that.field.drivers_license_number="";
                that.field.drivers_license_color_id="";
                that.field.drivers_license_issued_dt="";
                that.field.drivers_license_period_validity="";
                that.image_drivers_license_picture="";
                that.field.drivers_license_picture="";
                that.field.deleteFile = 'uncopy_case_register_history';
                that.field.drivers_license_divisions_1="";
                that.field.drivers_license_divisions_2="";
                that.field.drivers_license_divisions_3="";
                that.field.drivers_license_divisions_4="";
                that.field.drivers_license_divisions_5="";
                that.field.drivers_license_divisions_6="";
                that.field.drivers_license_divisions_7="";
                that.field.drivers_license_divisions_8="";
                that.field.drivers_license_divisions_9="";
                that.field.drivers_license_divisions_10="";
                that.field.drivers_license_divisions_11="";
                that.field.drivers_license_divisions_12="";
                that.field.drivers_license_divisions_13="";
                that.field.drivers_license_divisions_14="";
            }
            if(that.roles_staff_screen.indexOf(7)==-1)
            {
                that.field.retire_reasons="";
                that.field.retire_dt="";
                that.field.death_reasons="";
                that.field.death_dt="";
                that.field.belong_company_id="";
                that.field.occupation_id="";
                that.field.mst_business_office_id="";
                that.field.depertment_id="";
                that.field.driver_election_dt="";
                that.field.medical_checkup_interval_id="";
                that.field.employment_insurance_numbers="";
                that.field.health_insurance_numbers="";
                that.field.employees_pension_insurance_numbers="";
                that.field.workmens_compensation_insurance_fg="";
            }
            if(that.roles_staff_screen.indexOf(8)==-1)
            {
                that.field.mst_staff_auths={
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
                };
            }
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
                if(this.field["password"] != "********"){
                    this.field["is_change_password"] = true;
                }
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
                    that.backHistory();
                }
                this.field["clone"] = null;
                this.field["is_change_password"] = null;
                that.loading = false;
            });
        },
         loadFormEdit: async function () {
            let that = this;
            if($("#hd_staff_edit").val() == 1){
                this.loading = true;
                that.staff_edit = 1;
                that.staff_id = $("#hd_id").val();
                $.each(this.field,function (key,value) {
                    if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined && key != 'mst_staff_job_experiences' && key != 'mst_staff_dependents'){
                        if(key == "adhibition_start_dt" || key == "adhibition_end_dt"){
                            that.field[key + "_edit"] = $("#hd_"+key).val();
                        }
                        that.field.workmens_compensation_insurance_fg=that.field.workmens_compensation_insurance_fg==0?"":1;
                        that.image_drivers_license_picture = $("#hd_drivers_license_picture").val();
                        that.field[key] = $("#hd_"+key).val();
                        that.field.drivers_license_picture ='';
                    }
                });
                this.field["password"] = "********";
                await that.getMstCollapses();
            }
        },
        getMstCollapses:async function()
        {
            let that=this;
            await staffs_service.getListStaffJobEx(that.staff_id).then((response) => {
                if(response.data != null && response.data.length > 0){
                    that.field.mst_staff_job_experiences = response.data;
                }
            });
            await staffs_service.getListStaffQualifications(that.staff_id).then((response) => {
                if(response.data != null && response.data.length > 0){
                    that.field.mst_staff_qualifications = response.data;
                }
            });

            await staffs_service.getStaffDependents(that.staff_id).then((response) => {
                if(response.data != null && response.data.length > 0){
                    that.field.mst_staff_dependents = response.data;
                }
            });

            await staffs_service.getStaffAuths(that.staff_id).then((response) => {
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
                };
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
            setTimeout(function(){
                if(block=='mst_staff_dependents'){
                    that.showKana(that.index);
                }
            }, 100);


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
        convertKanaBlock:function(input,field, destination){
            var index = input.target.id.replace( /^\D+/g, '');
            let kana="";
            if(this.field.mst_staff_dependents[index][field] == ""){
                kana = "";
            }else{
                var furigana = this.autokana[input.target.id].getFurigana();
                var baseKana =  this.autokana[input.target.id].baseKana;
                kana = furigana=='' ? baseKana : furigana;
            }
            this.field.mst_staff_dependents[index][destination]=kana;

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
            this.index-=1;
            this.field[block].splice(index, 1);
        },
        backHistory: function () {
            if(this.staff_edit == 1) {
                staffs_service.backHistory().then(function () {
                    window.location.href = listRoute;
                });
            }else{
                window.location.href = listRoute;
            }
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
        deleteStaff: function(id){
            var that = this;
            staffs_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    that.backHistory();
                    return false;
                } else {
                    if(id==auth_staff_id)
                    {
                        alert(messages["MSG06005"]);
                        return;
                    }
                    if (confirm(messages["MSG06001"])) {
                        staffs_service.deleteStaffs(id).then((response) => {
                            window.location.href = listRoute;
                        });
                    }
                }
            });
        },
        showKana:function (index) {
            this.autokana ['mst_staff_dependents_last_nm'+index] = AutoKana.bind('#mst_staff_dependents_last_nm'+index, '#mst_staff_dependents_last_nm_kana'+index, { katakana: true });
            this.autokana ['mst_staff_dependents_first_nm'+index] = AutoKana.bind('#mst_staff_dependents_first_nm'+index, '#mst_staff_dependents_first_nm_kana'+index, { katakana: true });
        },
    },
    async beforeMount(){
        await this.loadFormEdit();
    },
    async mounted () {
        await this.loadFormEdit();
        var that=this;
        staffs_service.loadListReMunicipalOffice().then((response) => {
            that.dropdown_relocate_municipal_office_nm[0].data =  response.data;
        });
        if(info_basic_screen)
        {
            this.autokana ['last_nm'] = AutoKana.bind('#last_nm', '#last_nm_kana', { katakana: true });
            this.autokana ['first_nm'] = AutoKana.bind('#first_nm', '#first_nm_kana', { katakana: true });
        }
        if(dependents_screen)
        {
            this.field.mst_staff_dependents.forEach( function(value,key) {
                that.showKana(key);
                that.index = key;
            });
            if(this.staff_id==''){
                this.showKana(this.index);
            }
        }


    },
    components: {
        DatePicker,
        PulseLoader,
        Dropdown,
        VueAutosuggest
    }
});

