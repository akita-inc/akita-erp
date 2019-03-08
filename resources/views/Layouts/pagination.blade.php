<div v-cloak v-if="items.length > 0" class="block-item">
    全@{{ pagination.last_page }}ページ：@{{pagination.current_page}}ページ目　　全@{{ pagination.total }}件：@{{ items.length }}件表示
</div>

<div v-cloak v-if="items.length > 0" class="block-item">
    <nav aria-label="Page navigation">
        <ul class="pagination-custom mg-b-0">
            <li v-if="pagination.current_page > 1">
                <a href="javascript:void(0);" aria-label="Previous"
                   @click.prevent="changePage(pagination.current_page - 1)">
                    ◀◀前
                </a>
            </li>
            <li v-if="pagination.last_page > 1" v-for="page in pagination.last_page"
                v-bind:class="[ page == pagination.current_page ? 'active' : '']">
                <a href="javascript:void(0);"
                   @click.prevent="changePage(page)">@{{ page }}</a>
            </li>
            <li v-if="pagination.current_page < pagination.last_page">
                <a href="javascript:void(0);" aria-label="Next"
                   @click.prevent="changePage(pagination.current_page + 1)">
                    後▶▶
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </nav>
</div>
<div class="clearfix"></div>
