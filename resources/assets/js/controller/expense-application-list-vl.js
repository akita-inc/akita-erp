import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'

var ctrExpenseApplicationVl = new Vue({
    el: '#ctrExpenseApplicationVl',
    data: {
        loading:false,
        items:[],
        fileSearch:{
            applicant_id:"",
            applicant_nm:"",
            client_company_name:"",
            applicant_office_id:"",
            show_status:false,
            show_deleted:false,
        },
        message: '',
        flagSearch:false,
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
            expense_application_service.loadList(data).then((response) => {
                if (response.data.data.length===0) {
                    this.message = messages["MSG05001"];
                } else {
                    this.message = '';
                }
                that.flagSearch=true;
                that.items = response.data.data;
                that.pagination = response.pagination;
                that.fileSearch = response.fieldSearch;
                that.order = response.order;
                $.each(that.fileSearch, function (key, value) {
                    if (value === null)
                        that.fileSearch[key] = '';
                });
                that.loading = false;
                if (that.order.col !== null)
                    $('#'+ that.order.divId).addClass(that.order.descFlg ? 'sort-desc' : 'sort-asc');
            });
        },
        checkIsExist: function (id) {
            expense_application_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.getItems(1);
                } else {
                    switch (response.mode) {
                        case 'edit':
                            window.location.href = 'edit/' + id;
                            break;
                        case 'approval':
                            window.location.href = 'approval/' + id;
                            break;
                        default:
                            window.location.href = 'reference/' + id;
                    }
                }
            });
        },
        changePage: function (page) {
            this.pagination.current_page = page;
            this.getItems(page);
        },
        sortList: function(event, order_by) {
            $('.table-green thead th').removeClass('sort-asc').removeClass('sort-desc');
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
        clearCondition: function clearCondition() {
            this.fileSearch.vacation_id="";
            this.fileSearch.applicant_nm="";
            this.fileSearch.show_approved=false;
            this.fileSearch.show_deleted=false;
            this.fileSearch.sales_office="";
            this.fileSearch.vacation_class="";
        },
        setBgColor:function(delete_at){
            console.log(delete_at);
            let bgColor="";
            if(delete_at !== null){
                bgColor="#DDDDDD";
            }
            else            {
                bgColor="rgb(255, 255, 255)";
            }
            return bgColor;
        },
    },
    mounted () {
        this.flagSearch=true;
        this.getItems(1, true);
    },
    components: {
        PulseLoader,
        //DatePicker,
    }
});
