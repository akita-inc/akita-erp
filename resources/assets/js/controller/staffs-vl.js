import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import { Core } from '../package/yubinbango-core';
import DatePicker from 'vue2-datepicker';
import moment from "moment";
import historykana from "historykana";
var ctrStaffsVl = new Vue({
    el: '#ctrStaffsVl',
    data: {
        lang:lang_date_picker,
        furigana: '',
        history: [],
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
            enter_day:"",
            retire_day:"",
            insurer_number:"",
            basic_pension_number:"",
            person_insured_number:"",
            health_insurance_class:"",
            welfare_annuity_class:"",
            relocation_municipal_office_cd:"",
            educational_background:"",
            educational_background_dt:"",
            mst_staff_job_experiences:[{}],
            mst_staff_qualifications:[{}],
            mst_staff_dependents:[{}],
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
        addRows: function (block) {
            this.field[block].push({});
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
        }
    },
    mounted () {
    },
    components: {
        DatePicker
    }
});
