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
    createPDF: function (data) {
        return axios.post('/invoices/api-v1/create-pdf',data,{responseType: 'arraybuffer'}).then(function (response) {
            return response;
        }).catch(function (error) {
            return error;
        });
    },
    createCSV: function (data) {
        return axios.post('/invoices/api-v1/create-csv',data, {responseType: 'arraybuffer'}).then(function (response) {
            return response;
        }).catch(function (error) {
            return error;
        });
    },

}
