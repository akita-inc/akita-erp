import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
import DatePicker from 'vue2-datepicker'

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
            radio_reference_date : "1",
            reference_date: date_now,
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
        getItems: function(page){
            if (this.fieldSearch.radio_reference_date === '1' && this.fieldSearch.reference_date === '') {
                alert(messages["MSG02001"].replace(':attribute', '基準日'));
                $('#reference_date').focus();
                return;
            }

            var data = {
                pageSize:this.pageSize,
                page:page,
                fieldSearch:this.fieldSearch,
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
                that.loading = false;
            });
        },
        changePage: function (page) {
            this.pagination.current_page = page;
            this.getItems(page);
        }
    },
    methods : {
        clearCondition: function clearCondition() {
            this.fieldSearch.vehicles_cd = '';
            this.fieldSearch.door_number = '';
            this.fieldSearch.vehicles_kb = '';
            this.fieldSearch.registration_numbers = '';
            this.fieldSearch.mst_business_office_id = '';
            this.fieldSearch.radio_reference_date = '1';
            this.fieldSearch.reference_date = date_now;
        },
        setDefault: function (){
            if (this.fieldSearch.reference_date === '') {
                this.fieldSearch.reference_date = date_now;
            }
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
        this.getItems(1);
    },
    components: {
        PulseLoader,
        DatePicker,
    }
});
