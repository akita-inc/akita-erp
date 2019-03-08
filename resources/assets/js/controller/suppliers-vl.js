import { Core } from '../package/yubinbango-core';

var ctrSupplierrsVl = new Vue({
    el: '#ctrSupplierrsVl',
    data: {
    },
    methods : {
        convertKana: function (e , destination) {
            suppliers_service.convertKana({'data': e.target.value}).then(function (data) {
                $('#'+destination).val(data.info);
            });
        },
        getAddrFromZipCode: function() {
            var zip = $('#zip_cd').val();
            new Core(zip, function (addr) {
                $('#prefectures_cd').val(addr.region_id);// 都道府県ID
                $('#address1').val(addr.locality);// 市区町村
                $('#address2').val(addr.street);// 町域
            });
        }
    },
    mounted () {

    },
    components: {
    }
});
