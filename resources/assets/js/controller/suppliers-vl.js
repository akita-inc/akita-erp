var ctrSupplierrsVl = new Vue({
    el: '#ctrSupplierrsVl',
    data: {

    },
    methods : {
        convertKana: function (e , destination) {
            suppliers_service.convertKana({'data': e.target.value}).then(function (data) {
                $('#'+destination).val(data.info);
            });
        }
    },
    mounted () {
    },
    components: {
    }
});
