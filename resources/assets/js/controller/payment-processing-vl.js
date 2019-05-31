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
            discount:0,
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
        errors:[],
        listCheckbox: [],
    },
    methods: {
        getItems: function(){
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
                    that.handleCaculator();
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
        clearCondition: function(){
            this.fileSearch.customer_cd="";
            this.fileSearch.customer_nm="";
            this.errors = [];
        },
        addComma: function (value) {
            if(value!=null  && value!= ''){
                return  '¥'+value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }else{
                return 0;
            }
        },
        removeComma: function (value) {
            if(value!=null && value!= '') {
                return  parseFloat(value.toString().replace(/,/g, '').replace('¥', ''));
            }else{
                return 0;
            }
        },
        handleChecked: function(){
            var that = this;
            that.changeFee();
        },
        handleCaculator: function () {
            var that = this;
            that.field.invoice_balance_total = that.removeComma(that.field.invoice_balance_total);
            $.each(that.items, function (key, item) {
                that.field.invoice_balance_total += parseFloat(item.payment_remaining);
            });
            that.field.invoice_balance_total = that.addComma(that.field.invoice_balance_total);
        },
        handlePayment: function () {
            var that = this;
            var payment_amount =  that.removeComma(that.field.payment_amount);
            $.each(that.items, function (key, item) {
                if(that.listCheckbox.indexOf(key)!= -1){
                    if(payment_amount > 0){
                        var payment_remaining = that.addComma(item.payment_remaining);
                        item.payment_remaining = parseFloat(item.payment_remaining);
                        if(payment_amount <=  item.payment_remaining){
                            item.total_dw_amount = payment_amount;
                            payment_amount = 0;
                        }else{
                            item.total_dw_amount = item.payment_remaining;
                            payment_amount = payment_amount - parseFloat(item.payment_remaining);
                        }

                    }else {
                        item.total_dw_amount = 0;
                    }
                }
            });
        },
        handleFee: function () {
            var that = this;
            if(that.listCheckbox.length > 0){
                let min = Math.min.apply(Math,that.listCheckbox);
                that.items[min].fee = that.field.fee;
                that.handleToTalPayment();
            }
        },
        handleToTalPayment: function () {
            var that = this;
            that.field.total_payment_amount = that.field.payment_amount +  that.field.fee+ that.field.discount;
        },
        handleDiscount: function () {

        },
        changeTotalDwAmount: function () {

        },
        changeFee: function () {
            var that = this;
            that.field.discount = 0;
            $.each(that.items, function (key, item) {
                if(that.listCheckbox.indexOf(key)!= -1){
                    that.field.discount += parseFloat(item.discount);
                }
            });
            that.field.discount = that.addComma(that.field.discount);
        }
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