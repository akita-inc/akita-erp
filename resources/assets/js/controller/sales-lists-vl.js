import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master';
import { VueAutosuggest }  from "vue-autosuggest";
var ctrSalesListVl = new Vue({
    el: '#ctrSalesListVl',
    data: {
        loading:false,
        lang:lang_date_picker,
        format_date: format_date_picker,
        items:[],
        message:'',
        auth_staff_cd:'',
        filteredOptions: [],
        fileSearch:{
            daily_report_date:"",
            from_date:"",
            to_date:"",
            mst_business_office_id:"",
            invoicing_flag:"",
            customer_nm:"",
        },
        pagination:{
            total: 0,
            per_page: 2,
            from: 1,
            to: 0,
            current_page: 1,
            last_page:0
        },
        order: null,
        dropdown_mst_customer_cd: [{
            data:[]
        }],
        getItems: function(page, show_msg){
            if (show_msg !== true) {
                $('.alert').hide();
            }
            var data = {
                pageSize:this.pageSize,
                page:page,
                fieldSearch:this.fileSearch,
                order:this.order,
            };
            var that = this;
            that.loading = true;
            sales_lists_service.loadList(data).then((response) => {
                that.items = response.data.data;
                that.pagination = response.pagination;
                that.fileSearch = response.fieldSearch;
                that.order = response.order;
                $.each(that.fileSearch, function (key, value) {
                    if (value === null)
                        that.fileSearch[key] = '';
                });
                that.loading = false;
            });
        },
        changePage: function (page) {
            this.pagination.current_page = page;
            this.getItems(page);
        },
        checkIsExist: function (id) {
            sales_lists_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.getItems(1);
                } else {
                    window.location.href = 'edit/' + id;
                }
            });
        },
        sortList: function(event, order_by) {
            $('.search-content thead th').removeClass('sort-asc').removeClass('sort-desc');
            if (this.order.col === order_by && this.order.descFlg) {
                this.order.descFlg = false;
                event.target.classList.toggle('sort-asc');
            } else {
                this.order.descFlg = true;
                event.target.classList.toggle('sort-desc');
            }
            this.order.col = order_by;
            this.order.divId = event.currentTarget.id;
            this.getItems(this.pagination.current_page);
        }
    },
    computed:{
        inputProps: function() {
            var cls_error = this.fileSearch.mst_customers_cd != undefined ? 'form-control is-invalid':'';
            return {
                id: 'autosuggest__input',
                onInputChange: this.onInputChange,
                initialValue: this.fileSearch.mst_customers_cd,
                maxlength: 6,
                class: ''
            };
        },
    },
    methods : {
        onInputChange(text) {
            this.fileSearch.mst_customers_cd= text;
            if (text === '' || text === undefined) {
                return;
            }
            const filteredData = this.dropdown_mst_customer_cd[0].data.filter(item => {
                return item.toString().toLowerCase().indexOf(text.toLowerCase()) > -1;
            }).slice(0, this.limit);
            this.filteredOptions = [{
                data: filteredData
            }];
        },
        onSelected(option) {
            this.fileSearch.mst_customers_cd = option.item;
        },
        clearCondition:function () {
            this.fileSearch.daily_report_date = "";
            this.fileSearch.mst_business_office_id = "";
            this.fileSearch.from_date = "";
            this.fileSearch.to_date="";
            this.fileSearch.invoicing_flag="";
        },
        //end action list
    },
    mounted () {
        var type='cd';
        sales_lists_service.loadCustomerList(type).then((response) => {
            this.dropdown_mst_customer_cd[0].data =  response.data;
            console.log(response.data);
        });
        this.getItems(1, true);
        var from_date = new Date();
        from_date.setDate(1);
        this.fileSearch.from_date=from_date;
        var to_date = new Date();
        var lastDay = new Date(to_date.getFullYear(), to_date.getMonth()+1, 1);
        this.fileSearch.to_date=lastDay;
    },
    components: {
        PulseLoader,
        DatePicker,
        VueAutosuggest,
    }
});
