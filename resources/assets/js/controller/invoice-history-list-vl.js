import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'
import { VueAutosuggest }  from "vue-autosuggest";
import moment from 'moment';

var ctrInvoiceHistoryListVl = new Vue({
    el: '#ctrInvoiceHistoryListVl',
    data: {
        lang:lang_date_picker,
        format_date: format_date_picker,
        loading:false,
        items:[],
        fileSearch:{
            mst_business_office_id:"",
            start_date:firstDayPreviousMonth,
            end_date:lastDayPreviousMonth,
            customer_cd: "",
            customer_nm:"",
            display_remaining_payment:0,
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
            start_date:firstDayPreviousMonth,
            end_date:lastDayPreviousMonth,
            customer_cd: "",
            customer_nm:"",
            display_remaining_payment:0,
        },
        listBillingHistoryHeaderID:[],
        listBillingHistoryDetailID:[],
        getItems: function(not_show_msg){
            this.fileSearch.customer_cd = this.$refs.customer_cd.searchInput;
            this.fileSearch.customer_nm = this.$refs.customer_nm.searchInput;
            var data = {
                fieldSearch:this.fileSearch,
            };
            var that = this;
            this.loading = true;
            if(typeof not_show_msg !='undefined' && not_show_msg){
                that.deleteFlagSuccess=false;
            }

            invoice_history_service.loadList(data).then((response) => {
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
                    that.fileSearched.display_remaining_payment=response.fieldSearch.display_remaining_payment;
                    that.fileSearched.start_date=response.fieldSearch.start_date;
                    that.fileSearched.end_date=response.fieldSearch.end_date;
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
        deleteFlagSuccess: false,
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
            if(this.fileSearch.customer_cd === ''){
                this.fileSearch.closed_date = '';
            }
        },
        onSelectedNm(option) {
            this.fileSearch.customer_cd = option.item.mst_customers_cd;
            this.fileSearch.customer_nm = option.item.customer_nm;
            if(this.fileSearch.customer_nm === ''){
                this.fileSearch.closed_date = '';
            }
        },
        clearCondition: async function() {
            this.$refs.customer_nm.searchInput = "";
            this.$refs.customer_cd.searchInput = "";
            this.fileSearch.mst_business_office_id="";
            this.fileSearch.start_date="";
            this.fileSearch.end_date="";
            this.fileSearch.customer_cd="";
            this.fileSearch.customer_nm="";
            this.fileSearch.display_remaining_payment=0;
            this.errors = [];
            this.filteredCustomerCd = [];
            this.filteredCustomerNm = [];
            this.getFirstLastDatePreviousMonth();
        },
        openModal: function (item) {
            this.loading = true;
            this.deleteFlagSuccess=false;
            this.modal.invoice = item;
            var that = this;
            invoice_history_service.getDetailsInvoice({'invoice_number':item.invoice_number}).then((response) => {
                if (response.info.length > 0) {
                   that.modal.sale_info = response.info;
                }
                $('#detailsModal').modal('show');
                that.loading = false;
            });
        },
        createPDF: async function () {
            this.deleteFlagSuccess=false;
            var that = this;
            this.loading = true;
            await that.items.forEach(  ( value,key) =>{
                setTimeout(function(){
                    invoice_history_service.createPDF({
                        data:value,
                        type:1
                    }).then( async function (response){
                        await that.downloadFile(response);
                        var filename = response.headers['content-disposition'].split('=')[1].replace(/^\"+|\"+$/g, '');
                        invoice_history_service.createPDF({
                            data:value,
                            type:2,
                            fileName:filename
                        }).then(  function (response1){
                            that.downloadFile(response1);
                        });

                    });
                }, key*1000);

            });
            this.loading =  false;
        },
        createCSV: async function () {
            this.deleteFlagSuccess=false;
            var that = this;
            this.loading = true;
            await that.items.forEach(  ( value,key) =>{
                setTimeout(function(){
                    invoice_history_service.createCSV(
                        {
                            data:value,
                            'fieldSearch': that.fileSearched,
                        }).then(  function (response){
                         that.downloadFile(response);
                    });
                }, key*1000);

            });
            this.loading = false;
        },
        createAmazonCSV: async function () {
            this.deleteFlagSuccess=false;
            var that = this;
            this.loading = true;
            await that.items.forEach(  ( value,key) =>{
                setTimeout(function(){
                    invoice_history_service.createAmazonCSV({
                        data:value,
                        'fieldSearch': that.fileSearched,
                    }).then(  function (response){
                        that.downloadFile(response);
                    });
                }, key*1000);

            });
            this.loading = false;
        },
        downloadFile(response) {
            // It is necessary to create a new blob object with mime-type explicitly set
            // otherwise only Chrome works like it should
            var newBlob = new Blob([response.data], {type: response.headers["content-type"]})

            var filename = response.headers['content-disposition'].split('=')[1].replace(/^\"+|\"+$/g, '');

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
        getFirstLastDatePreviousMonth: function(){
            var that = this;
            invoice_history_service.getFirstLastDatePreviousMonth().then((response) => {
                that.fileSearch.start_date = response.firstDayPreviousMonth;
                that.fileSearch.end_date = response.lastDayPreviousMonth;
            });
        },
        confirmDelete:function() {
            var that = this;
            that.loading=true;
            invoice_history_service.delete({
                invoice_number:that.modal.invoice.invoice_number,
                document_no:that.modal.sale_info[0].document_no,
                customer_cd:that.modal.invoice.customer_cd,
                branch_office_cd:that.modal.sale_info[0].branch_office_cd,
            }).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                } else {
                    that.loading=false;
                    that.deleteFlagSuccess=true;
                    this.clearCondition();
                    $(window).scrollTop(0);
                    this.getItems();
                }
            });
        },
        openModalDelete: function(){
            $('#confirmDeleteModal').modal('show');
        }
    },
    async mounted () {
        var that = this;
        await invoice_history_service.loadListCustomers().then((response) => {
            that.dropdown_customer_cd[0].data =  response.data;
            that.dropdown_customer_nm[0].data =  response.data;
        });
        that.getItems();
    },
    components: {
        PulseLoader,
        DatePicker,
        VueAutosuggest,
    }
});