import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master';
import moment from 'moment';

var ctrExpenseApplicationVl = new Vue({
    el: '#ctrExpenseApplicationVl',
    data: {
        lang:lang_date_picker,
        loading:false,
        expense_application_edit:0,
        expense_application_id:null,
        field:{
            applicant_id:staff_cd,
            staff_nm:staff_nm,
            applicant_office_id	:mst_business_office_id,
            applicant_office_nm	:business_ofice_nm,
            date:"",
            cost:"",
            place:"",
            client_company_name:"",
            client_members:"",
            client_members_count:"",
            own_members:"",
            own_members_count:"",
            conditions:"",
            purpose:"",
            deposit_flg:defaultApprovalKb,
            deposit_amount:"",
            approval_fg:null,
            wf_additional_notice:[
                {
                    email_address:'',
                    staff_cd:''
                }
            ],
            mode:$('#mode').val(),
            send_back_reason:""
        },
        search:{
            name:"",
            mst_business_office_id:"",
        },
        deposit_flg:false,
        errors:{},
        disabledStartDate: false,
        disabledEndDate: false,
        disabledDays: false,
        disabledTimes: false,
        order: {
            col:'',
            descFlg: true,
            divId:''
        },
        listStaffs:[],
        message: '',
        currentIndex:0,
        listWfAdditionalNoticeDB: JSON.parse(listWfAdditionalNotice.replace(/&quot;/g,'"')),
        wf_business_entertaining_id:""
    },
    methods : {
        resetForm: function () {
            this.errors = {};
            if($("#hd_expense_application_edit").val() == 1){
                this.loadFormEdit();
            }else{
                this.field = {
                    applicant_id:staff_cd,
                    staff_nm:staff_nm,
                    applicant_office_id	:mst_business_office_id,
                    applicant_office_nm	:business_ofice_nm,
                    date:"",
                    cost:"",
                    place:"",
                    client_company_name:"",
                    client_members:"",
                    client_members_count:"",
                    own_members:"",
                    own_members_count:"",
                    conditions:"",
                    purpose:"",
                    deposit_flg:defaultApprovalKb,
                    deposit_amount:"",
                    approval_fg:null,
                    send_back_reason:"",
                    wf_additional_notice:[
                        {
                            email_address:'',
                            staff_cd:''
                        }
                    ],
                    mode:$('#mode').val(),
                };
                $('input:text').val('');
                $('input[type="tel"]').val('');
                $('textarea').val('');
                this.handleDepositFlag();
            }
        },
        submit: function(approval_fg){
            let that = this;
            that.loading = true;
            if(this.field.mode != 'register'){
                this.field["id"] = this.expense_application_id;
            }
            that.field.cost=that.removeComma(that.field.cost);
            that.field.deposit_amount=that.removeComma(that.field.deposit_amount);
            switch (this.field.mode) {
                case 'register':
                    expense_application_service.submit(that.field).then((response) => {
                        if(response.success == false){
                            that.errors = response.message;
                            that.field.cost=that.addComma(isNaN(that.field.cost)?0:that.field.cost);
                            if(that.field.deposit_flg==1)
                            {
                                that.field.deposit_amount=that.addComma(isNaN(that.field.deposit_amount)?0:that.field.deposit_amount);
                            }
                            else {
                                that.field.deposit_amount="";
                            }
                        }else{
                            that.errors = [];
                            window.location.href = listRoute;
                        }
                        that.loading = false;
                    });
                    break;
                case 'edit':
                case 'approval':
                    if(that.field.mode=='approval'){
                        that.field.approval_fg = approval_fg;
                    }
                    expense_application_service.checkIsExist(that.expense_application_id, {'mode' : this.field.mode,'approval_fg': approval_fg,'modified_at': that.modified_at }).then((response) => {
                        if (!response.success) {
                            that.loading = false;
                            alert(response.msg);
                            that.backHistory();
                            return false;
                        } else {
                            expense_application_service.submit(that.field).then((response) => {
                                if(response.success == false){
                                    that.errors = response.message;
                                }else{
                                    that.errors = [];
                                    window.location.href = listRoute;
                                }
                                that.loading = false;
                            });
                        }
                    });
                    break;
            }
        },
        showError: function ( errors ){
            return errors.join("<br/>");
        },
        backHistory: function () {
            if(this.expense_application_edit == 1){
                expense_application_service.backHistory().then(function () {
                    window.location.href = listRoute;
                });
            }else{
                window.location.href = listRoute;
            }
        },
        loadFormEdit: function () {
            let that = this;
            that.loading = true;
            that.expense_application_edit = 1;
            that.expense_application_id = $("#hd_id").val();
            $.each(this.field, function (key,value) {
                if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined && key != 'mst_bill_issue_destinations'){
                    that.field[key] = $("#hd_"+key).val();
                }
            });
            that.field.wf_additional_notice = JSON.parse(listWfAdditionalNotice.replace(/&quot;/g,'"'));
            if(that.field.wf_additional_notice.length==0){
                that.field.wf_additional_notice =[
                    {
                        email_address:'',
                        staff_cd:''
                    }
                ];
            }
            that.modified_at = $('#hd_modified_at').val();
            that.field.cost=that.addComma(that.field.cost);
            that.handleDepositFlag();
            // this.handleDepositFlag();
            that.loading = false;
            console.log(that.field);

        },
        deleteExpenseApplication: function(id){
            var that = this;
            expense_application_service.checkIsExist(id,{'mode' : 'delete'}).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    that.backHistory();
                    return false;
                } else {
                    if (confirm(messages["MSG10028"])) {
                        expense_application_service.delete(id).then((response) => {
                            window.location.href = listRoute;
                        });
                    }
                }
            });
        },
        openModal: function (index) {
            var that = this;
            that.currentIndex = index;
            that.message ='';
            that.listStaffs=[];
            that.search = {
                name:"",
                mst_business_office_id:"",
            };
            that.order = {
                col:'',
                descFlg: true,
                divId:''
            };
            $('#searchStaffModal').modal('show');
        },
        addRow: function () {
            var that = this;
            that.loading = true;
            that.field.wf_additional_notice.push({
                email_address:'',
                staff_cd:''
            });
            that.loading = false;
        },
        searchStaff: function () {
            var that = this;
            take_vacation_list_service.searchStaff({
                name: that.search.name,
                mst_business_office_id: that.search.mst_business_office_id,
                order:that.order
            }).then((response) => {
                if (!response.success) {
                    that.listStaffs = [];
                    that.message = response.msg;
                    return false;
                } else {
                    that.listStaffs = response.info;
                    that.message = '';
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
            this.searchStaff();
        },
        setEmailAddress: function (item) {
            var that = this;
            that.field.wf_additional_notice[that.currentIndex].email_address = item.mail;
            that.field.wf_additional_notice[that.currentIndex].staff_cd = item.staff_cd;
            $('#searchStaffModal').modal('hide');
        },
        setInputFilter: function (textbox, inputFilter) {
            ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
                textbox.addEventListener(event, function() {
                    if (inputFilter(this.value)) {
                        this.oldValue = this.value;
                        this.oldSelectionStart = this.selectionStart;
                        this.oldSelectionEnd = this.selectionEnd;
                    } else if (this.hasOwnProperty("oldValue")) {
                        this.value = this.oldValue;
                        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                    }
                });
            });
        },
        addComma: function (value) {
            if(value!=null  && value!=""){
                return  '¥'+value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }else{
                return "";
            }
        },
        removeComma: function (value) {
            if(value!=null && value!="") {
                return  parseFloat(value.toString().replace(/,/g, '').replace('¥',''));
            }else{
                return "";
            }
        },
        addCommaByID: function (id,key) {
            if(this.field[id]!=null && this.field[id]!=""){
                this.field[id] = '¥'+this.field[id].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
            else
            {
                this.field[id] ="";
            }
            this.$forceUpdate();
        },
        removeCommaByID: function (id,key) {
            if(this.field[id]!==null && this.field[id]!=""){
                this.field[id] = parseFloat(this.field[id].toString().replace(/,/g, '').replace('¥',''));
            }
            else
            {
                this.field[id] ="";

            }
            this.$forceUpdate();
        },
        handleDepositFlag:function () {
            var that=this;
            if(that.field.deposit_flg==0)
            {
                that.field.deposit_amount="";
                that.deposit_flg=true;
            }
            else
            {
                that.field.deposit_amount=(that.field.deposit_amount==null)?"":that.addComma(that.field.deposit_amount);
                that.deposit_flg=false;
            }
        }
    },
    mounted () {
        this.handleDepositFlag();
        if($("#hd_expense_application_edit").val() == 1) {
            this.loadFormEdit();
        }
    },
    components: {
        PulseLoader,
        DatePicker,
    }
});
