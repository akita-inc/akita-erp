import { Core } from '../package/yubinbango-core';
import DatePicker from 'vue2-datepicker';
import moment from "moment";
var ctrStaffsVl = new Vue({
    el: '#ctrStaffsVl',
    data: {
        adhibition_start_dt:$('#adhibition_start_dt').val(),
        business_start_dt:$('#business_start_dt').val(),
        lang:lang_date_picker,
        furigana: '',
        history: [],
        field:{
            adhibition_start_dt:"",
            adhibition_end_dt:"2999/12/31",
            last_nm:"",
            first_nm:"",
            last_nm_kana:"",
            first_nm_kana:"",
            zip_cd:"",
            prefectures_cd:"",
            address1:"",
            address2:"",
            address3:"",
            notes:"",
            enter_day:"",
            retire_day:"",
            educational_background:"",
            educational_background_dt:"",
            mst_staff_job_experiences:[{}],
            mst_staff_qualifications:[{}],
            mst_staff_dependents:[{}],
            mst_staff:[{}]
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
        addRows: function () {
            this.field.mst_staff_qualifications.push({});
        },
        convertKana: function (e , destination) {
            suppliers_service.convertKana({'data': e.target.value}).then(function (data) {
                $('#'+destination).val(data.info);
            });
        },
        getAddrFromZipCode: function() {
            var zip = $('#zip_cd').val();
            new Core(zip, function (addr) {
                $('#prefectures_cd').val(addr.region_id);// 都道府県ID
                $('#address1').val(addr.locality);// 市区町村
                $('#address2').val(addr.street);// 町域
            });
        },
        removeRows: function (index) {
            this.field.mst_staff_qualifications.splice(index, 1);
        }
    },
    mounted () {
    },
    components: {
        DatePicker
    }
});
