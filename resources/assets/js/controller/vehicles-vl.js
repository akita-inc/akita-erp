import { Core } from '../package/yubinbango-core';
import DatePicker from '../component/vue2-datepicker-master'
import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import ModalViewerFile from '../component/ModalViewerFile'

var ctrVehiclesVl = new Vue({
    el: '#ctrVehiclesVl',
    data: {
        lang:lang_date_picker,
        listStaffs: [],
        field:{
            mode:"",
            vehicles_cd:"",
            door_number:"",
            vehicles_kb:"",
            registration_numbers:"",
            mst_business_office_id:"",
            vehicle_size_kb:"",
            vehicle_purpose_id:"",
            land_transport_office_cd:"",
            vehicle_inspection_sticker_pdf:"",
            registration_dt:"",
            first_year_registration_dt:"",
            vehicle_classification_id:"",
            private_commercial_id:"",
            car_body_shape_id:"",
            vehicle_id:"",
            seating_capacity:"",
            max_loading_capacity:"",
            vehicle_body_weights:"",
            vehicle_total_weights:"",
            frame_numbers:"",
            vehicle_lengths:"",
            vehicle_widths:"",
            vehicle_heights:"",
            axle_loads_ff:"",
            axle_loads_fr:"",
            axle_loads_rf:"",
            axle_loads_rr:"",
            vehicle_types:"",
            engine_typese:"",
            total_displacements:"",
            rated_outputs:"",
            kinds_of_fuel_id:"",
            type_designation_numbers:"",
            id_segment_numbers:"",
            wireless_installation_fg:"",
            owner_nm:"",
            owner_address:"",
            user_nm:"",
            user_address:"",
            user_base_locations:"",
            expiry_dt:"",
            car_inspections_notes:"",
            digital_tachograph_numbers:"",
            etc_numbers:"",
            drive_recorder_numbers:"",
            bed_fg:"",
            refrigerator_fg:"",
            drive_system_id:"",
            transmissions_id:"",
            transmissions_notes:"",
            suspensions_cd:"",
            tank_capacity_1:"",
            tank_capacity_2:"",
            loading_inside_dimension_capacity_length:"",
            loading_inside_dimension_capacity_width:"",
            loading_inside_dimension_capacity_height:"",
            snowmelt_fg:"",
            double_door_fg:"",
            floor_iron_plate_fg:"",
            floor_sagawa_embedded_fg:"",
            floor_roller_fg:"",
            floor_joloda_conveyor_fg:"",
            power_gate_cd:"",
            vehicle_delivery_dt:"",
            specification_notes:"",
            mst_staff_cd:"",
            personal_insurance_prices:"",
            property_damage_insurance_prices:"",
            vehicle_insurance_prices:"",
            picture_fronts:"",
            picture_rights:"",
            picture_lefts:"",
            picture_rears:"",
            acquisition_amounts:"",
            acquisition_amortization:"",
            durable_years:"",
            tire_sizes:"",
            battery_sizes:"",
            dispose_dt:"",
            notes:"",
            deleteFile:[],
        },
        loading:false,
        errors:{},
        vehicle_edit:0,
        vehicle_id:null,
        file:{
            vehicle_inspection_sticker_pdf:"",
            picture_fronts:"",
            picture_rights:"",
            picture_lefts:"",
            picture_rears:"",
        },
        checkbox:[
            'wireless_installation_fg',
            'bed_fg',
            'refrigerator_fg',
            'snowmelt_fg',
            'double_door_fg',
            'floor_iron_plate_fg',
            'floor_sagawa_embedded_fg',
            'floor_roller_fg',
            'floor_joloda_conveyor_fg',
        ]

    },
    methods : {
        onBlur: function(){
            this.history = [];
            this.furigana = '';
        },
        getAddrFromZipCode: function() {
            var zip = $('#zip_cd').val();
            new Core(zip, function (addr) {
                $('#prefectures_cd').val(addr.region_id);// 都道府県ID
                $('#address1').val(addr.locality);// 市区町村
                $('#address2').val(addr.street);// 町域
            });
        },
        deleteVehicle: function(id){
            var that = this;
            vehicles_service.checkIsExist(id).then((response) => {
                if (!response.success) {
                    alert(response.msg);
                    that.backHistory();
                    return false;
                } else {
                    if (confirm(messages["MSG06001"])) {
                        vehicles_service.delete(id).then((response) => {
                            window.location.href = listRoute;
                        });
                    }
                }
            });
        },
        backHistory: function () {
            if(this.vehicle_edit == 1){
                vehicles_service.backHistory().then(function () {
                    window.location.href = listRoute;
                });
            }else {
                window.location.href = listRoute;
            }
        },
        getListStaff: function(){
            var that = this;
            vehicles_service.getListStaff().then(function (result) {
                that.listStaffs = result.info;
            });
        },
        submit: async function(){
            let that = this;
            that.loading = true;
            if(this.vehicle_edit == 1){
                this.field["id"] = this.vehicle_id;
                this.field["mode"] =  'edit';
            }
            let formData = new FormData();

            formData.append('data', JSON.stringify(this.field));
            formData.append('vehicle_inspection_sticker_pdf', this.file.vehicle_inspection_sticker_pdf);
            formData.append('picture_fronts', this.file.picture_fronts);
            formData.append('picture_rights', this.file.picture_rights);
            formData.append('picture_lefts', this.file.picture_lefts);
            formData.append('picture_rears', this.file.picture_rears);
            if(this.vehicle_edit == 1) {
                vehicles_service.checkIsExist(this.vehicle_id,{'mode':'edit'}).then((response) => {
                    if (!response.success) {
                        alert(response.msg);
                        that.backHistory();
                        return false;
                    } else {
                        vehicles_service.submit(formData).then((response) =>  {
                            that.field["mode"] = null;
                            that.loading = false;
                            if(response.success == false){
                                that.errors = response.message;
                            }else{
                                that.errors = [];
                                window.location.href = listRoute;
                            }
                        });
                    }
                });
            }else{
                vehicles_service.submit(formData).then((response) =>  {
                    that.field["mode"] = null;
                    that.loading = false;
                    if(response.success == false){
                        that.errors = response.message;
                    }else{
                        that.errors = [];
                        window.location.href = listRoute;
                    }
                });
            }
        },
        loadFormEdit: function () {
            let that = this;
            if($("#hd_vehicle_edit").val() == 1){
                this.loading = true;
                that.vehicle_edit = 1;
                that.vehicle_id = $("#hd_id").val();
                $.each(this.field,function (key,value) {
                    if( $("#hd_"+key) != undefined && $("#hd_"+key).val() != undefined ){
                        if(that.checkbox.indexOf(key) != -1){
                            if($("#hd_"+key).val() == 1){
                                that.field[key] = true;
                            }
                        }else{
                            that.field[key] = $("#hd_"+key).val();
                        }
                    }

                });
            }
            that.loading = false;
        },
        onFileChange:function(e,target) {
            this.field[target] = e.target.files[0].name;
            this.file[target]= e.target.files[0];
            $(".btnPreview"+target).hide();
        },
        deleteFileUpload: function (e,destination){
            $(".btnPreview"+destination).hide();
            this.file[destination]= '';
            this.$refs[destination].value = '';
            this.field[destination]="";
            if(this.field.deleteFile.indexOf(destination) ==-1){
                this.field.deleteFile.push(destination);
            }
        },
        copyText: function() {
            this.field.user_nm = this.field.owner_nm;
            this.field.user_address = this.field.owner_address;
        }
    },
    mounted () {
        this.loadFormEdit();
        this.getListStaff();
    },
    components: {
        DatePicker,
        PulseLoader,
        'modal-viewer-file':ModalViewerFile
    }
});
