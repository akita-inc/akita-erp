import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
var ctrSuppliersListVl = new Vue({
    el: '#ctrSuppliersListVl',
    data: {
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
            suppliers_service.loadList(data).then((response) => {
                if (response.data.data.length===0) {
                    this.message = messages["MSG05001"];
                } else {
                    this.message = '';
                }

                that.items = response.data.data;
                that.pagination = response.pagination;
                that.loading = false;
            });
        },
        changePage: function (page) {
            this.pagination.current_page = page;
            this.getItems(page);
        }
    },
    methods : {
        gotoCreate: function gotoCreate() {
            var data = {
                page:this.pagination.current_page,
                fieldSearch:this.fieldSearch,
            };
            return suppliers_service.gotoCreate(data);
        },
        clearCondition: function clearCondition() {
            this.fieldSearch.mst_suppliers_cd = '';
            this.fieldSearch.supplier_nm = '';
            this.fieldSearch.radio_reference_date = '1';
            this.fieldSearch.reference_date = date_now;
            this.message = '';
            this.getItems(1);
        },
        setDefault: function (){
            if (this.fieldSearch.reference_date === '') {
                this.fieldSearch.reference_date = date_now;
            }
        },
        deleteSupplier: function (id){
            if (confirm(messages["MSG06001"])) {
                suppliers_service.deleteSupplier(id).then((response) => {
                    this.getItems(1);
                });
            }
        }
    },
    mounted () {
        this.getItems(1);
    },
    components: {
        PulseLoader
    }
});
