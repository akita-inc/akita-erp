payments_service = {
    loadList: function (data) {
        return axios.post('/payments/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getDetailsPayment:function (data) {
        return axios.post('/payments/api-v1/get-details-payment',data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    loadListBundleDt: function (data) {
        return axios.post('/payments/api-v1/load-list-bundle-dt',data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    loadListSuppliers: function(data){
        return axios.get('/payments/api-v1/get-list-suppliers', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
}