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
            status:false,
            arrive_date:false,
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
        auth_offfice_id:auth_offfice_id,
        getItems: function(page,show_msg){
            if (show_msg !== true) {
                $('.alert').hide();
            }
            var data = {
                pageSize:this.pageSize,
                page:page,
                fieldSearch:this.fileSearch,
                order:this.order
            };
            var that = this;
            this.loading = true;
            console.log(this.fileSearch.status);
            empty_info_service.loadList(data).then((response) => {
                if (response.data.data.length===0) {
                    that.message = messages["MSG05001"];
                } else {
                    that.message = '';
                }
                that.items = response.data.data;
                that.pagination = response.pagination;
                that.fileSearch = response.fieldSearch;
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
    },
    methods : {
        setBgColor:function(status){
            let bgColor="";
            if(status==2)
            {
                bgColor="#FFFED8";
            }
            else if(status==8 || status==9)
            {
                bgColor="#DDDDDD";
            }
            else
            {
                bgColor="rgb(255, 255, 255)";
            }
            return bgColor;
        },
        clearCondition: function clearCondition() {

        },
        setDefault: function (){

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
        this.getItems(1, true);
    },
    components: {
        PulseLoader,
        DatePicker,
    }
});
