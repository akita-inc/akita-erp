suppliers_service = {
    convertKana: function (input) {
        return axios.post('/api/supplier/convert-to-kana',input).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    }
}
