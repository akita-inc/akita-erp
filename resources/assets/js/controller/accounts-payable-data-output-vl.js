import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import DatePicker from '../component/vue2-datepicker-master'
import { VueAutosuggest }  from "vue-autosuggest";
import moment from 'moment';

var ctrAccountsPayableDataOutputVl = new Vue({
    el: '#ctrAccountsPayableDataOutputVl',
    data: {
        lang:lang_date_picker,
        format_date: format_date_picker,
        loading:false,
        items:[],
        fileSearch:{
            billing_year: '',
            billing_month: '',
            closed_date:defaultBundleDt,
            billing_start_date:'',
            billing_end_date:'',
        },
        message: '',
        errors:[],
        flagSearch: false,
        outputSuccess: false,
    },
    methods : {
        clearCondition: function clearCondition() {
            this.fileSearch.mst_business_office_id="";
            this.fileSearch.billing_year="";
            this.fileSearch.billing_month="";
            this.fileSearch.closed_date="";
            this.fileSearch.start_date="";
            this.fileSearch.end_date="";
            this.errors = [];
            this.getCurrentYearMonth();
            this.handleEndDate();
        },
        createCSV: async function () {
            var that = this;
            this.loading = true;
            accounts_payable_data_output_service.createCSV({
                    'fieldSearch': that.fileSearch,
                }).then(  function (response){
                   if(response.headers['content-type']=='application/json'){
                       that.outputSuccess = false;
                       that.flagSearch = true;
                   }else{
                       that.outputSuccess = true;
                       that.flagSearch = true;
                       that.downloadFile(response);
                   }
            });
            this.loading = false;
        },
        downloadFile(response) {
            // It is necessary to create a new blob object with mime-type explicitly set
            // otherwise only Chrome works like it should
            var newBlob = new Blob([response.data], {type: response.headers["content-type"]})

            var filename = response.headers['content-disposition'].split('=')[1].replace(/^\"+|\"+$/g, '');

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
        getCurrentYearMonth: function(){
            var that = this;
            accounts_payable_data_output_service.getCurrentYearMonth().then((response) => {
                that.fileSearch.billing_year = response.current_year;
                that.fileSearch.billing_month = response.current_month;
                that.handleEndDate();
            });
        },
        handleEndDate: function () {
            var that = this;
            var end_date = that.fileSearch.billing_year+'/'+that.fileSearch.billing_month+'/'+that.fileSearch.closed_date;
            that.fileSearch.end_date = moment(end_date).format('YYYY/MM/DD');
            that.fileSearch.start_date = moment(end_date).subtract(1, 'months').add(1, 'days').format('YYYY/MM/DD');
        }
    },
    mounted () {
        this.getCurrentYearMonth();
    },
    components: {
        PulseLoader,
        DatePicker,
        VueAutosuggest,
    }
});