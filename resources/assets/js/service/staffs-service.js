staffs_service = {
    loadList: function (data) {
        return axios.post('/staffs/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    deleteStaffs: function (id) {
        return axios.get('/staffs/api-v1/delete/' + id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    submit: function (data) {
        return axios.post('/staffs/api-v1/submit', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    checkIsExist: function (id) {
        return axios.get('/staffs/api-v1/checkIsExist/' + id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    backHistory: function () {
        return axios.get('/staffs/api-v1/back-history').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    loadListReMunicipalOffice: function () {
        return axios.post('/staffs/api-v1/relocation-municipal-office').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    loadRoleConfig: function (role_id) {
        return axios.post('/staffs/api-v1/load-role-config',{role_id:role_id}).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    }
}
