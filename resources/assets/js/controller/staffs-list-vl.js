import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from 'vue2-datepicker';
var ctrStaffsListVl = new Vue({
    el: '#ctrStaffsListVl',
    data: {
        loading:false,
        lang:lang_date_picker,
        format_date: format_date_picker,
        items:[],
        message:'',
        auth_staff_cd:'',
        fileSearch:{
            staff_cd:"",
            position_id:"",
            staff_nm:"",
            date_nm:"",
            status:1,
            belong_company_id:"",
            mst_business_office_id:"",
            employment_pattern_id:"",
            reference_date:date_now,
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
            staffs_service.loadList(data).then((response) => {
                if (response.data.data.length===0) {
                    that.message = messages["MSG05001"];
                } else {
                    that.message = '';
                }
                that.items = response.data.data;
                that.pagination = response.pagination;
                that.fileSearch = response.fieldSearch;
                that.loading = false;
                that.auth_staff_cd=auth_staff_cd;
            });
        },
        changePage: function (page) {
            this.pagination.current_page = page;
            this.getItems(page);
        },
        deleteStaffs: function (id){
            staffs_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.getItems(1);
                } else {
                    if (confirm(messages["MSG06001"])) {
                        staffs_service.deleteStaffs(id).then((response) => {
                            this.getItems(1);
                        });
                    }
                }
            });
        },
        checkIsExist: function (id) {
            staffs_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.getItems(1);
                } else {
                    window.location.href = 'edit/' + id;
                }
            });
        }
    },
    methods : {
        clearCondition:function () {
            this.fileSearch.staff_cd = "";
            this.fileSearch.staff_nm = "";
            this.fileSearch.status = 1;
            this.fileSearch.reference_date = date_now;
            this.fileSearch.position_id="";
            this.fileSearch.date_nm="";
            this.fileSearch.belong_company_id="";
            this.fileSearch.mst_business_office_id="";
            this.fileSearch.employment_pattern_id="";
            this.fileSearch.reference_date=date_now;
        },
        setDefault:function () {
            if (this.fileSearch.reference_date === '') {
                this.fileSearch.reference_date = date_now;
            }
        }
        //end action list
    },
    mounted () {
        this.getItems(1);
    },
    components: {
        PulseLoader,
        DatePicker
    }
});
