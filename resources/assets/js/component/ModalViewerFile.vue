<template>
    <div style="margin-left: 5px;">
        <!-- Button to Open the Modal -->
        <a data-toggle="modal" v-bind:data-target="'#ModalViewerFile'+id" href="javascript:void(0);">
            <slot></slot>
        </a>

        <!-- The Modal -->
        <div class="modal" v-bind:id="'ModalViewerFile'+id">
            <div class="modal-dialog" style="max-width: 960px;">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{header}}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                        <vue-pdf-viewer v-if="typeFile == 'pdf'" height="calc(100vh - 170px)" :url="path"></vue-pdf-viewer>
                        <img style="width: 100%;" v-else-if="typeFile == 'image'" v-bind:src="path"/>
                        <a v-else target="_blank" v-bind:href="path">{{path}}</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import VuePDFViewer from 'vue-instant-pdf-viewer'
    export default {
        name: 'ModalViewerFile',
        props: {
            header:String,
            path:String
        },
        components: {
            'vue-pdf-viewer': VuePDFViewer,

        },
        data () {
            return {
                typeFile:"",
                id: Date.now()
            }
        },
        methods : {
            checkFileType () {
                let ext = this.path.split('.');
                ext = ext[ext.length-1].toLowerCase();
                let arrayExtensionsImages = ['jpg' , 'jpeg', 'png', 'bmp', 'gif'];
                if (arrayExtensionsImages.lastIndexOf(ext) != -1) {
                    this.typeFile="image";
                }
                else if(ext == "pdf") {
                    this.typeFile="pdf";
                }else{
                    this.typeFile="link";
                }
            }
        },
        mounted () {
            this.checkFileType();
        }
    }
</script>
