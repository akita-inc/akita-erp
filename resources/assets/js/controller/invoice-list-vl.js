import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'
import { VueAutosuggest }  from "vue-autosuggest";
import moment from 'moment';

var ctrInvoiceListVl = new Vue({
    el: '#ctrInvoiceListVl',
    data: {
        lang:lang_date_picker,
        format_date: format_date_picker,
        loading:false,
        items:[],
        fileSearch:{
            mst_business_office_id:"",
            billing_year: '',
            billing_month: '',
            customer_cd: "",
            customer_nm:"",
            closed_date:"",
            special_closing_date:"",
            closed_date_input:"",
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
            mst_business_office_id:"",
            billing_year: '',
            billing_month: '',
            customer_cd: "",
            customer_nm:"",
            closed_date:"",
            special_closing_date:"",
            closed_date_input:"",
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
            invoice_service.loadList(data).then((response) => {
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
                    that.disableBtn =  false;
                    that.errors = [];
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
                this.getListBundleDt();
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
            if(this.fileSearch.customer_cd === ''){
                this.getListBundleDt();
                this.fileSearch.closed_date = '';
            }else{
                this.getListBundleDt(true);
            }
        },
        onSelectedNm(option) {
            this.fileSearch.customer_cd = option.item.mst_customers_cd;
            this.fileSearch.customer_nm = option.item.customer_nm;
            if(this.fileSearch.customer_nm === ''){
                this.getListBundleDt();
                this.fileSearch.closed_date = '';
            }else{
                this.getListBundleDt(true);
            }
        },
        clearCondition: function clearCondition() {
            this.$refs.customer_nm.searchInput = "";
            this.$refs.customer_cd.searchInput = "";
            this.fileSearch.mst_business_office_id="";
            this.fileSearch.billing_year="";
            this.fileSearch.billing_month="";
            this.fileSearch.customer_cd="";
            this.fileSearch.customer_nm="";
            this.fileSearch.closed_date="";
            this.fileSearch.special_closing_date="";
            this.fileSearch.closed_date_input="";
            this.errors = [];
            this.filteredCustomerCd = [];
            this.filteredCustomerNm = [];
            this.getListBundleDt();
            this.getCurrentYearMonth();
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
            invoice_service.getDetailsInvoice({'mst_customers_cd':item.customer_cd,'mst_business_office_id':item.mst_business_office_id,'fieldSearch': that.fileSearched}).then((response) => {
                if (response.info.length > 0) {
                   that.modal.sale_info = response.info;
                }
                $('#detailsModal').modal('show');
                that.loading = false;
            });
        },
        createPDF: async function () {
            var that = this;
            this.loading = true;
            await that.items.forEach(  ( value,key) =>{
                setTimeout(function(){
                    invoice_service.createPDF({data:value,'fieldSearch': that.fileSearched,type:1,date_of_issue: that.date_of_issue}).then( async function (response){
                        await that.downloadFile(response);
                        var filename = response.headers['content-disposition'].split('=')[1].replace(/^\"+|\"+$/g, '');
                        invoice_service.createPDF({data:value,'fieldSearch': that.fileSearched,type:2,fileName:filename,date_of_issue: that.date_of_issue}).then(  function (response1){
                            that.downloadFile(response1);
                        });
                    });
                    return;
                }, key*1000);

            });
            this.disableBtn =  true;
            this.loading =  false;
        },
        createCSV: async function () {
            var that = this;
            this.loading = true;
            await that.items.forEach(  ( value,key) =>{
                setTimeout(function(){
                    invoice_service.createCSV({data:value,'fieldSearch': that.fileSearched,date_of_issue: that.date_of_issue}).then(  function (response){
                         that.downloadFile(response, 'csv');
                    });
                }, key*1000);

            });
            this.disableBtn =  true;
            this.loading = false;
        },
        downloadFile(response) {
            // It is necessary to create a new blob object with mime-type explicitly set
            // otherwise only Chrome works like it should
            var newBlob = new Blob([response.data], {type: response.headers["content-type"]})

            var filename = response.headers['content-disposition'].split('=')[1].replace(/^\"+|\"+$/g, '')

            // IE doesn't allow using a blob object directly as link href
            // instead it is necessary to use msSaveOrOpenBlob
            if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                window.navigator.msSaveOrOpenBlob(newBlob,filename)
                return
            }

            // For other browsers:
            // Create a link pointing to the ObjectURL containing the blob.
            const data = window.URL.createObjectURL(newBlob)
            var link = document.createElement('a')
            link.href = data
            link.download = filename;
            link.click()
            setTimeout(function () {
                // For Firefox it is necessary to delay revoking the ObjectURL
                window.URL.revokeObjectURL(data)
            }, 100)
        },
        addComma: function (value) {
           return  value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        getCurrentYearMonth: function(){
            var that = this;
            invoice_service.getCurrentYearMonth().then((response) => {
                that.fileSearch.billing_year = response.current_year;
                that.fileSearch.billing_month = response.current_month;
            });
        },
    },
    mounted () {
        var that = this;
        this.getListBundleDt();
        this.getCurrentYearMonth();
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