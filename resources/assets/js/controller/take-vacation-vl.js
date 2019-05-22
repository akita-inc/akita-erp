import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'

var ctrTakeVacationVl = new Vue({
    el: '#ctrTakeVacationVl',
    data: {
        loading:false,
        take_vacation_edit:0,
        take_vacation_id:null,
        field:{
            applicant_id:'',
            staff_nm:staff_nm,
            applicant_office_id	:mst_business_office_id,
            applicant_office_nm	:business_ofice_nm,
            approval_kb:'',
            half_day_kb	:'',
            start_date:'',
            end_date:'',
            days:'',
            times:'',
            reasons:'',
            mode:$('#mode').val(),
        },
        errors:{},

    },
    methods : {


    },
    mounted () {

    },
    components: {
        PulseLoader,
        DatePicker,
    }
});
