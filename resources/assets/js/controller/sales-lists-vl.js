import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master';
import { VueAutosuggest }  from "vue-autosuggest";
import encoding from 'encoding-japanese';
var ctrSalesListVl = new Vue({
    el: '#ctrSalesListVl',
    data: {
        loading:false,
        lang:lang_date_picker,
        format_date: format_date_picker,
        items:[],
        allItems:[],
        export_file_nm:"",
        message:'',
        auth_staff_cd:'',
        filteredCustomerCd: [],
        filteredCustomerNm:[],
        fileSearch:{
            daily_report_date:"",
            from_date:"",
            to_date:"",
            mst_business_office_id:"",
            invoicing_flag:"",
            mst_customers_cd:"",
            mst_customers_nm:"",
        },
        errors:[],
        pagination:{
            total: 0,
            per_page: 2,
            from: 1,
            to: 0,
            current_page: 1,
            last_page:0
        },
        flagSearch:false,
        flagExport:false,
        order: null,
        dropdown_mst_customer_cd: [{
            data:[]
        }],
        dropdown_mst_customer_nm:[{
            data:[]
        }],
        getItems: function(page, show_msg){
            if (show_msg !== true) {
                $('.alert').hide();
            }
            var that = this;
            that.loading = true;
            if( that.fileSearch.from_date=="")
            {
                that.errors["from_date"]=messages["MSG02001"].split(':attribute').join('期間');
                that.loading = false;
            }
            else
            {
                delete that.errors["from_date"];
            }
            if( that.fileSearch.to_date=="")
            {
                that.errors["to_date"]=messages["MSG02001"].split(':attribute').join('期間');
                that.loading = false;
            }
            else
            {
                delete that.errors["to_date"];
            }
            that.fileSearch.mst_customers_cd=this.$refs.mst_customers_cd.searchInput;
            that.fileSearch.mst_customers_nm=this.$refs.mst_customers_nm.searchInput;
            that.flagSearch=false;
            that.flagExport=false;
            var data = {
                pageSize: that.pageSize,
                page:page,
                fieldSearch: that.fileSearch,
                order: that.order,
            };
            if(that.fileSearch.from_date!="" && that.fileSearch.to_date!="" && that.$refs.mst_customers_cd.searchInput!="" && that.fileSearch.invoicing_flag=="0" && that.fileSearch.mst_business_office_id)
            {
                that.flagExport=true;
            }
            if(that.errors.from_date===undefined && that.errors.to_date===undefined)
            {
                that.loading = true;
                sales_lists_service.loadList(data).then((response) => {
                    that.items = response.data.data;
                    that.pagination = response.pagination;
                    that.fileSearch = response.fieldSearch;
                    that.order = response.order;
                    that.allItems=response.allItems;
                    that.export_file_nm=response.export_file_nm;
                    that.flagSearch=true;
                    $.each(that.fileSearch, function (key, value) {
                        if (value === null)
                            that.fileSearch[key] = '';
                    });
                    that.loading = false;
                });
            }
        },
        changePage: function (page) {
            this.pagination.current_page = page;
            this.getItems(page);
        },
        checkIsExist: function (id) {
            sales_lists_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    this.getItems(1);
                } else {
                    window.location.href = 'edit/' + id;
                }
            });
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
    computed:{
        inputPropsCd: function() {
            var cls_error = this.fileSearch.mst_customers_cd != undefined ? 'form-control is-invalid':'';
            return {
                id: 'autosuggest__input',
                onInputChange: this.onInputChangeCd,
                initialValue: this.fileSearch.mst_customers_cd,
                maxlength: 6,
                class: 'form-control',
                ref:"mst_customers_cd"
            };
        },
        inputPropsName:function () {
            var cls_error = this.fileSearch.mst_customers_nm != undefined ? 'form-control is-invalid':'';
            return {
                id: 'autosuggest__input',
                onInputChange: this.onInputChangeName,
                initialValue: this.fileSearch.mst_customers_nm,
                maxlength: 50,
                class: 'form-control w-100',
                ref:"mst_customers_nm"
            };
        }
    },
    methods : {
        renderSuggestion(suggestion) {
            const customer = suggestion.item;
            return customer.mst_customers_cd+ ': '+ customer.mst_customers_nm;

        },
        getSuggestionValueCd(suggestion) {
            this.$refs.mst_customers_nm.searchInput = suggestion.item.mst_customers_nm;
            return suggestion.item.mst_customers_cd;

        },
        getSuggestionValueName(suggestion) {
            this.$refs.mst_customers_cd.searchInput = suggestion.item.mst_customers_cd;
            return suggestion.item.mst_customers_nm;
        },
        onInputChangeCd(text) {
            this.fileSearch.mst_customers_cd= text;
            if (text === '' || text === undefined) {
                this.filteredCustomerCd = [];
                return;
            }
            const filteredDataCd = this.dropdown_mst_customer_cd[0].data.filter(item => {
                return item.mst_customers_cd.toString().toLowerCase().indexOf(text.toLowerCase()) > -1;
            }).slice(0, this.limit);
            this.filteredCustomerCd=[{
                data:filteredDataCd
            }]
        },
        onInputChangeName(text){
            this.fileSearch.mst_customers_nm= text;
            if (text === '' || text === undefined) {
                this.filteredCustomerNm = [];
                return;
            }
            const filteredDataNm = this.dropdown_mst_customer_nm[0].data.filter(item => {
                return item.mst_customers_nm.toString().toLowerCase().indexOf(text.toLowerCase()) > -1;
            }).slice(0, this.limit);

            this.filteredCustomerNm=[{
                data:filteredDataNm
            }]

        },
        onSelectedCd(option) {
            this.fileSearch.mst_customers_cd = option.item.mst_customers_cd;
            this.fileSearch.mst_customers_nm = option.item.mst_customers_nm;
        },
        onSelectedName(option) {
            this.fileSearch.mst_customers_cd = option.item.mst_customers_cd;
            this.fileSearch.mst_customers_nm = option.item.mst_customers_nm;
        },
        clearCondition:function () {
            this.fileSearch.daily_report_date = "";
            this.fileSearch.mst_business_office_id = "";
            this.setDefaultDate();
            this.fileSearch.invoicing_flag="";
            this.fileSearch.mst_customers_cd="";
            this.$refs.mst_customers_cd.searchInput = "";
            this.fileSearch.mst_customers_nm="";
            this.$refs.mst_customers_nm.searchInput = "";
            this.filteredCustomerCd = [];
            this.filteredCustomerNm = [];
        },
        setDefaultDate:function () {
            var from_date = new Date();
            this.fileSearch.from_date=from_date.getFullYear()+"/"+(from_date.getMonth()+1)+"/"+1;
            var to_date = new Date();
            var lastDay = new Date(to_date.getFullYear(), to_date.getMonth()+1,0);
            this.fileSearch.to_date=lastDay.getFullYear()+"/"+(lastDay.getMonth()+1)+"/"+lastDay.getDate();
        },
        createCSV: async function () {
            var that = this;
            that.loading = true;
            var data = {
                data: that.allItems,
            };
            sales_lists_service.createCSV(data).then(  function (response){
                that.downloadFileCSV(response, 'csv');
                that.loading = false;
            });
        },
        downloadFileCSV(response) {
            // It is necessary to create a new blob object with mime-type explicitly set
            // otherwise only Chrome works like it should
            var newBlob = new Blob([response.data], {type: response.headers["content-type"]})

            var filename = response.headers['content-disposition'].split('=')[1].replace(/^\"+|\"+$/g, '')

            // IE doesn't allow using a blob object directly as link href
            // instead it is necessary to use msSaveOrOpenBlob
            if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                window.navigator.msSaveOrOpenBlob(newBlob,filename)
                return
            }

            // For other browsers:
            // Create a link pointing to the ObjectURL containing the blob.
            const data = window.URL.createObjectURL(newBlob)
            var link = document.createElement('a')
            link.href = data
            link.download = filename;
            link.click()
            setTimeout(function () {
                // For Firefox it is necessary to delay revoking the ObjectURL
                window.URL.revokeObjectURL(data)
            }, 100)
        },
    },
    mounted () {
        sales_lists_service.loadCustomerList().then((response) => {
            this.dropdown_mst_customer_cd[0].data =  response.data;
            this.dropdown_mst_customer_nm[0].data =  response.data;
        });
        this.setDefaultDate();
    },
    components: {
        PulseLoader,
        DatePicker,
        VueAutosuggest,
    }
});
