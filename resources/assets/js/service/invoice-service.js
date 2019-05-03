invoice_service = {
    loadListCustomers: function(data){
        return axios.get('/invoices/api-v1/getListCustomers', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    loadList: function (data) {
        return axios.post('/invoices/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    checkIsExist: function (id,data) {
        return axios.post('/invoices/api-v1/checkIsExist/' + id,data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    backHistory: function () {
        return axios.get('/invoices/api-v1/back-history').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    loadListBundleDt: function (data) {
        return axios.post('/invoices/api-v1/load-list-bundle-dt',data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getDetailsInvoice: function (data) {
        return axios.post('/invoices/api-v1/get-details-invoice',data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getListStaffJobEx: function (id) {
        return axios.get('/invoices/api-v1/list-staff-job-ex/'+id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getListStaffQualifications:function(id){
        return axios.get('/invoices/api-v1/list-staff-qualification/'+id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getStaffDependents:function(id){
        return axios.get('/invoices/api-v1/list-staff-dependents/'+id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getStaffAuths:function(id){
        return axios.get('/invoices/api-v1/list-staff-auths/'+id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
}
