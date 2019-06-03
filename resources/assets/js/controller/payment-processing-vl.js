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
            dw_classification: defaultDwClassification,
            payment_amount:0,
            fee:0,
            total_discount:0,
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
        errorValidate:[],
        errorStr:"",
        listCheckbox: [],
        allSelected: false,
        data:[],
        registerSuccess:false,
    },
    methods: {
        getItems: async function(){
            this.errors = [];
            this.errorValidate = [];
            this.registerSuccess = false;
            var data = {
                fieldSearch:this.fileSearch,
            };
            var that = this;
            this.loading = true;
            that.field ={
                dw_day : currentDate,
                    invoice_balance_total: '',
                    dw_classification: defaultDwClassification,
                    payment_amount:that.addComma(0),
                    fee:that.addComma(0),
                    total_discount:that.addComma(0),
                    total_payment_amount:0,
                    item_payment_total:0,
                    note:'',
            };
            that.listCheckbox = [];
            that.allSelected =  false;
            await payment_processing_service.loadList(data).then((response) => {
                if(response.success == false){
                    that.errors = response.message;
                    that.loading = false;
                }else{
                    that.fileSearched= {
                        customer_cd: "",
                        customer_nm:"",
                    };
                    that.flagSearch = true;
                    that.errors = [];
                    if (response.data.length===0) {
                        that.message = messages["MSG05001"];
                    } else {
                        that.message = '';
                    }
                    $.each(response.data, function (key, item) {
                        that.items[key] = {
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
                        };
                        that.items[key].invoice_number  = item.invoice_number;
                        that.items[key].mst_business_office_id  = item.mst_business_office_id;
                        that.items[key].office_nm  = item.office_nm;
                        that.items[key].publication_date  = item.publication_date;
                        that.items[key].total_fee  = item.total_fee;
                        that.items[key].consumption_tax  = item.consumption_tax;
                        that.items[key].tax_included_amount  = item.tax_included_amount;
                        that.items[key].last_payment_amount  = item.last_payment_amount;
                        that.items[key].fee  = item.fee;
                        that.items[key].discount  = that.addComma(item.discount);
                        that.items[key].total_dw_amount  =that.addComma(item.total_dw_amount);
                        that.items[key].payment_remaining  = item.payment_remaining;
                    });

                    that.itemsDB = response.data;
                    if(that.items.length>0){
                        that.handlePaymentRemainingTotal();
                        that.selectAll();
                        that.allSelected =  true;
                    }
                    that.fileSearched.customer_cd=response.fieldSearch.customer_cd;
                    that.fileSearched.customer_nm=response.fieldSearch.customer_nm;
                    $.each(that.fileSearch, function (key, value) {
                        if (value === null)
                            that.fileSearch[key] = '';
                    });

                    that.loading = false;
                }
            });
            $.each(that.items, function (key, item) {
                if(document.getElementById('total_dw_amount'+key)!=null){
                    that.setInputFilter(document.getElementById('total_dw_amount'+key), function(value) {
                        return /^\d*\.?\d*$/.test(value); });
                }
                if(document.getElementById('discount'+key)!=null){
                    that.setInputFilter(document.getElementById('discount'+key), function(value) {
                        return /^\d*\.?\d*$/.test(value); });
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
            this.errorValidate = [];
            this.registerSuccess = false;
        },
        addComma: function (value) {
            if(value!=null){
                return  '¥'+value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }else{
                return 0;
            }
        },
        addCommaByID: function (id,key) {
            if(id=='total_dw_amount' || id=='discount'){
                if(this.items[key][id]!=null){
                    this.items[key][id] = '¥'+this.items[key][id].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            }else{
                if(this.field[id]!=null){
                    this.field[id] = '¥'+this.field[id].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            }
            this.$forceUpdate();
        },
        removeComma: function (value) {
            if(value!=null && value!= '') {
                return  parseFloat(value.toString().replace(/,/g, '').replace('¥',''));
            }else{
                return 0;
            }
        },
        removeCommaByID: function (id,key) {
            if(id=='total_dw_amount' || id=='discount'){
                if(this.items[key][id]!=null){
                    this.items[key][id] = parseFloat(this.items[key][id].toString().replace(/,/g, '').replace('¥',''));
                }
            }else{
                if(this.field[id]!=null){
                    this.field[id] = parseFloat(this.field[id].toString().replace(/,/g, '').replace('¥',''));
                }
            }
            this.$forceUpdate();
        },
        handleChecked: function(e){
            var that = this;
            that.handlePayment();
            that.handleFee();
            that.handleTotalDiscount();
            that.handlePaymentRemainingTotal();
        },
        selectAll: function() {
            var that = this;
            that.listCheckbox = [];
            if (!that.allSelected) {
                $.each(that.items, function (key, item) {
                    that.listCheckbox.push(key);
                });
            }
            that.handlePayment();
            that.handleFee();
            that.handleTotalDiscount();
            that.handlePaymentRemainingTotal();
        },
        handlePaymentRemainingTotal: function () {
            var that = this;
            that.field.invoice_balance_total = 0;
            $.each(that.itemsDB, function (key, item) {
                if (that.listCheckbox.indexOf(key) != -1) {
                    that.field.invoice_balance_total += parseFloat(item.payment_remaining);
                }
            });
            that.field.invoice_balance_total = that.addComma(that.field.invoice_balance_total);
        },
        handlePayment: function () {
            var that = this;
            if(!that.field.payment_amount){
                that.field.payment_amount = that.addComma(0);
            }
            if(that.listCheckbox.length > 0) {
                var payment_amount = that.removeComma(that.field.payment_amount);
                that.field.item_payment_total = 0;
                $.each(that.items, function (key, item) {
                    item.total_dw_amount = 0;
                    if (that.listCheckbox.indexOf(key) != -1) {
                        if (payment_amount > 0) {
                            var payment_remaining = that.removeComma(that.itemsDB[key].payment_remaining);
                            if (payment_amount < payment_remaining) {
                                item.total_dw_amount = payment_amount;
                                payment_amount = 0;
                            } else {
                                item.total_dw_amount = payment_remaining;
                                payment_amount = payment_amount - parseFloat(payment_remaining);
                            }
                        } else {
                            item.total_dw_amount = 0;
                        }
                        that.field.item_payment_total += item.total_dw_amount;
                        item.total_dw_amount = that.addComma(item.total_dw_amount);
                        that.handlePaymentRemaining(key);
                    }
                });
            }
            that.field.item_payment_total =  that.addComma(that.field.item_payment_total);
            that.handleToTalPayment();

        },
        handleFee: function () {
            var that = this;
            if(!that.field.fee){
                that.field.fee = that.addComma(0);
            }
            $.each(that.items, function (key, item) {
                item.fee = 0;
            });
            if(that.listCheckbox.length > 0){
                let min = Math.min.apply(Math,that.listCheckbox);
                that.items[min].fee = that.removeComma(that.field.fee);
                that.handlePaymentRemaining(min);
            }
            that.handleToTalPayment();
        },
        handleToTalPayment: function () {
            var that = this;
            that.field.total_payment_amount = 0;
            let payment_amount =  that.removeComma(that.field.payment_amount);
            let fee =  that.removeComma(that.field.fee);
            let discount =  that.removeComma(that.field.total_discount);
            that.field.total_payment_amount = payment_amount +  fee+ discount;
            that.field.total_payment_amount = that.addComma(that.field.total_payment_amount);
        },
        setInputFilter: function (textbox, inputFilter) {
            ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
                textbox.addEventListener(event, function() {
                    if (inputFilter(this.value)) {
                        this.oldValue = this.value;
                        this.oldSelectionStart = this.selectionStart;
                        this.oldSelectionEnd = this.selectionEnd;
                    } else if (this.hasOwnProperty("oldValue")) {
                        this.value = this.oldValue;
                        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                    }
                });
            });
        },
        changeTotalDwAmount: function (key) {
            var that = this;
            if(!that.items[key].total_dw_amount){
                that.items[key].total_dw_amount = 0;
                this.$forceUpdate();
            }
            that.handlePaymentRemaining(key);
            that.handleItemPaymentTotal();

        },
        changeDiscount: function (key) {
            var that = this;
            if(!that.items[key].discount){
                that.items[key].discount = 0;
                this.$forceUpdate();
            }
            that.handlePaymentRemaining(key);
            that.handleTotalDiscount();
        },
        handlePaymentRemaining: function(key){
            var that = this;
            that.items[key].payment_remaining = that.itemsDB[key].payment_remaining;
            let tax_included_amount = parseFloat(that.itemsDB[key].tax_included_amount);
            that.items[key].payment_remaining = tax_included_amount -  parseFloat(that.items[key].last_payment_amount) -  that.removeComma(that.items[key].total_dw_amount)-  that.removeComma(that.items[key].fee) -  that.removeComma(that.items[key].discount);
        },
        handleTotalDiscount: function () {
            var that = this;
            that.field.total_discount = 0;
            $.each(that.items, function (key, item) {
                if(that.listCheckbox.indexOf(key)!= -1){
                    that.field.total_discount += that.removeComma(item.discount);
                }
            });
            that.field.total_discount = that.addComma(that.field.total_discount);
            that.handleToTalPayment();
        },
        handleItemPaymentTotal: function () {
            var that = this;
            that.field.item_payment_total = 0;
            $.each(that.items, function (key, item) {
                if(that.listCheckbox.indexOf(key)!= -1){
                    that.field.item_payment_total += that.removeComma(item.total_dw_amount);
                }
            });
            that.field.item_payment_total = that.addComma(that.field.item_payment_total);
        },
        submit: function(){
            let that = this;
            that.loading = true;
            that.data = [];
            $.each(that.items, function (key, item) {
                if(that.listCheckbox.indexOf(key)!= -1){
                    let row = {
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
                    };
                    row.invoice_number  = item.invoice_number;
                    row.mst_business_office_id  = item.mst_business_office_id;
                    row.office_nm  = item.office_nm;
                    row.publication_date  = item.publication_date;
                    row.total_fee  = item.total_fee;
                    row.consumption_tax  = item.consumption_tax;
                    row.tax_included_amount  = item.tax_included_amount;
                    row.last_payment_amount  = item.last_payment_amount;
                    row.fee  = item.fee;
                    row.discount  = item.discount;
                    row.discount  = that.removeComma(row.discount);
                    row.total_dw_amount  = item.total_dw_amount;
                    row.total_dw_amount  = that.removeComma(row.total_dw_amount);
                    row.payment_remaining  = item.payment_remaining;
                    that.data.push(row);
                }
            });
            let dataPayment = {};
            dataPayment.dw_day = that.field.dw_day;
            dataPayment.invoice_balance_total= that.removeComma(that.field.invoice_balance_total);
            dataPayment.dw_classification= that.field.dw_classification;
            dataPayment.payment_amount= that.removeComma(that.field.payment_amount);
            dataPayment.fee= that.removeComma(that.field.fee);
            dataPayment.total_discount  = that.removeComma(that.field.total_discount);
            dataPayment.total_payment_amount  = that.removeComma(that.field.total_payment_amount);
            dataPayment.item_payment_total  = that.removeComma(that.field.item_payment_total);
            dataPayment.note= that.field.note;
            payment_processing_service.submit({
                dataPayment:dataPayment,
                listInvoice:that.data,
                dataSearch:that.fileSearched,
            }).then((response) => {
                if(response.success == false){
                    that.errors = response.error;
                    that.errorValidate = response.errorValidate;
                    that.showError();
                    that.showErrorValidate();
                }else{
                    that.errors = [];
                    that.errorValidate = [];
                    that.registerSuccess = true;
                }
                that.loading = false;
            });
        },
        showError: function ( ){
            var that = this;
            var errorStr = "";
            $.each(that.errors, function (key, item) {
                switch (key) {
                    case 'payment_amount':
                        errorStr+=item+"\n";
                        $('#payment_amount').focus();
                        break;
                    case 'total_dw_amount':
                        errorStr+=item.message.join("\n");
                        if(typeof that.errors['payment_amount'] == "undefined"){
                            $('#total_dw_amount'+item.indexError[0]).focus();
                            return false;
                        }

                }
            });
            if(errorStr!=""){
                alert(errorStr);
            }
        },
        showErrorValidate: function ( ){
            var that = this;
            $.each(that.errorValidate, function (key, item) {
                if(key =='listInvoice'){
                    $.each(item[0], function (key1, item1) {
                        that.errorStr +=item1.message+"<br/>";
                    });
                }
            });
        },
    },
    mounted () {
        var that = this;
        payment_processing_service.loadListCustomers().then((response) => {
            that.listCustomer =  response.data;
        });
        var listInputNumber = ['customer_cd','payment_amount','fee'];
        for (let item of listInputNumber){
            if(document.getElementById(item)!=null){
                this.setInputFilter(document.getElementById(item), function(value) {
                    return  /^\d*\.?\d*$/.test(value); });
            }
        }

    },
    components: {
        PulseLoader,
        DatePicker,
        VueAutosuggest,
    }
});