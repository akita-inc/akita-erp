import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
import DatePicker from '../component/vue2-datepicker-master'

var ctrVehiclesListVl = new Vue({
    el: '#ctrVehiclesListVl',
    data: {
        lang:lang_date_picker,
        format_date: format_date_picker,
        loading:false,
        items:[],
        fieldSearch:{
            vehicles_cd:"",
            door_number:"",
            vehicles_kb: "",
            registration_numbers: "",
            mst_business_office_id: "",
        },
        message: '',
        pagination:{
            total: 0,
            per_page: 0,
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
                fieldSearch:this.fieldSearch,
                order:this.order,
            };

            var that = this;
            this.loading = true;
            vehicles_service.loadList(data).then((response) => {
                if (response.data.data.length===0) {
                    this.message = messages["MSG05001"];
                } else {
                    this.message = '';
                }

                that.items = response.data.data;
                that.pagination = response.pagination;
                that.fieldSearch = response.fieldSearch;
                that.order = response.order;
                $.each(that.fieldSearch, function (key, value) {
                    if (value === null)
                        that.fieldSearch[key] = '';
                });
                that.loading = false;
                if (that.order.col !== null)
                    $('#'+ that.order.divId).addClass(that.order.descFlg ? 'sort-desc' : 'sort-asc');
            });
        },
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
        clearCondition: function clearCondition() {
            this.fieldSearch.vehicles_cd = '';
            this.fieldSearch.door_number = '';
            this.fieldSearch.vehicles_kb = '';
            this.fieldSearch.registration_numbers = '';
            this.fieldSearch.mst_business_office_id = '';
        },
        deleteVehicle: function (id){
            vehicles_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.getItems(1);
                    return false;
                } else {
                    if (confirm(messages["MSG06001"])) {
                        vehicles_service.delete(id).then((response) => {
                            this.getItems(1);
                        });
                    }
                }
            });
        },
        checkIsExist: function (id) {
            vehicles_service.checkIsExist(id).then((response) => {
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
