import DatePicker from 'vue2-datepicker'
import moment from 'moment'

var ctrCustomersVl = new Vue({
    el: '#ctrCustomersVl',
    data: {
        field:{
            adhibition_start_dt:"",
            adhibition_end_dt:"2999/12/31",
            customer_nm:"",
            customer_nm_kana:"",
            customer_nm_formal:"",
            person_in_charge_last_nm:"",
            person_in_charge_first_nm:"",
            person_in_charge_last_nm_kana:"",
            person_in_charge_first_nm_kana:"",
            zip_cd:"",
            prefectures_cd:"",
            address1:"",
            address2:"",
            address3:"",
            phone_number:"",
            fax_number:"",
            hp_url:"",
            customer_category_id:"",
            prime_business_office_id:"",
            explanations_bill:"",
            bundle_dt:"",
            deposit_month_id:"",
            deposit_day:"",
            deposit_method_id:"",
            deposit_method_notes:"",
            consumption_tax_calc_unit_id:"",
            rounding_method_id:"",
            discount_rate:"",
            except_g_drive_bill_fg:"",
            mst_bill_issue_destinations:[{
                mst_bill_issue_destinations:"",
                bill_address1:"",
            }]
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
        }
    },
    mounted () {
    },
    components: {
        DatePicker
    }
});
