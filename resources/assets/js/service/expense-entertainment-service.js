expense_entertainment_service = {
    loadList: function (data) {
        return axios.post('/expense_entertainment/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    checkIsExist: function (id,data) {
        return axios.post('/expense_entertainment/api-v1/checkIsExist/' + id, data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    backHistory: function () {
        return axios.get('/expense_entertainment/api-v1/back-history').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    searchStaff: function (data) {
        return axios.post('/expense_entertainment/api-v1/search-staff',data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    delete: function (id) {
        return axios.get('/expense_entertainment/api-v1/delete/' + id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    submit: function (data) {
        return axios.post('/expense_entertainment/api-v1/submit', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    searchEntertainment: function (data) {
        return axios.post('/empty_info/api-v1/search-entertainment',data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
}
