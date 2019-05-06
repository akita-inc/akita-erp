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
            billing_year: currentYear,
            billing_month: currentMonth,
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
        date_of_issue:moment().format('YYYY/MM/DD') ,
        getItems: function(page,show_msg){
            if (show_msg !== true) {
                $('.alert').hide();
            }
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
                    that.errors = [];
                    if (response.data.length===0) {
                        that.message = messages["MSG05001"];
                    } else {
                        that.message = '';
                    }
                    that.items = response.data;
                    that.fileSearch = response.fieldSearch;
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
            return {id:'autosuggest__input', onInputChange: this.onInputChangeCd ,initialValue: this.fileSearch.customer_cd,maxlength:5,class:'form-control' ,ref:"customer_cd"}
        },
        inputPropsNm: function() {
            return {id:'autosuggest__input', onInputChange: this.onInputChangeNm ,initialValue: this.fileSearch.customer_nm,maxlength:5,class:'form-control',ref:"customer_nm"}
        }
    },
    methods : {
        renderSuggestion(suggestion) {
            const customer = suggestion.item;
            return customer.mst_customers_cd+ ': '+ customer.customer_nm;

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
            this.getListBundleDt();
        },
        onSelectedNm(option) {
            this.fileSearch.customer_cd = option.item.mst_customers_cd;
            this.fileSearch.customer_nm = option.item.customer_nm;
            this.getListBundleDt();
        },
        clearCondition: function clearCondition() {
            this.$refs.customer_nm.searchInput = "";
            this.$refs.customer_cd.searchInput = "";
            this.fileSearch.mst_business_office_id="";
            this.fileSearch.billing_year=currentYear;
            this.fileSearch.billing_month=currentMonth;
            this.fileSearch.customer_cd="";
            this.fileSearch.customer_nm="";
            this.fileSearch.closed_date="";
            this.fileSearch.special_closing_date="";
            this.fileSearch.closed_date_input="";
            this.errors = [];
        },
        checkIsExist: function (id) {
            empty_info_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.getItems(1);
                } else {
                    window.location.href = 'edit/' + id;
                }
            });
        },
        getListBundleDt: function () {
            var that = this;
            invoice_service.loadListBundleDt({customer_cd:that.fileSearch.customer_cd}).then((response) => {
                if (response.info.length>0) {
                    that.list_bundle_dt = response.info;
                }
            });
        },
        openModal: function (item) {
            this.loading = true;
            this.modal.invoice = item;
            var that = this;
            invoice_service.getDetailsInvoice({'mst_customers_cd':item.customer_cd,'mst_business_office_id':item.mst_business_office_id}).then((response) => {
                if (response.info.length > 0) {
                   that.modal.sale_info = response.info;
                }
                $('#detailsModal').modal('show');
                that.loading = false;
            });
        },
        createPDF: function () {
            var that = this;
            invoice_service.createPDF({}).then((response) => {
                this.downloadFile(response, 'csv');
            });
        },
        createCSV: function () {
            var that = this;
            invoice_service.createCSV({data:that.items}).then((response) => {
                this.downloadFile(response, 'csv');
            });
        },
        downloadFile(response, filename) {
            // It is necessary to create a new blob object with mime-type explicitly set
            // otherwise only Chrome works like it should
            var newBlob = new Blob([response.data], {type: 'application/octet-stream'})

            // IE doesn't allow using a blob object directly as link href
            // instead it is necessary to use msSaveOrOpenBlob
            if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                window.navigator.msSaveOrOpenBlob(newBlob)
                return
            }

            // For other browsers:
            // Create a link pointing to the ObjectURL containing the blob.
            const data = window.URL.createObjectURL(newBlob)
            var link = document.createElement('a')
            link.href = data
            link.download = filename + '.zip'
            link.click()
            setTimeout(function () {
                // For Firefox it is necessary to delay revoking the ObjectURL
                window.URL.revokeObjectURL(data)
            }, 100)
        },
    },
    mounted () {
        var that = this;
        this.getListBundleDt();
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