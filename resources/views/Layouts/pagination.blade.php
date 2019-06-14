<pagination :data="items" @pagination-change-page="getItems" :limit="5" :align="'center'" :show-disabled="true" v-cloak>
    <span slot="prev-nav">〈</span>
    <span slot="next-nav">〉</span>
</pagination>