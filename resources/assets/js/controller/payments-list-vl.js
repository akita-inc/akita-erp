import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
// import DatePicker from "../component/vue2-datepicker-master/lib";
import {VueAutosuggest} from "vue-autosuggest"
import moment from 'moment';

var ctrPaymentsListVl = new Vue({
    el: '#ctrPaymentsListVl',
    data: {
        loading: false,
        items: [],
        message: '',
        fileSearch: {
            mst_business_office_id: '',
            billing_year:'',
            billing_month: '',
            supplier_cd: '',
            supplier_nm: '',
            closed_date: ''
        },
        pagination: {
            total: 0,
            per_page: 2,
            from: 1,
            to: 0,
            current_page: 1,
            last_page: 0
        },
        flagSearch: false,
        order: {
            col: '',
            descFlg: true,
            divId: ''
        },
        modal: {
            payment: {},
            detail_info: []
        },
        errors:[],
        filteredSupplierCd: [],
        filteredSupplierNm: [],
        dropdown_supplier_cd:[
            {
                data:[]
            }
        ],
        dropdown_supplier_nm:[
            {
                data:[]
            }
        ],
        list_bundle_dt:[],
        getItems: function (page, show_msg) {
            if (show_msg !== true) {
                $('.alert').hide();
            }
            var data = {
                pageSize: this.pageSize,
                page: page,
                fieldSearch: this.fileSearch,
                order: this.order,
            };
            var that = this;
            this.loading = true;
            payments_service.loadList(data).then((response) => {
                that.flagSearch = true;
                if(response.success == false){
                    that.errors = response.message;
                    that.loading = false;
                }
                else{
                    that.errors = [];
                    if (response.data.length === 0) {
                        that.message = messages["MSG05001"];
                    } else {
                        that.message = '';
                    }
                    that.items = response.data;
                    console.log(that.items);
                    //that.pagination = response.pagination;
                    that.fileSearch = response.fieldSearch;
                    //that.order = response.order;
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
            return {id:'autosuggest__input', onInputChange: this.onInputChangeCd ,initialValue: this.fileSearch.supplier_cd,class:'form-control input-cd' ,ref:"supplier_cd"}
        },
        inputPropsNm: function() {
            return {id:'autosuggest__input', onInputChange: this.onInputChangeNm ,initialValue: this.fileSearch.supplier_nm,class:'form-control',ref:"supplier_nm"}
        }
    },
    methods: {
        //suggestion
        renderSuggestion(suggestion) {
            const supplier = suggestion.item;
            return supplier.mst_suppliers_cd+ ': '+ (supplier.supplier_nm != null?supplier.supplier_nm:'');
        },
        getSuggestionValueCd(suggestion) {
            this.$refs.supplier_nm.searchInput = suggestion.item.supplier_nm;
            return suggestion.item.mst_suppliers_cd;

        },
        getSuggestionValueNm(suggestion) {
            this.$refs.supplier_cd.searchInput = suggestion.item.mst_suppliers_cd;
            return suggestion.item.supplier_nm;
        },
        onInputChangeCd(text) {
            this.fileSearch.supplier_cd = text;
            if (text === '' || text === undefined) {
                this.getListBundleDt();
                this.fileSearch.closed_date = '';
                this.filteredSupplierCd = [];
                return;
            }
            /* Full control over filtering. Maybe fetch from API?! Up to you!!! */
            const filteredData = this.dropdown_supplier_cd[0].data.filter(item => {
                return item.mst_suppliers_cd.toString().toLowerCase().indexOf(text.toLowerCase()) > -1;
            }).slice(0, this.limit);
            //
            console.log(filteredData);
            if(filteredData.length == 0){
                this.fileSearch.closed_date = '';
                this.list_bundle_dt = [];
            }
            this.filteredSupplierCd = [{
                data: filteredData
            }];
        },
        onInputChangeNm(text) {
            this.fileSearch.supplier_nm = text;
            if (text === '' || text === undefined) {
                /*this.getListBundleDt();
                this.fileSearch.closed_date = '';*/
                this.filteredSupplierNm = [];
                return;
            }
            /* Full control over filtering. Maybe fetch from API?! Up to you!!! */
            const filteredData = this.dropdown_supplier_nm[0].data.filter(item => {
                if(item.supplier_nm != null){
                    return item.supplier_nm.toString().toLowerCase().indexOf(text.toLowerCase()) > -1;
                }else{
                    return '';
                }

            }).slice(0, this.limit);

            this.filteredSupplierNm = [{
                data: filteredData
            }];
        },
        onSelectedCd(option) {
            this.fileSearch.supplier_cd = option.item.mst_suppliers_cd;
            this.fileSearch.supplier_nm = option.item.supplier_nm;
            if(this.fileSearch.supplier_cd === ''){
                this.getListBundleDt();
                this.fileSearch.closed_date = '';
            }else{
                this.getListBundleDtWithValueSelected();
            }
        },
        onSelectedNm(option) {
            this.fileSearch.supplier_cd = option.item.mst_suppliers_cd;
            this.fileSearch.supplier_nm = option.item.supplier_nm;
            if(this.fileSearch.supplier_nm === ''){
                this.getListBundleDt();
                this.fileSearch.closed_date = '';
            }else{
                this.getListBundleDtWithValueSelected();
            }
        },
        //
        clearCondition: function clearCondition() {
            this.$refs.supplier_nm.searchInput = "";
            this.$refs.supplier_cd.searchInput = "";
            this.fileSearch.mst_business_office_id = "";
            this.fileSearch.supplier_cd="";
            this.fileSearch.supplier_nm="";
            this.fileSearch.closed_date="";
            this.errors = [];
            //
            this.getListBundleDt();
            this.filteredSupplierCd = [];
            this.filteredSupplierNm = [];
            //
            this.getCurrentYearMonth();
        },
        getListBundleDt: function(){
            var that = this;
            payments_service.loadListBundleDt({
                supplier_cd:that.fileSearch.supplier_cd
            }).then((response) => {
                if (response.info.length > 0) {
                    that.list_bundle_dt = response.info;
                }
            });
        },
        getListBundleDtWithValueSelected: function(){
            var that = this;
            payments_service.loadListBundleDt({
                supplier_cd:that.fileSearch.supplier_cd
            }).then((response) => {
                if (response.info.length > 0) {
                    that.list_bundle_dt = response.info;
                    that.fileSearch.closed_date = that.list_bundle_dt[0].bundle_dt;
                }
            });
        },
        getCurrentYearMonth: function(){
            var that = this;
            payments_service.getCurrentYearMonth().then((response) => {
                that.fileSearch.billing_year = response.current_year;
                that.fileSearch.billing_month = response.current_month;
            });
        },
        openModal: function (item) {
            this.loading = true;
            this.modal.payment = item;
            var that = this;
            payments_service.getDetailsPayment({
                'mst_suppliers_cd': item.mst_suppliers_cd,
                'mst_business_office_id': item.mst_business_office_id,
                'daily_report_date': that.fileSearch.billing_year+'-'+that.fileSearch.billing_month+'-'+that.fileSearch.closed_date
            }).then((response) => {
                if (response.info.length > 0) {
                    that.modal.detail_info = response.info;
                }
                $('#detailsModal').modal('show');
                that.loading = false;
            });
        },
        execution: function(){
            var that = this;
            this.loading = true;
            this.flagSearch = false;
            payments_service.execution({data:that.items,daily_report_date:that.fileSearch.billing_year+'-'+that.fileSearch.billing_month+'-'+that.fileSearch.closed_date}).then((response) => {
                if(response.success === false){
                    that.errors = response.message;
                    that.loading = false;
                }else{
                    that.errors = [];
                    that.items = [];
                    that.message = messages["MSG10023"];
                    that.loading = false;
                }
            });
        }
        //end action list
    },
    mounted (){
        var that = this;
        this.getListBundleDt();
        this.getCurrentYearMonth();
        payments_service.loadListSuppliers().then((response) => {
            that.dropdown_supplier_cd[0].data = response.data;
            that.dropdown_supplier_nm[0].data = response.data;
        });
    },
    components: {
        PulseLoader,
    }
});