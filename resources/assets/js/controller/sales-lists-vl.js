import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master';
var ctrSalesListVl = new Vue({
    el: '#ctrSalesListVl',
    data: {
        loading:false,
        lang:lang_date_picker,
        format_date: format_date_picker,
        items:[],
        message:'',
        auth_staff_cd:'',
        fileSearch:{
            daily_report_date:"",
            position_id:"",
            staff_nm:"",
            date_nm:"",
            belong_company_id:"",
            mst_business_office_id:"",
            employment_pattern_id:"",
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
            this.loading = true;
            sales_lists_service.loadList(data).then((response) => {
                that.items = response.data.data;
                that.pagination = response.pagination;
                that.fileSearch = response.fieldSearch;
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
    methods : {
        clearCondition:function () {
            this.fileSearch.staff_cd = "";
            this.fileSearch.staff_nm = "";
            this.fileSearch.position_id="";
            this.fileSearch.date_nm="";
            this.fileSearch.belong_company_id="";
            this.fileSearch.mst_business_office_id="";
            this.fileSearch.employment_pattern_id="";
        }
        //end action list
    },
    mounted () {
        this.getItems(1, true);
    },
    components: {
        PulseLoader,
        DatePicker
    }
});
