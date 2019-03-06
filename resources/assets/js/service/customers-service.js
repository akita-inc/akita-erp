customers_service = {
    loadList: function (data) {
        return axios.post('/customers/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    }
}
