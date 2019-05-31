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
        itemsDB:[],
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
        allSelected: false,
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
                    if (response.data.length===0) {
                        that.message = messages["MSG05001"];
                    } else {
                        that.message = '';
                    }
                    $.each(response.data, function (key, item) {
                        that.items[key] = [{
                            invoice_number:'',
                            mst_business_office_id:'',
                            office_nm:'',
                            publication_date:'',
                            total_fee:'',
                            consumption_tax:'',
                            tax_included_amount:'',
                            last_payment_amount:'',
                            fee:'',
                            discount:'',
                            total_dw_amount:'',
                            payment_remaining:'',
                        }];
                        that.items[key].invoice_number  = item.invoice_number;
                        that.items[key].mst_business_office_id  = item.mst_business_office_id;
                        that.items[key].office_nm  = item.office_nm;
                        that.items[key].publication_date  = item.publication_date;
                        that.items[key].total_fee  = item.total_fee;
                        that.items[key].consumption_tax  = item.consumption_tax;
                        that.items[key].tax_included_amount  = item.tax_included_amount;
                        that.items[key].last_payment_amount  = item.last_payment_amount;
                        that.items[key].fee  = item.fee;
                        that.items[key].discount  = item.discount;
                        that.items[key].total_dw_amount  = item.total_dw_amount;
                        that.items[key].payment_remaining  = item.payment_remaining;
                    });

                    that.itemsDB = response.data;
                    that.handlePaymentRemainingTotal();
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
        removeCommaByID: function (id) {
            if(this.field[id]!=null) {
                this.field[id] = this.field[id].toString().replace(/,/g, '').replace('¥', '');
            }
        },
        handleChecked: function(e){
            var that = this;
            that.changeDiscount();
            that.handlePayment();
            that.handleFee();
            that.handlePaymentRemaining();
        },
        selectAll: function() {
            var that = this;
            that.listCheckbox = [];
            if (!that.allSelected) {
                $.each(that.items, function (key, item) {
                    that.listCheckbox.push(key);
                });
            }
            that.changeDiscount();
            that.handlePayment();
            that.handleFee();
            that.handlePaymentRemaining();
        },
        handlePaymentRemainingTotal: function () {
            var that = this;
            that.field.invoice_balance_total = that.removeComma(that.field.invoice_balance_total);
            $.each(that.itemsDB, function (key, item) {
                that.field.invoice_balance_total += parseFloat(item.payment_remaining);
            });
            that.field.invoice_balance_total = that.addComma(that.field.invoice_balance_total);
        },
        handlePayment: function () {
            var that = this;
            that.field.payment_amount =  that.removeComma(that.field.payment_amount);
            var payment_amount =  that.removeComma(that.field.payment_amount);
            that.field.item_payment_total = 0;
            $.each(that.items, function (key, item) {
                item.total_dw_amount = that.addComma(0);
                if(that.listCheckbox.indexOf(key)!= -1){
                    if(payment_amount > 0){
                        var payment_remaining = that.removeComma(that.itemsDB[key].payment_remaining);
                        if(payment_amount <  payment_remaining){
                            item.total_dw_amount = payment_amount;
                            payment_amount = 0;
                        }else{
                            item.total_dw_amount = payment_remaining;
                            payment_amount = payment_amount - parseFloat(payment_remaining);
                        }
                    }else {
                        item.total_dw_amount = 0;
                    }
                    that.field.item_payment_total += item.total_dw_amount;
                }
            });
            that.field.payment_amount =  that.addComma(that.field.payment_amount);
            that.field.item_payment_total =  that.addComma(that.field.item_payment_total);
            that.handleToTalPayment();
            that.handlePaymentRemaining();
        },
        handleFee: function () {
            var that = this;
            $.each(that.items, function (key, item) {
                item.fee = that.addComma(0);
            });
            if(that.listCheckbox.length > 0){
                let min = Math.min.apply(Math,that.listCheckbox);
                that.items[min].fee = that.field.fee;
            }
            that.handleToTalPayment();
            that.handlePaymentRemaining();
        },
        handleToTalPayment: function () {
            var that = this;
            that.field.total_payment_amount = 0;
            let payment_amount =  that.removeComma(that.field.payment_amount);
            let fee =  that.removeComma(that.field.fee);
            let discount =  that.removeComma(that.field.discount);
            that.field.total_payment_amount = payment_amount +  fee+ discount;
            that.field.total_payment_amount = that.addComma(that.field.total_payment_amount);
        },
        handleDiscount: function () {

        },
        changeTotalDwAmount: function () {
            var that = this;
            that.handleItemPaymentTotal();
        },
        changeDiscount: function () {
            var that = this;
            that.field.discount = 0;
            $.each(that.items, function (key, item) {
                if(that.listCheckbox.indexOf(key)!= -1){
                    that.field.discount += parseFloat(item.discount);
                }
            });
            that.field.discount = that.addComma(that.field.discount);
            that.handleToTalPayment();
        },
        handleItemPaymentTotal: function () {
            var that = this;
            that.field.item_payment_total = 0;
            $.each(that.items, function (key, item) {
                if(that.listCheckbox.indexOf(key)!= -1){
                    that.field.item_payment_total += parseFloat(item.total_dw_amount);
                }
            });
            that.field.item_payment_total =  that.addComma(that.field.item_payment_total);
        },
        handlePaymentRemaining: function(){
            var that = this;
            $.each(that.items, function (key, item) {
                item.payment_remaining = that.itemsDB[key].payment_remaining;
                let tax_included_amount = parseFloat(that.itemsDB[key].tax_included_amount)
                if(that.listCheckbox.indexOf(key)!= -1){
                   item.payment_remaining = tax_included_amount -  parseFloat(item.last_payment_amount) -  parseFloat(item.total_dw_amount)-  parseFloat(item.fee) -  parseFloat(item.discount);
                }
            });
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