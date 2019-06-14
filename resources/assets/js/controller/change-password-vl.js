import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'
import { VueAutosuggest }  from "vue-autosuggest";
var navBarMain = new Vue({
    el: '#navBarMain',
    data: {
        loading:false,
        field:{
            password:""
        },
        message: '',
        errors:[],
        successMsg:"",

    },
    methods : {
        changePassword: function() {
            var that=this;
            staffs_service.changePassword(that.field).then((response) => {
                $('.message-error').show();
                if(response.success == false){
                    that.loading=true;
                    that.errors = response.message;
                    that.successMsg="";
                    $('#msg-success-password').hide();
                }else{
                    that.errors = [];
                    that.field={
                        password:""
                    };
                    that.successMsg=response.message;
                    $('#changePasswordModal').modal('hide');
                    $('#msg-success-password').show();
                }
                that.loading = false;
            });
        },
        openModal:function () {
            this.loading = false;
            this.errors=[];
            this.field.password="";
            this.successMsg="";
            $('#changePasswordModal').modal('show');
        }
    },
     mounted () {
    },
    components: {
        PulseLoader,
        DatePicker,
        VueAutosuggest,
    }
});