work_flow_list_service = {
    loadList: function (data) {
        return axios.post('/work_flow/api-v1/getItems', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    checkIsExist: function (id,data) {
        return axios.post('/work_flow/api-v1/checkIsExist/' + id, data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    backHistory: function () {
        return axios.get('/work_flow/api-v1/back-history').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getListWfApplicantAffiliationClassification: function () {
        return axios.get('/work_flow/api-v1/get-list-wf-applicant-affiliation-classification').then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    validateData: function (data) {
        return axios.post('/work_flow/api-v1/validate-data',data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    submit: function (data) {
        return axios.post('/work_flow/api-v1/submit', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getListApprovalBase: function (data) {
        return axios.post('/work_flow/api-v1/get-list-approval-base', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    },
    getListApproval: function (data) {
        return axios.post('/work_flow/api-v1/get-list-approval', data).then(function (response) {
            return response.data;
        }).catch(function (error) {
            return error;
        });
    }
}
