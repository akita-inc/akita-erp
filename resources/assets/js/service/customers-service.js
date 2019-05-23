customers_service = {
    loadList: function (data) {
        return axios.post('/customers/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    deleteCustomer: function (id) {
        return axios.get('/customers/delete/' + id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    submit: function (data) {
        return axios.post('/customers/api-v1/submit', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    checkIsExist: function (id,data) {
        return axios.post('/customers/api-v1/checkIsExist/' + id,data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    backHistory: function () {
        return axios.get('/customers/api-v1/back-history').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getListBill: function (id) {
        return axios.get('/customers/api-v1/getListBill/'+id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
}
