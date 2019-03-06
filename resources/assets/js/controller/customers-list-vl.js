import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
var ctrCustomersListVl = new Vue({
    el: '#ctrCustomersListVl',
    data: {
        loading:false,
        items:[],
        fileSearch:{
            mst_customers_cd:""
        },
        pagination:{
            total: 0,
            per_page: 2,
            from: 1,
            to: 0,
            current_page: 1,
            last_page:0
        },
        getItems: function(page){
            var data = {
                pageSize:this.pageSize,
                page:page,
                fieldSearch:this.fileSearch,
                order:this.order
            };
            var that = this;
            this.loading = true;
            customers_service.loadList(data).then((response) => {
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
        //end action list
    },
    mounted () {
        this.getItems(1);
    },
    components: {
        PulseLoader
    }
});
