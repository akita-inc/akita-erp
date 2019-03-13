home_service = {
    convertKana: function (input) {
        return axios.post('/home/api-v1/convertKana',input).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    }
}
