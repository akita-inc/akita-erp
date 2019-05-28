accounts_payable_data_output_service = {
    loadList: function (data) {
        return axios.post('/accounts_payable_data_output/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    createCSV: function (data) {
        return axios.post('/accounts_payable_data_output/api-v1/create-csv',data, {responseType: 'arraybuffer'}).then(function (response) {
            return response;
        }).catch(function (error) {
            return error;
        });
    },
    getCurrentYearMonth: function (data) {
        return axios.post('/accounts_payable_data_output/api-v1/get-current-year-month',data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
}
