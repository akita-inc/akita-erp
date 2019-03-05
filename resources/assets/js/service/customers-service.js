customers_service = {
    create: function (input) {
        return axios.post('/admin/apiBK/customer/create',input).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    }
}
