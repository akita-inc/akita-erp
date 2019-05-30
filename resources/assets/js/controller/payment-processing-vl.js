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
            dw_day : "",
            invoice_balance_total: '',
            dw_classification: '',
            payment_amount:'',
            fee:'',
            discount:'',
            total_payment_amount:'',
            item_payment_total:'',
            note:'',
        },
        message: '',
        errors:[],
        filteredCustomerCd: [],
        filteredCustomerNm: [],
        dropdown_customer_cd: [{
            data:[]
        }],
        dropdown_customer_nm: [{
            data:[]
        }],
        list_bundle_dt:[],
        modal:{
            invoice:{},
            sale_info:[],
        },
        disableBtn: false,
        flagSearch: false,
        fileSearched:{
            customer_cd: "",
            customer_nm:"",
        },
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
    computed: {
        inputPropsCd: function() {
            return {id:'autosuggest__input', onInputChange: this.onInputChangeCd ,initialValue: this.fileSearch.customer_cd,maxlength:5,class:'form-control input-cd' ,ref:"customer_cd"}
        },
        inputPropsNm: function() {
            return {id:'autosuggest__input', onInputChange: this.onInputChangeNm ,initialValue: this.fileSearch.customer_nm,maxlength:5,class:'form-control',ref:"customer_nm"}
        }
    },
    methods : {
        renderSuggestion(suggestion) {
            const customer = suggestion.item;
            return customer.mst_customers_cd+ ': '+ (customer.customer_nm != null?customer.customer_nm:'');

        },
        getSuggestionValueCd(suggestion) {
            this.$refs.customer_nm.searchInput = suggestion.item.customer_nm;
            return suggestion.item.mst_customers_cd;

        },
        getSuggestionValueNm(suggestion) {
            this.$refs.customer_cd.searchInput = suggestion.item.mst_customers_cd;
            return suggestion.item.customer_nm;
        },
        onInputChangeCd(text) {
            this.fileSearch.customer_cd = text;
            if (text === '' || text === undefined) {
                this.filteredCustomerCd = [];
                return;
            }
            /* Full control over filtering. Maybe fetch from API?! Up to you!!! */
            const filteredData = this.dropdown_customer_cd[0].data.filter(item => {
                return item.mst_customers_cd.toString().toLowerCase().indexOf(text.toLowerCase()) > -1;
            }).slice(0, this.limit);

            this.filteredCustomerCd = [{
                data: filteredData
            }];
        },
        onInputChangeNm(text) {
            this.fileSearch.customer_nm = text;
            if (text === '' || text === undefined) {
                this.filteredCustomerNm = [];
                return;
            }
            /* Full control over filtering. Maybe fetch from API?! Up to you!!! */
            const filteredData = this.dropdown_customer_nm[0].data.filter(item => {
                return item.customer_nm.toString().toLowerCase().indexOf(text.toLowerCase()) > -1;
            }).slice(0, this.limit);

            this.filteredCustomerNm = [{
                data: filteredData
            }];
        },
        onSelectedCd(option) {
            this.fileSearch.customer_cd = option.item.mst_customers_cd;
            this.fileSearch.customer_nm = option.item.customer_nm;
        },
        onSelectedNm(option) {
            this.fileSearch.customer_cd = option.item.mst_customers_cd;
            this.fileSearch.customer_nm = option.item.customer_nm;
        },
        clearCondition: function clearCondition() {
            this.$refs.customer_nm.searchInput = "";
            this.$refs.customer_cd.searchInput = "";
            this.fileSearch.customer_cd="";
            this.fileSearch.customer_nm="";
            this.errors = [];
            this.filteredCustomerCd = [];
            this.filteredCustomerNm = [];
        },
        openModal: function (item) {
            this.loading = true;
            this.modal.invoice = item;
            var that = this;
            payment_processing_service.getDetailsInvoice({'mst_customers_cd':item.customer_cd,'mst_business_office_id':item.mst_business_office_id,'fieldSearch': that.fileSearched}).then((response) => {
                if (response.info.length > 0) {
                    that.modal.sale_info = response.info;
                }
                $('#detailsModal').modal('show');
                that.loading = false;
            });
        },
        addComma: function (value) {
            return  value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

    },
    mounted () {
        var that = this;
        payment_processing_service.loadListCustomers().then((response) => {
            that.dropdown_customer_cd[0].data =  response.data;
            that.dropdown_customer_nm[0].data =  response.data;
        });
    },
    components: {
        PulseLoader,
        DatePicker,
        VueAutosuggest,
    }
});