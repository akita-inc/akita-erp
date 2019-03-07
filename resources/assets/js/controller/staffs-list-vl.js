import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
var ctrStaffsListVl = new Vue({
    el: '#ctrStaffsListVl',
    data: {
        loading:false,
        items:[],
        fileSearch:{
            staffs_cd:"",
            staff_nm:"",
            status:0,
            reference_date:"",
            belong_company_id:"",
            business_office_id:"",
            employment_pattern_id:"",
            position_id:"",
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
            var date=$("#reference_date" ).datepicker({
                format: 'yyyy/mm/dd'}).val();
            this.fileSearch.reference_date=date;
            var data = {
                pageSize:this.pageSize,
                page:page,
                fieldSearch:this.fileSearch,
                order:this.order
            };
            var that = this;
            this.loading = true;
            staffs_service.loadList(data).then((response) => {
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
        clearCondition:function () {

        }
        //end action list
    },
    mounted () {
        this.getItems(1);
    },
    components: {
        PulseLoader
    }
});
