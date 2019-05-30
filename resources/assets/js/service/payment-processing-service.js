payment_processing_service = {
    loadListCustomers: function(data){
        return axios.get('/payment_processing/api-v1/getListCustomers', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    loadList: function (data) {
        return axios.post('/payment_processing/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    checkIsExist: function (id,data) {
        return axios.post('/payment_processing/api-v1/checkIsExist/' + id,data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    backHistory: function () {
        return axios.get('/payment_processing/api-v1/back-history').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
}
