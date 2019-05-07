import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master';
import { VueAutosuggest }  from "vue-autosuggest";
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
        fields: {
            "daily_report_date": "日報日付",
            "branch_office_cd":"支店CD",
            "document_no":"伝票NO",
            "registration_numbers":"登録番号",
            "staff_cd":"社員CD",
            "staff_nm":"社員名",
            "mst_customers_cd":"得意先CD",
            "customer_nm":"得意先名",
            "goods":"品物",
            "departure_point_name":"発地名",
            "landing_name":"着地名",
            "delivery_destination":"納入先",
            "quantity":"数量",
            "unit_price":"単価",
            "total_fee":"便請求金額",
            "insurance_fee":"保険料",
            "billing_fast_charge":"請求高速料",
            "discount_amount":"値引金額",
            "tax_included_amount":"請求金額",
            "loading_fee":"積込料",
            "wholesale_fee":"取卸料",
            "waiting_fee":"待機料",
            "incidental_fee":"附帯料",
            "surcharge_fee":"サーチャージ料",
        },
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
            var data = {
                pageSize: that.pageSize,
                page:page,
                fieldSearch: that.fileSearch,
                order: that.order,
            };
            that.flagSearch=false;
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
        },
        setDefaultDate:function () {
            var from_date = new Date();
            this.fileSearch.from_date=from_date.getFullYear()+"/"+(from_date.getMonth()+1)+"/"+1;
            var to_date = new Date();
            var lastDay = new Date(to_date.getFullYear(), to_date.getMonth()+1,0);
            this.fileSearch.to_date=lastDay.getFullYear()+"/"+(lastDay.getMonth()+1)+"/"+lastDay.getDate();
        },
        exportCSV:function () {
            let  data=this.allItems;
            let arrKeys=Object.keys(data[0]);
            let fields=this.fields;
            let headerFields=[];
            if(!this.fileSearch.mst_business_office_id)
            {
                const distinctBranchCd=[...new Set(data.map(item=>item.branch_office_cd))];
                for(var k=0;k<distinctBranchCd.length;k++)
                {
                    let arrMultiExport=[];
                    for(var j=0;j<data.length;j++)
                    {
                        if(distinctBranchCd[k]!==undefined && distinctBranchCd[k]==data[j].branch_office_cd)
                        {
                            arrMultiExport.push(data[j]);
                        }
                    }
                    this.downloadFile(arrKeys,fields,headerFields,arrMultiExport,distinctBranchCd[k])
                }
                console.log(distinctBranchCd);
            }
            else
            {
                this.downloadFile(arrKeys,fields,headerFields,data,null)
            }

        },
        downloadFile:function (arrKeys,fields,headerFields,data,branch_cd){
            let export_file_nm=this.export_file_nm.split("branch_office_cd").join(branch_cd?branch_cd:data[0].branch_office_cd);
            export_file_nm=export_file_nm.split("yyyymmddhhmmss").join(Date.now());
            for(var i=0;i<arrKeys.length;i++)
            {
                if(arrKeys[i]!==undefined && fields[arrKeys[i]]!==undefined)
                {
                    headerFields.push(fields[arrKeys[i]]);
                }
            }
            let csvContent = "data:text/csv;charset=shift-jis,";
            csvContent += [
                            headerFields.join(","),
                            ...data.map(item => '"'+Object.values(item).join('","')+'"'
                          )].join('\n').replace(/(^\[)|(\]$)/gm, "");
            const dataExport = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", dataExport);
            link.setAttribute("download", export_file_nm);
            link.click();
        }
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
