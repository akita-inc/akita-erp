empty_info_service = {
    loadList: function (data) {
        return axios.post('/empty_info/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    }
}
