
vehicles_service = {
    loadList: function (data) {
        return axios.post('/vehicles/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    delete: function (id) {
        return axios.get('/vehicles/api-v1/delete/' + id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    checkIsExist: function (id,data) {
        return axios.post('/vehicles/api-v1/checkIsExist/' + id,data).then(function (response) {
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
    getListStaff: function () {
        return axios.post('/vehicles/api-v1/load-list-staff').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    submit: function (data) {
        return axios.post('/vehicles/api-v1/submit', data,{ 'content-type': 'multipart/form-data' }).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
}