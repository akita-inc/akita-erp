import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master';
import moment from 'moment';

var ctrExpenseApplicationVl = new Vue({
    el: '#ctrExpenseApplicationVl',
    data: {
        lang:lang_date_picker,
        loading:false,
        expense_entertainment_edit:0,
        expense_entertainment_id:null,
        field:{
            applicant_id:staff_cd,
            staff_nm:staff_nm,
            applicant_office_id	:mst_business_office_id,
            applicant_office_nm	:business_ofice_nm,
            wf_business_entertaining_id:"",
            date:"",
            client_company_name:"",
            client_members:"",
            client_members_count:"",
            own_members:"",
            own_members_count:"",
            place:"",
            report:"",
            cost:"",
            payoff_kb:0,
            deposit_amount:'',
            payoff_amount:0,
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
    },
    methods : {
        resetForm: function () {
            this.errors = {};
            if($("#hd_expense_entertainment_edit").val() == 1){
                this.loadFormEdit();
            }else{
                this.field = {
                    applicant_id:staff_cd,
                    staff_nm:staff_nm,
                    applicant_office_id	:mst_business_office_id,
                    applicant_office_nm	:business_ofice_nm,
                    wf_business_entertaining_id:"",
                    date:"",
                    client_company_name:"",
                    client_members:"",
                    client_members_count:"",
                    own_members:"",
                    own_members_count:"",
                    place:"",
                    report:"",
                    cost:"",
                    payoff_kb:defaultPayoffKb,
                    deposit_amount:'',
                    payoff_amount:0,
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
                this.field["id"] = this.expense_entertainment_id;
            }
            that.field.cost=that.removeComma(that.field.cost);
            that.field.deposit_amount=that.removeComma(that.field.deposit_amount);
            that.field.payoff_amount=that.removeComma(that.field.payoff_amount);
            switch (this.field.mode) {
                case 'register':
                    expense_entertainment_service.submit(that.field).then((response) => {
                        if(response.success == false){
                            that.errors = response.message;
                            that.field.cost=that.addComma(isNaN(that.field.cost)?0:that.field.cost);
                            that.field.deposit_amount=that.addComma(isNaN(that.field.deposit_amount)?0:that.field.deposit_amount);
                            that.field.payoff_amount=that.addComma(isNaN(that.field.payoff_amount)?0:that.field.payoff_amount);
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
                    expense_entertainment_service.checkIsExist(that.expense_entertainment_id, {'mode' : this.field.mode,'approval_fg': approval_fg,'modified_at': that.modified_at }).then((response) => {
                        if (!response.success) {
                            that.loading = false;
                            alert(response.msg);
                            that.backHistory();
                            return false;
                        } else {
                            expense_entertainment_service.submit(that.field).then((response) => {
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
            if(this.expense_entertainment_edit == 1){
                expense_entertainment_service.backHistory().then(function () {
                    window.location.href = listRoute;
                });
            }else{
                window.location.href = listRoute;
            }
        },
        loadFormEdit: function () {
            let that = this;
            that.loading = true;
            that.expense_entertainment_edit = 1;
            that.expense_entertainment_id = $("#hd_id").val();
            $.each(this.field, function (key,value) {
                if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined && key != 'wf_additional_notice'){
                    that.field[key] = $("#hd_"+key).val();
                }
            });
            that.addCommaByID('cost');
            that.addCommaByID('deposit_amount');
            that.addCommaByID('payoff_amount');
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
            that.handlePayoffKb();
            that.loading = false;

        },
        deleteExpenseApplication: function(id){
            var that = this;
            expense_entertainment_service.checkIsExist(id,{'mode' : 'delete'}).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    that.backHistory();
                    return false;
                } else {
                    if (confirm(messages["MSG10028"])) {
                        expense_entertainment_service.delete(id).then((response) => {
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
        handlePayoffKb:function () {
            var that=this;
            let cost =  that.field.cost=='' ? 0 : that.removeComma(that.field.cost);
            let deposit_amount =  that.field.deposit_amount=='' ? 0 : that.removeComma(that.field.deposit_amount);
            if(that.field.payoff_kb==0) {
                that.field.payoff_amount= cost - deposit_amount;
                that.field.payoff_amount= that.addComma(that.field.payoff_amount)
            }
            else {
                that.field.payoff_amount= deposit_amount - cost;
                that.field.payoff_amount= that.addComma(that.field.payoff_amount);
            }
        },
        searchEntertainment: function () {
            var that = this;
            if(that.field.wf_business_entertaining_id==''){
                alert(messages['MSG10035']);
                return;
            }else{
                if(isNaN(that.field.wf_business_entertaining_id)){
                    alert(messages['MSG10010']);
                    return;
                }
            }
            expense_entertainment_service.searchEntertainment({wf_business_entertaining_id: that.field.wf_business_entertaining_id}).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    return false;
                } else {
                    let result = response.info;
                    that.field.date = result.date;
                    that.field.client_company_name = result.client_company_name;
                    that.field.client_members = result.client_members;
                    that.field.client_members_count = result.client_members_count;
                    that.field.own_members = result.own_members;
                    that.field.own_members_count = result.own_members_count;
                    that.field.place = result.place;
                    that.field.deposit_amount = result.deposit_amount;
                    that.addCommaByID('cost');
                    that.addCommaByID('deposit_amount');
                    that.handlePayoffKb();
                }
            });
        },
    },
    mounted () {
        this.handlePayoffKb();
        if($("#hd_expense_entertainment_edit").val() == 1) {
            this.loadFormEdit();
        }
        if(document.getElementById('search_business_entertaining')!=null){
            this.setInputFilter(document.getElementById('search_business_entertaining'), function(value) {
                return   /^\d*$/.test(value); });
        }
        if(document.getElementById('client_members_count')!=null){
            this.setInputFilter(document.getElementById('client_members_count'), function(value) {
                return   /^\d*$/.test(value); });
        }
        if(document.getElementById('own_members_count')!=null){
            this.setInputFilter(document.getElementById('own_members_count'), function(value) {
                return   /^\d*$/.test(value); });
        }
        if(document.getElementById('cost')!=null){
            this.setInputFilter(document.getElementById('cost'), function(value) {
                return   /^\d*$/.test(value); });
        }
        if(document.getElementById('deposit_amount')!=null){
            this.setInputFilter(document.getElementById('deposit_amount'), function(value) {
                return   /^\d*$/.test(value); });
        }
    },
    components: {
        PulseLoader,
        DatePicker,
    }
});
