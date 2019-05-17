import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import moment from 'moment';

var ctrWorkFlowVl = new Vue({
    el: '#ctrWorkFlowVl',
    data: {
        loading:false,
        work_flow_edit:0,
        work_flow_id:null,
        field:{
            name:"",
            steps:"",
            mst_wf_require_approval_base: [],
            mst_wf_require_approval: [],

        },
        screenStep:1,
        listApplicant:null,
        errors:{},
        modified_at: "",
        defaultLevel:defaultLevel,
        defaultKb:defaultKb,
        steps_default: null,

    },
    methods : {
        getListWfApplicantAffiliationClassification: function(){
            var that = this;
            work_flow_list_service.getListWfApplicantAffiliationClassification().then((response) => {
                that.listApplicant = response.info;
            });
        },
        nextStep: async function(){
            this.loading = true;
            this.screenStep++;
            if(this.screenStep==2){
                await this.handleStep2();
            }else{
                await this.handleStep3();
            }
            this.loading = false;
        },
        previousStep: async function(){
            this.loading = true;
            this.screenStep--;
            if(this.screenStep==2){
                await this.handleStep2();
            }else{
                await this.handleStep3();
            }
            this.loading = false;
        },
        handleStep2:async function(){
            var that = this;
            if(that.work_flow_edit==0) {
                await work_flow_list_service.validateData({
                    name: that.field.name,
                    steps: that.field.steps
                }).then((response) => {
                    if (response.success) {
                        that.errors = [];
                        for (var i = 0; i < that.field.steps; i++) {
                            that.field.mst_wf_require_approval_base.push({
                                approval_steps: i + 1,
                                approval_levels: that.defaultLevel,
                                approval_kb: that.defaultKb,
                            });
                        }
                    } else {
                        that.screenStep--;
                        that.errors = response.message;
                    }
                });
            }else{
                await work_flow_list_service.validateData({
                    name: that.field.name,
                    steps: that.field.steps
                }).then(async function(response) {
                    if (response.success) {
                        that.errors = [];
                        await work_flow_list_service.getListApprovalBase({wf_type:that.work_flow_id}).then((response1) => {
                            if(response1.info.length > 0){
                                that.field.mst_wf_require_approval_base = response1.info;
                            }
                            if( that.field.steps< that.steps_default ){
                                for (var i = that.steps_default ; i > that.field.steps; i--) {
                                    that.field.mst_wf_require_approval_base.splice(i-1, 1);
                                }
                            }else if(that.field.steps > that.steps_default ){
                                for (var i = that.steps_default ; i < that.field.steps; i++) {
                                    that.field.mst_wf_require_approval_base.push({
                                        approval_steps: i + 1,
                                        approval_levels: that.defaultLevel,
                                        approval_kb: that.defaultKb,
                                    });
                                }
                            }
                        });
                    } else {
                        that.screenStep--;
                        that.errors = response.message;
                    }
                });
            }
        },
        handleStep3: async function(){
            var that = this;
            if(that.work_flow_edit==0) {
                await that.listApplicant.forEach((value, key) => {
                    that.field.mst_wf_require_approval[value.date_id] = {
                        applicant_section_nm: "",
                        list: [],
                    };
                    that.field.mst_wf_require_approval[value.date_id].applicant_section_nm = value.date_nm;
                    that.field.mst_wf_require_approval_base.forEach((value1, key1) => {
                        var data = {};
                        data.approval_steps = value1.approval_steps;
                        data.approval_levels = value1.approval_levels;
                        data.approval_kb = value1.approval_kb;
                        that.field.mst_wf_require_approval[value.date_id].list.push(data)
                    });
                });
            }else{
                await work_flow_list_service.getListApproval({wf_type:that.work_flow_id}).then(async (response) => {
                    if(response.info.length > 0){
                        response.info.forEach((items,section)=>{
                            that.field.mst_wf_require_approval[section] = {
                                applicant_section_nm: "",
                                list: [],
                            };
                            that.field.mst_wf_require_approval[section].applicant_section_nm = items[0].applicant_section_nm;
                            that.field.mst_wf_require_approval[section].list = items
                        });
                    }
                    if( that.field.steps< that.steps_default ){
                        that.field.mst_wf_require_approval.forEach((value,key)=>{
                            for (var i = that.steps_default ; i > that.field.steps; i--) {
                                value.list.splice(i - 1, 1);
                            }
                        });

                    }else if(that.field.steps > that.steps_default ){
                        that.field.mst_wf_require_approval.forEach((value,key)=> {
                            for (var i = that.steps_default; i < that.field.steps; i++) {
                                that.field.mst_wf_require_approval_base.forEach((value1, key1) => {
                                    if(key1==i){
                                        var data = {};
                                        data.approval_steps = value1.approval_steps;
                                        data.approval_levels = value1.approval_levels;
                                        data.approval_kb = value1.approval_kb;
                                        value.list.push(data)
                                    }
                                });
                            }
                        });
                    }
                });
            }
        },
        submit: function(status){
            let that = this;
            that.loading = true;
            if($("#hd_work_flow_edit").val() == 1){
                this.field["id"] = this.work_flow_id;
                work_flow_list_service.checkIsExist(that.work_flow_id, {'mode' : this.field.mode,'status': status,'modified_at': that.modified_at }).then((response) => {
                    if (!response.success) {
                        that.loading = false;
                        alert(response.msg);
                        that.backHistory();
                        return false;
                    } else {
                        work_flow_list_service.submit(that.field).then((response) => {
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
            }else{
                work_flow_list_service.submit(that.field).then((response) => {
                    if(response.success == false){
                        that.errors = response.message;
                    }else{
                        that.errors = [];
                        window.location.href = listRoute;
                    }
                    that.loading = false;
                });

            }
        },
        showError: function ( errors ){
            return errors.join("<br/>");
        },
        backHistory: function () {
            if(this.work_flow_edit == 1){
                work_flow_list_service.backHistory().then(function () {
                    window.location.href = listRoute;
                });
            }else{
                window.location.href = listRoute;
            }
        },
        loadFormEdit: function () {
            let that = this;
            this.loading = true;
            that.work_flow_edit = 1;
            that.work_flow_id = $("#hd_id").val();
            that.steps_default = $('#hd_steps').val();
            $.each(this.field,function (key,value) {
                if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined && key != 'mst_bill_issue_destinations'){
                    that.field[key] = $("#hd_"+key).val();
                }
            });
            this.modified_at = $('#hd_modified_at').val();
            this.loading = false;

        },
        resetForm: async function () {
            this.loading = true;
            this.errors = {};
            if($("#hd_work_flow_edit").val() == 1){
                switch (this.screenStep) {
                    case 1:
                        this.loadFormEdit();
                        break;
                    case 2:
                        this.field.mst_wf_require_approval_base =[];
                        await this.handleStep2();
                        break;
                    case 3:
                        this.field.mst_wf_require_approval =[];
                        await this.handleStep3();
                        break;

                }
            }else{
                switch (this.screenStep) {
                    case 1:
                        this.field.name ="";
                        this.field.steps ="";
                        break;
                    case 2:
                        this.field.mst_wf_require_approval_base =[];
                        await this.handleStep2();
                        break;
                    case 3:
                        this.field.mst_wf_require_approval =[];
                        await this.handleStep3();
                        break;

                }
                $('input:text').val('');
            }
            this.loading = false;
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
        this.getListWfApplicantAffiliationClassification();
        if($("#hd_work_flow_edit").val() == 1) {
            this.loadFormEdit();
        }
        if(document.getElementById("steps")!=null){
            this.setInputFilter(document.getElementById("steps"), function(value) {
                return /^\d*$/.test(value); });
        }
    },
    components: {
        PulseLoader
    }
});
