import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'

var ctrEmptyInfoListVl = new Vue({
    el: '#ctrEmptyInfoListVl',
    data: {
        lang:lang_date_picker,
        format_date: format_date_picker,
        loading:false,
        items:[],
        fileSearch:{
            regist_office_id:"",
            vehicle_size:"",
            vehicle_body_shape:"",
            asking_baggage: "",
            equipment:"",
            start_pref_cd:"",
            start_address:"",
            arrive_pref_cd:"",
            arrive_address:"",
            status:0,
            arrive_date:0,
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
        errors:[],
        getItems: function(page){
            if (this.fileSearch.status === 1 && this.fileSearch.reference_date === '') {
                alert(messages["MSG02001"].replace(':attribute', '基準日'));
                $('#reference_date').focus();
                return;
            }

            var data = {
                pageSize:this.pageSize,
                page:page,
                fieldSearch:this.fileSearch,
                order:this.order
            };
            var that = this;
            this.loading = false;
            empty_info_service.loadList(data).then((response) => {
                if (response.data.data.length===0) {
                    this.message = messages["MSG05001"];
                } else {
                    this.message = '';
                }

                that.items = response.data.data;
                console.log( that.items);
                that.pagination = response.pagination;
                that.fileSearch = response.fieldSearch;
                that.loading = false;
            });
        },
        changePage: function (page) {
            this.pagination.current_page = page;
            this.getItems(page);
        },
    },
    methods : {
        clearCondition: function clearCondition() {
            this.fileSearch.mst_customers_cd = '';
            this.fileSearch.customer_nm = '';
            this.fileSearch.status = 1;
            this.fileSearch.reference_date = date_now;
        },
        setDefault: function (){
            if (this.fileSearch.reference_date === '') {
                this.fileSearch.reference_date = date_now;
            }
        },
        deleteSupplier: function (id){
            customers_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.getItems(1);
                    return false;
                } else {
                    if (confirm(messages["MSG06001"])) {
                        customers_service.deleteCustomer(id).then((response) => {
                            this.getItems(1);
                        });
                    }
                }
            });
        },
        checkIsExist: function (id) {
            customers_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.getItems(1);
                } else {
                    window.location.href = 'edit/' + id;
                }
            });
        }
    },
    mounted () {
        this.getItems(1);
    },
    components: {
        PulseLoader,
        DatePicker,
    }
});
