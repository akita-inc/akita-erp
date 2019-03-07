staffs_service = {
    loadList: function (data) {
        return axios.post('/staffs/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    }
}
