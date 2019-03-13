
suppliers_service = {
    convertKana: function (input) {
        return axios.post('/api/supplier/convert-to-kana',input).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    loadList: function (data) {
        return axios.post('/suppliers/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    deleteSupplier: function (id) {
        return axios.get('/suppliers/delete/' + id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    checkIsExist: function (id) {
        return axios.get('/api/supplier/checkIsExist/' + id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    backHistory: function () {
        return axios.get('/api/supplier/back-history').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
}