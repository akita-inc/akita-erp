import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import { Core } from '../package/yubinbango-core';
import DatePicker from 'vue2-datepicker';
import moment from "moment";
import historykana from "historykana";
import Dropdown from 'vue-simple-search-dropdown';

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
            dropdown_relocate_municipal_office_nm:"",
            educational_background:"",
            educational_background_dt:"",
            mst_staff_job_experiences:[{}],
            job_duties:"",
            staff_tenure_start_dt:"",
            staff_tenure_end_dt:"",
            // mst_staff_qualifications:[{}],
            // mst_staff_dependents:[{
            //      birthday:"",
            //      ast_nm:"",
            //      last_nm_kana:"",
            //      first_nm:"",
            //      first_nm_kana:"",
            //      sex_id:"",
            //      social_security_number:"",
            // }],
        },
        errors:{},
        dateFormat: {
            stringify: (date) => {
                return date ? moment(date).format('YYYY MM DD') : null
            },
            parse: (value) => {
                return value ? moment(value, 'YYYY MM DD').toDate() : null
            }
        }
    },
    methods : {
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
            console.log(event.target.value);
        },
        addRows: function (block) {
            this.field[block].push({});
        },
        convertKana: function (input , destination) {
            let that = this;
            this.history.push(input.target.value);
            console.log(input.target.value);
            this.furigana = historykana(this.history);
            home_service.convertKana({'data': this.furigana}).then(function (data) {
                that.field[destination] = data.info;
            });
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
        removeRows: function (block,index) {
            this.field[block].splice(index, 1);
        },
        backHistory: function () {
            staffs_service.backHistory().then(function () {
                window.location.href = listRoute;
            });
        }
    },
    mounted () {
        var that=this;
        staffs_service.loadListReMunicipalOffice().then((response) => {
            that.field.dropdown_relocate_municipal_office_nm =  response.data;
        });
    },
    components: {
        DatePicker,
        PulseLoader,
        Dropdown
    }
});
