import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
var ctrCustomersListVl = new Vue({
    el: '#ctrCustomersListVl',
    data: {
        loading:false,
        items:[],
        fileSearch:{
            mst_customers_cd:"",
            customer_nm:"",
            status:1,
            reference_date: date_now,
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
            this.loading = true;
            customers_service.loadList(data).then((response) => {
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
        },
    },
    methods : {
        clearCondition: function clearCondition() {
            this.fileSearch.mst_customers_cd = '';
            this.fileSearch.customer_nm = '';
            this.fileSearch.status = 1;
            this.fileSearch.reference_date = date_now;
            this.message = '';
            this.getItems(1);
        },
        setDefault: function (){
            if (this.fileSearch.reference_date === '') {
                this.fileSearch.reference_date = date_now;
            }
        },
        deleteSupplier: function (id){
            if (confirm(messages["MSG06001"])) {
                customers_service.deleteSupplier(id).then((response) => {
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