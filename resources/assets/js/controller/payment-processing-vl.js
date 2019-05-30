import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'
import { VueAutosuggest }  from "vue-autosuggest";
import moment from 'moment';

var ctrPaymentProcessingVl = new Vue({
    el: '#ctrPaymentProcessingVl',
    data: {
        lang:lang_date_picker,
        format_date: format_date_picker,
        loading:false,
        items:[],
        fileSearch:{
            customer_cd: "",
            customer_nm:"",
        },
        field:{
            dw_day : currentDate,
            invoice_balance_total: '',
            dw_classification: '',
            payment_amount:0,
            fee:0,
            discount:'',
            total_payment_amount:'',
            item_payment_total:'',
            note:'',
        },
        message: '',
        disableBtn: false,
        flagSearch: false,
        fileSearched:{
            customer_cd: "",
            customer_nm:"",
        },
        listCustomer:[],
        getItems: function(page,show_msg){
            if (show_msg !== true) {
                $('.alert').hide();
            }
            this.fileSearch.customer_cd = this.$refs.customer_cd.searchInput;
            this.fileSearch.customer_nm = this.$refs.customer_nm.searchInput;
            var data = {
                fieldSearch:this.fileSearch,
            };
            var that = this;
            this.loading = true;
            payment_processing_service.loadList(data).then((response) => {
                if(response.success == false){
                    that.errors = response.message;
                    that.loading = false;
                }else{
                    that.fileSearched= {
                        mst_business_office_id:"",
                        billing_year: '',
                        billing_month: '',
                        customer_cd: "",
                        customer_nm:"",
                        closed_date:"",
                        special_closing_date:"",
                        closed_date_input:"",
                    };
                    that.flagSearch = true;
                    that.errors = [];
                    that.listBillingHistoryHeaderID =[];
                    that.listBillingHistoryDetailID =[];
                    if (response.data.length===0) {
                        that.message = messages["MSG05001"];
                    } else {
                        that.message = '';
                    }
                    that.items = response.data;
                    that.fileSearch = response.fieldSearch;
                    that.fileSearched.mst_business_office_id=response.fieldSearch.mst_business_office_id;
                    that.fileSearched.billing_year=response.fieldSearch.billing_year;
                    that.fileSearched.billing_month=response.fieldSearch.billing_month;
                    that.fileSearched.customer_cd=response.fieldSearch.customer_cd;
                    that.fileSearched.customer_nm=response.fieldSearch.customer_nm;
                    that.fileSearched.closed_date=response.fieldSearch.closed_date;
                    that.fileSearched.special_closing_date=response.fieldSearch.special_closing_date;
                    that.fileSearched.closed_date_input=response.fieldSearch.closed_date_input;
                    $.each(that.fileSearch, function (key, value) {
                        if (value === null)
                            that.fileSearch[key] = '';
                    });
                    that.loading = false;
                }
            });
        },
    },
    methods: {
        handleChangeCustomerNm: function(){
            for (var i=0; i < this.listCustomer.length; i++) {
                if (this.listCustomer[i].mst_customers_cd === this.fileSearch.customer_nm){
                    this.fileSearch.customer_cd = this.listCustomer[i].mst_customers_cd;
                    return;
                }
            }
        },
        handleChangeCustomerCd: function(){
            if(this.fileSearch.customer_cd!=''){
                for (var i=0; i < this.listCustomer.length; i++) {
                    if (this.listCustomer[i].mst_customers_cd === this.fileSearch.customer_cd){
                        this.fileSearch.customer_nm= this.listCustomer[i].mst_customers_cd;
                        return;
                    }
                }
            }else{
                this.fileSearch.customer_nm = "";
            }
        },
        clearCondition: function clearCondition() {
            this.fileSearch.customer_cd="";
            this.fileSearch.customer_nm="";
            this.errors = [];
        },
        addComma: function (value) {
            return  value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

    },
    mounted () {
        var that = this;
        payment_processing_service.loadListCustomers().then((response) => {
            that.listCustomer =  response.data;
        });
    },
    components: {
        PulseLoader,
        DatePicker,
        VueAutosuggest,
    }
});