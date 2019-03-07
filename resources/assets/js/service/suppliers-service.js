suppliers_service = {
    loadList: function (data) {
        return axios.post('/suppliers/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    gotoCreate: function (data) {
        return axios.post('/suppliers/create', data).then(function (response) {
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
    }
}
