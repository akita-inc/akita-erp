
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
    }
}