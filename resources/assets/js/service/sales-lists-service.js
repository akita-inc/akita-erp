sales_lists_service = {
    loadList: function (data) {
        return axios.post('/sales_lists/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },

    checkIsExist: function (id,data) {
        return axios.post('/sales_lists/api-v1/checkIsExist/' + id, data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    backHistory: function () {
        return axios.get('/sales_lists/api-v1/back-history').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    loadCustomerList: function (type) {
        return axios.post('/sales_lists/api-v1/mst-customer-list',{type:type}).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    createCSV: function (data) {
        return axios.post('/sales_lists/api-v1/create-csv',data, {responseType: 'arraybuffer'}).then(function (response) {
            return response;
        }).catch(function (error) {
            return error;
        });
    },
}
