import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
import DatePicker from '../component/vue2-datepicker-master'

var ctrSuppliersListVl = new Vue({
    el: '#ctrSuppliersListVl',
    data: {
        lang:lang_date_picker,
        format_date: format_date_picker,
        loading:false,
        items:[],
        fieldSearch:{
            mst_suppliers_cd:"",
            supplier_nm:"",
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
        getItems: function(page, show_msg){
            if (show_msg !== true) {
                $('.alert').hide();
            }

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
            suppliers_service.loadList(data).then((response) => {
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
            this.fieldSearch.mst_suppliers_cd = '';
            this.fieldSearch.supplier_nm = '';
            this.fieldSearch.radio_reference_date = '1';
            this.fieldSearch.reference_date = date_now;
        },
        setDefault: function (){
            if (this.fieldSearch.reference_date === '') {
                this.fieldSearch.reference_date = date_now;
            }
        },
        deleteSupplier: function (id){
            suppliers_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.getItems(1);
                    return false;
                } else {
                    if (confirm(messages["MSG06001"])) {
                        suppliers_service.deleteSupplier(id).then((response) => {
                            this.getItems(1);
                        });
                    }
                }
            });
        },
        checkIsExist: function (id) {
            suppliers_service.checkIsExist(id).then((response) => {
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
