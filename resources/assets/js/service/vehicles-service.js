
vehicles_service = {
    loadList: function (data) {
        return axios.post('/vehicles/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    delete: function (id) {
        return axios.get('/vehicles/delete/' + id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    checkIsExist: function (id) {
        return axios.get('/vehicles/api-v1/checkIsExist/' + id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    backHistory: function () {
        return axios.get('/vehicles/api-v1/back-history').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getListStaff: function (data) {
        return axios.post('/vehicles/api-v1/load-list-staff',data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
}