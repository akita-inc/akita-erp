import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import { Core } from '../package/yubinbango-core';
import DatePicker from 'vue2-datepicker';
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
        field:{
            staff_cd:"",
            adhibition_start_dt:"",
            adhibition_end_dt:"2999/12/31",
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
            dropdown_relocate_municipal_office_nm:[],
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
            mst_role_id:'',
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
            }

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
        showError: function ( errors ){
            return errors.join("<br/>");
        },
        submit:function()
        {
            let that = this;
            that.loading = true;
            staffs_service.submit(this.field).then((response) => {
                if(response.success == false){
                    that.errors = response.message;
                }
                else
                {
                    that.errors = {};
                }
                that.loading = false;
            });
        },
        getDropdownValues:function()
        {

        },
        onChange:function(event)
        {
            this.field.relocation_municipal_office_cd=event.target.value;
            // this.handleSelect({id:event.target.value,name:event.target.selectedOptions[0].text});
            // this.handleSelect({id:event.target.value,name:'abc2'});
        },
        addRows: function (block) {
            let value;
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
                    break;
            }
            this.field[block].push(value);
        },
        convertKana: function (input , destination) {
            this.field[destination] = this.autokana[input.target.id].getFurigana();
        },
        onBlur: function(){
            this.history = [];
            this.furigana = '';
        },
        getAddrFromZipCode: function() {
            var that=this;
            var zip = that.field.zip_cd;
            new Core(zip, function (addr) {
                that.field.prefectures_cd=addr.region_id;// 都道府県ID
                that.field.address1=addr.locality;// 市区町村
                that.field.address2=addr.street;// 町域
            });
        },
        removeRows: function (index) {
            this.field.mst_staff_job_experiences.splice(index, 1);
        },
        backHistory: function () {
            staffs_service.backHistory().then(function () {
                window.location.href = listRoute;
            });
        },
        loadRoleConfig: function () {
            var that = this;
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
                                that.field.mst_staff_auths[2].accessible_kb = item.accessible_kb;
                        }

                    });
                }
                console.log(that.field.mst_staff_auths);

            });
        },
        handleSelect: function (selected) {
            if(typeof selected.id!="undefined"){
                this.field.relocation_municipal_office_cd = selected.id;
            }
        },
    },
    mounted () {
        var that=this;
        staffs_service.loadListReMunicipalOffice().then((response) => {
            that.field.dropdown_relocate_municipal_office_nm =  response.data;
        });

        this.autokana ['last_nm'] = AutoKana.bind('#last_nm', '#last_nm_kana', { katakana: true });
        this.autokana ['first_nm'] = AutoKana.bind('#first_nm', '#first_nm_kana', { katakana: true });
    },
    components: {
        DatePicker,
        PulseLoader,
        Dropdown
    }
});
