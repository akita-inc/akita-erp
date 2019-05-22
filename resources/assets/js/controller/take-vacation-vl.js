import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'
import moment from 'moment';

var ctrTakeVacationVl = new Vue({
    el: '#ctrTakeVacationVl',
    data: {
        lang:lang_date_picker,
        loading:false,
        take_vacation_edit:0,
        take_vacation_id:null,
        field:{
            applicant_id:staff_cd,
            staff_nm:staff_nm,
            applicant_office_id	:mst_business_office_id,
            applicant_office_nm	:business_ofice_nm,
            approval_kb:defaultApprovalKb,
            half_day_kb	:defaultHalfDayKb,
            start_date:currentDate,
            end_date:currentDate,
            days:1,
            times:0,
            reasons:'',
            wf_additional_notice:[
                {
                    email_address:'',
                    staff_cd:''
                }
            ],
            mode:$('#mode').val(),
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
    },
    methods : {
        resetForm: function () {
            this.errors = {};
            this.disabledStartDate = false;
            this.disabledEndDate=  false;
            this.disabledDays = false;
            this.disabledTimes = false;
            if($("#hd_take_vacation_edit").val() == 1){
                this.loadFormEdit();
            }else{
                this.field = {
                    applicant_id:staff_cd,
                    staff_nm:staff_nm,
                    applicant_office_id	:mst_business_office_id,
                    applicant_office_nm	:business_ofice_nm,
                    approval_kb:defaultApprovalKb,
                    half_day_kb	:defaultHalfDayKb,
                    start_date:currentDate,
                    end_date:currentDate,
                    days:1,
                    times:0,
                    reasons:'',
                    wf_additional_notice:[
                        {
                            email_address:'',
                            staff_cd:''
                        }
                    ],
                    mode:$('#mode').val(),
                };
                $('input:checkbox').prop('checked',false);
                $('input:text').val('');
                $('input[type="tel"]').val('');
                $('textarea').val('');
            }
            this.handleChangeHalfDay();
        },
        submit: function(status){
            let that = this;
            // that.loading = true;
            if(this.field.mode != 'register'){
                this.field["id"] = this.take_vacation_id;
            }
            switch (this.field.mode) {
                case 'register':
                    take_vacation_list_service.submit(that.field).then((response) => {
                        if(response.success == false){
                            that.errors = response.message;
                        }else{
                            that.errors = [];
                            window.location.href = listRoute;
                        }
                        that.loading = false;
                    });
                    break;
                case 'edit':
                    take_vacation_list_service.checkIsExist(that.empty_info_id, {'mode' : this.field.mode,'status': status,'modified_at': that.modified_at }).then((response) => {
                        if (!response.success) {
                            that.loading = false;
                            alert(response.msg);
                            that.backHistory();
                            return false;
                        } else {
                            take_vacation_list_service.submit(that.field).then((response) => {
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
                case 'approval':
                    empty_info_service.checkIsExist(that.empty_info_id, {'status': status,'modified_at': that.modified_at}).then((response) => {
                        if (!response.success) {
                            that.loading = false;
                            alert(response.msg);
                            that.backHistory();
                            return false;
                        } else {
                            empty_info_service.updateStatus(that.empty_info_id,{status:status}).then((response) => {
                                that.loading = false;
                                window.location.href = listRoute;
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
            if(this.empty_info_edit == 1){
                empty_info_service.backHistory().then(function () {
                    window.location.href = listRoute;
                });
            }else{
                window.location.href = listRoute;
            }
        },
        loadFormEdit: function () {
            let that = this;
            if(this.field.mode != 'register'){
                this.loading = true;
                that.empty_info_edit = 1;
                that.empty_info_id = $("#hd_id").val();
                $.each(this.field,function (key,value) {
                    if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined && key != 'mst_bill_issue_destinations'){
                        that.field[key] = $("#hd_"+key).val();
                        if(key == "asking_price"){
                            that.field[key] = '¥ '+$("#hd_"+key).val().toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                        }
                        if(key == "max_load_capacity"){
                            that.field[key] = $("#hd_"+key).val().toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                        }
                    }
                });
                if(this.field.mode=='reservation_approval'){
                    this.field.application_office_id = $("#hd_ask_office").val();;
                    this.field.reservation_person =$("#hd_reservation_person").val();
                }
                this.modified_at = $('#hd_modified_at').val();
                this.loading = false;
            }
        },
        deleteInfo: function(id){
            var that = this;
            empty_info_service.checkIsExist(id,{'mode' : 'delete'}).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    that.backHistory();
                    return false;
                } else {
                    if (confirm(messages["MSG06001"])) {
                        empty_info_service.delete(id).then((response) => {
                            window.location.href = listRoute;
                        });
                    }
                }
            });
        },
        handleSelectDate: function (date) {
            var that = this;
            if(that.field.half_day_kb==2 || that.field.half_day_kb==3 ){
                that.field.end_date = that.field.start_date;
            }
            let end_date =  moment(that.field.end_date);
            let start_date =  moment(that.field.start_date);
            that.field.days = end_date.diff(start_date, 'days')+1;

        },
        handleChangeHalfDay: function () {
            var that = this;
            switch (that.field.half_day_kb) {
                case '1':
                    that.handleSelectDate();
                    that.field.times = 0;
                    that.disabledStartDate = false;
                    that.disabledEndDate = false;
                    that.disabledDays = false;
                    that.disabledTimes= true;
                    break;
                case '2':
                case '3':
                    that.field.days = 0;
                    that.field.times = 4;
                    that.field.end_date = that.field.start_date;
                    that.disabledStartDate = false;
                    that.disabledEndDate = true;
                    that.disabledDays = true;
                    that.disabledTimes= true;
                    break;
                case '4':
                    that.field.start_date = currentDate;
                    that.field.end_date = currentDate;
                    that.field.days = 0;
                    that.disabledStartDate = true;
                    that.disabledEndDate = true;
                    that.disabledDays = true;
                    that.disabledTimes = false;
                    break;


            }
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
                    that.message = response.msg;
                    return false;
                } else {
                    that.listStaffs = response.info;
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

    },
    mounted () {
        this.handleChangeHalfDay();
        if($("#hd_take_vacation_edit").val() == 1) {
            this.loadFormEdit();
        }
        if(document.getElementById("days")!=null){
            this.setInputFilter(document.getElementById("days"), function(value) {
                return /^\d*$/.test(value); });
        }
        if(document.getElementById("times")!=null){
            this.setInputFilter(document.getElementById("times"), function(value) {
                return /^\d*$/.test(value); });
        }
    },
    components: {
        PulseLoader,
        DatePicker,
    }
});
