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
    checkIsExist: function (id) {
        return axios.get('/staffs/api-v1/checkIsExist/' + id).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    }
}
