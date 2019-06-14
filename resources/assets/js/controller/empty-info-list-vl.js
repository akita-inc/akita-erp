import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'

var ctrEmptyInfoListVl = new Vue({
    el: '#ctrEmptyInfoListVl',
    data: {
        lang:lang_date_picker,
        format_date: format_date_picker,
        loading:false,
        items:{},
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
        order: {
            col:'',
            descFlg: true,
            divId:''
        },
        errors:[],
        auth_offfice_id:auth_offfice_id,
        changePage: function (page) {
            this.pagination.current_page = page;
            this.getItems(page);
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
            empty_info_service.loadList(data).then((response) => {
                if (response.data.data.length===0) {
                    that.message = messages["MSG05001"];
                } else {
                    that.message = '';
                }
                that.items = response.data;
                that.pagination = response.pagination;
                that.fileSearch = response.fieldSearch;
                that.order = response.order;
                $.each(that.fileSearch, function (key, value) {
                    if (value === null)
                        that.fileSearch[key] = '';
                });
                that.loading = false;
                that.auth_offfice_id=auth_offfice_id;
                if (that.order.col !== null)
                {
                    $('#'+ that.order.divId).addClass(that.order.descFlg ? 'sort-desc' : 'sort-asc');
                }
            });
        },
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
            this.fileSearch.regist_office_id="";
            this.fileSearch.vehicle_size="";
            this.fileSearch.vehicle_body_shape="";
            this.fileSearch.asking_baggage="";
            this.fileSearch.equipment="";
            this.fileSearch.start_pref_cd="";
            this.fileSearch.start_address="";
            this.fileSearch.arrive_pref_cd="";
            this.fileSearch.arrive_address="";
            this.fileSearch.status=false;
            this.fileSearch.arrive_date=false;
        },
        setDefault: function (){

        },
        handleLinkEmptyInfo:function (id,status,regist_office_id){
            if (status == 8 || status == 9) {
                window.location.href = 'reservation/' + id;
            }
            else if (this.auth_offfice_id != regist_office_id) {
                window.location.href = 'reservation/' + id;
            }
            else if (this.auth_offfice_id == regist_office_id && status == 1) {
                this.checkIsExist(id);
            }
            else if (this.auth_offfice_id == regist_office_id && status == 2) {
                window.location.href='reservation-approval/'+id;
            }
        },
        deleteSupplier: function (id){
            empty_info_service.checkIsExist(id).then((response) => {
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
            empty_info_service.checkIsExist(id).then((response) => {
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
