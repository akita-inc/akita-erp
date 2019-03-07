import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
var ctrSuppliersListVl = new Vue({
    el: '#ctrSuppliersListVl',
    data: {
        loading:false,
        items:[],
        fieldSearch:{
            mst_suppliers_cd:"",
            supplier_nm:"",
            radio_reference_date : "0",
            reference_date:"",
        },
        message: '',
        pagination:{
            total: 0,
            per_page: 10,
            from: 1,
            to: 0,
            current_page: 1,
            last_page:0
        },
        getItems: function(page){
            if (this.fieldSearch.radio_reference_date === '1' && this.fieldSearch.reference_date === '') {
                alert('aggfgf');
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
                if (response.data.data.count()===0) {
                    this.message = 'ádfghjk';
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
            this.fieldSearch.radio_reference_date = '0';
            this.fieldSearch.reference_date = '';
            this.getItems(1);
        },
        setDefault: function (){
            if (this.fieldSearch.reference_date === '') {
                var now = new Date();
                this.fieldSearch.reference_date = now.getFullYear() + '/' + (now.getMonth() + 1) + '/' + now.getDay();
            }
        },
        deleteSupplier: function (id){
            suppliers_service.deleteSupplier(id).then((response) => {
                this.getItems(this.pagination.current_page);
            });
        }
    },
    mounted () {
        this.getItems(1);
    },
    components: {
        PulseLoader
    }
});
