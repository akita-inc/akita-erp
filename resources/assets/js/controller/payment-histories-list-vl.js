import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'
import { VueAutosuggest }  from "vue-autosuggest";
import moment from 'moment';

var ctrPaymentHistoryListVl = new Vue({
    el: '#ctrPaymentHistoryListVl',
    data: {
        lang:lang_date_picker,
        format_date: format_date_picker,
        loading:false,
        items:[],
        fileSearch:{
            customer_cd: "",
            customer_nm:"",
            from_date:"",
            to_date:"",
        },
        message: '',
        pagination:{
            total: 0,
            per_page: 2,
            from: 1,
            to: 0,
            current_page: 1,
            last_page:0
        },
        order: {
            col:'',
            descFlg: true,
            divId:''
        },
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
        date_of_issue:moment().format('YYYY/MM/DD') ,
        fileSearched:{
            customer_cd: "",
            customer_nm:"",
            from_date:"",
            to_date:"",

        },
        listBillingHistoryHeaderID:[],
        listBillingHistoryDetailID:[],
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
            payment_histories_service.loadList(data).then((response) => {
                if(response.success == false){
                    that.errors = response.message;
                    that.loading = false;
                }else{
                    that.fileSearched= {
                        customer_cd: "",
                        customer_nm:"",
                        from_date:"",
                        to_date:"",
                    };
                    that.flagSearch = true;
                    that.errors = [];
                    that.items = response.data;
                    that.fileSearch = response.fieldSearch;
                    that.fileSearched.customer_cd=response.fieldSearch.customer_cd;
                    that.fileSearched.customer_nm=response.fieldSearch.customer_nm;
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
        getListBundleDt: function (flagSelect) {
            var that = this;
            invoice_service.loadListBundleDt({customer_cd:that.fileSearch.customer_cd}).then((response) => {
                if (response.info.length>0) {
                    that.list_bundle_dt = response.info;
                    if(flagSelect){
                        that.fileSearch.closed_date = that.list_bundle_dt[0].bundle_dt;
                    }
                }
            });
        },
        openModal: function (item) {
            this.loading = true;
            this.modal.invoice = item;
            var that = this;
            payment_histories_service.getDetailsInvoice({'mst_customers_cd':item.customer_cd,'mst_business_office_id':item.mst_business_office_id,'fieldSearch': that.fileSearched}).then((response) => {
                if (response.info.length > 0) {
                    that.modal.sale_info = response.info;
                }
                $('#detailsModal').modal('show');
                that.loading = false;
            });
        }
    },
    mounted () {
        var that = this;
        invoice_service.loadListCustomers().then((response) => {
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