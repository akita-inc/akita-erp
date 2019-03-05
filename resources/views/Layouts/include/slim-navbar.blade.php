<div class="slim-navbar">
    <div class="container">
        @if(Auth::user()->agency_id == 1)
            <ul class="nav">
            <li class="nav-item {{ Route::currentRouteName() == "admin.dashboard" ? 'active': ''}}">
                <a class="nav-link" href="{{route("admin.dashboard")}}">
                    <i class="icon ion-ios-home-outline"></i>
                    <span>Tổng Quan</span>
                </a>
            </li>
            <li class="nav-item with-sub {{
            Request::is('*merchandise*')
            || Request::is('*category*')
            || Request::is('*product-option*')
            || Request::is('*in-stock*')
            || Request::is('*preset-find-product*')
            || Request::is('*inventory*') ? 'active': ''
            }}">
                <a class="nav-link" href="#">
                    <i class="icon ion-social-buffer-outline"></i>
                    <span>Hàng Hóa</span>
                </a>
                <div class="sub-item">
                    <ul>
                        <li>
                            <a class="{{ Request::is('*merchandise*')? 'active': '' }}" href="{{route("admin.merchandise")}}">
                            <i class="icon ion-ios-pricetag-outline"></i> Danh mục hàng hóa</a>
                        </li>
                        <li>
                            <a class="{{ Request::is('*category*')? 'active': '' }}" href="{{route("admin.category")}}">
                                <i class="icon ion-ios-pricetags-outline"></i> Quản lý Nhóm hàng</a>
                        </li>
                        <li>
                            <a class="{{ Request::is('*product-option*')? 'active': '' }}" href="{{route("admin.productoption")}}">
                                <i class="icon ion-ios-plus-outline"></i> Quản lý Thuộc tính</a>
                        </li>
                        <li>
                            <a class="{{ Request::is('*preset-find-product*')? 'active': '' }}" href="{{route("admin.presetfindproduct")}}">
                                <i class="icon ion-ios-plus-outline"></i> Quản lý Tìm Size</a>
                        </li>
                        <li><a class="{{ Request::is('*in-stock*') || Request::is('*inventory*')? 'active': '' }}" href="{{route("admin.inventory")}}">
                                <i class="icon ion-android-checkmark-circle"></i> Kiểm kho</a></li>
                    </ul>
                </div><!-- dropdown-menu -->
            </li>
            <li class="nav-item with-sub {{
            Request::is('*voucher*')
            || Request::is('*using-card-discount*')
            || Request::is('*set-price*') ? 'active': ''
            }}">
                <a class="nav-link" href="#">
                    <i class="icon ion-toggle"></i>
                    <span>Khuyến mãi</span>
                </a>
                <div class="sub-item">
                    <ul>
                        <li>
                            <a class="{{ Request::is('*voucher*')? 'active': '' }}" href="{{route("admin.voucher")}}">
                                <i class="icon ion-ios-plus-outline"></i> Voucher</a>
                        </li>
                        <li>
                            <a class="{{ Request::is('*set-price*')? 'active': '' }}" href="{{route("admin.setprice")}}">
                                <i class="icon ion-ios-plus-outline"></i> Giảm giá</a>
                        </li>
                        <li>
                            <a class="{{ Request::is('*using-card-discount*')? 'active': '' }}" href="{{route("admin.usingcarddiscount")}}">
                                <i class="icon ion-ios-plus-outline"></i> Giảm số lần thẻ thành viên</a>
                        </li>
                    </ul>
                </div><!-- dropdown-menu -->
            </li>
            <li class="nav-item with-sub {{
            Request::is('*import-product*') ||
            Request::is('*ship-payment*') ||
            Request::is('*orders*') ||
            Request::is('*returns-product*') ||
            Request::is('*order-detail*') ||
            Request::is('*import-product*') ? 'active': ''}}">
                <a class="nav-link" href="#">
                    <i class="icon ion-ios-analytics-outline"></i>
                    <span>Giao Dịch</span>
                </a>
                <div class="sub-item">
                    <ul>
                        <li><a class="{{ Request::is('*orders*') || Request::is('*order-detail*') ? 'active': '' }}" href="{{route("admin.orders")}}"><i class="icon ion-ios-paper-outline"></i> Đơn hàng online</a></li>
                        <li><a class="{{ Request::is('*returns-product*')? 'active': '' }}" href="{{route("admin.returnsproduct")}}"><i class="icon ion-ios-paper"></i> Trả hàng</a></li>
                        <li><a class="{{ Request::is('*import-product*')? 'active': '' }}" href="{{route("admin.importproduct")}}"><i class="icon ion-ios-download-outline"></i> Nhập hàng</a></li>
                        <li><a class="{{ Request::is('*ship-payment*')? 'active': '' }}" href="{{route("admin.shippayment")}}"><i class="icon ion-android-send"></i> Thiết lập phí vận chuyển</a></li>
                    </ul>
                </div><!-- dropdown-menu -->
            </li>
            <li class="nav-item with-sub {{ Request::is('*customer*') || Request::is('*supplier*') ? 'active': ''}}" >
                <a class="nav-link" href="#">
                    <i class="icon ion-ios-people-outline"></i>
                    <span>Đối Tác</span>
                </a>
                <div class="sub-item">
                    <ul>
                        <li><a class="{{ Request::is('*admin/customer*') ? 'active': ''}}" href="{{route("admin.customer.list")}}"><i class="icon ion-person-stalker"></i> Khách hàng</a></li>
                        <li><a class="{{ Request::is('*admin/supplier*') ? 'active': ''}}" href="{{route("admin.supplier")}}"><i class="icon ion-ribbon-b"></i> Nhà cung cấp</a></li>
                    </ul>
                </div><!-- dropdown-menu -->
            </li>
            <li class="nav-item with-sub {{ Request::is('*report*') ? 'active': ''}}">
                <a class="nav-link" href="#">
                    <i class="icon ion-ios-pie-outline"></i>
                    <span>Báo Cáo</span>
                </a>
                <div class="sub-item">
                    <ul>
                        <li><a class="{{ Request::is('*admin/report/sell*') ? 'active': ''}}" href="{{route("report.sell")}}"><i class="icon ion-ios-bolt-outline"></i> Bán hàng</a></li>
                        <li><a class="{{ Request::is('*admin/report/merchandise*') ? 'active': ''}}" href="{{route("report.merchandise")}}"><i class="icon ion-ios-list-outline"></i> Hàng hóa</a></li>
                        <li><a {{ Request::is('*admin/report/cus*') ? 'active': ''}} href="{{route("report.customer")}}"><i class="icon ion-person-stalker"></i> Khách hàng</a></li>
                    </ul>
                </div><!-- dropdown-menu -->
            </li>
            <li class="nav-item with-sub {{
            Request::is('*slider*') ||
            Request::is('*feature*') ||
            Request::is('*news*')||
            Request::is('*notification-expo*')||
            Request::is('*contact/list*')? 'active': ''}}">
                <a class="nav-link" href="#">
                    <i class="icon ion-ios-world-outline"></i>
                    <span>Online</span>
                </a>
                <div class="sub-item">
                    <ul>
                        <li><a class="{{ Request::is('*notification-expo*') ? 'active': ''}}" href="{{route('admin.notificationexpo')}}"><i class="icon ion-ios-bell-outline"></i> Thông báo APP</a></li>
                        <li><a class="{{ Request::is('*feature*') ? 'active': ''}}" href="{{route('admin.features')}}"><i class="icon ion-ios-pricetags-outline"></i> Quản lý feature</a></li>
                        <!--<li><a href="page-404.html"><i class="icon ion-ios-chatboxes-outline"></i> Tư vấn khách hàng</a></li>-->
                        <li><a class="{{ Request::is('*news*') ? 'active': ''}}"  href="{{route('news.list')}}"><i class="icon ion-ios-bookmarks-outline"></i> Quản lý tin tức</a></li>
                        <li><a class="{{ Request::is('*slider*') ? 'active': ''}}"  href="{{route('slider.list')}}"><i class="icon ion-ios-bookmarks-outline"></i> Quản lý Slider</a></li>
                        <li><a class="{{ Request::is('*contact/list*') ? 'active': ''}}"  href="{{route('contact.list')}}"><i class="icon ion-ios-gear-outline"></i> Liên hệ nhận bảng tin</a></li>
                    </ul>
                </div><!-- dropdown-menu -->
            </li>
            <li class="nav-item with-sub {{
            Request::is('*list-province*')
            || Request::is('*/term*')
            || Request::is('*admin/setting*')
            || Request::is('*payment-method*')
            || Request::is('*admin/user*')
            || Request::is('*agency*')
            || Request::is('*list-district*') ? 'active': ''}}">
                <a class="nav-link" href="javascript:void(0)">
                    <i class="icon ion-ios-settings"></i>
                    <span>Thiết lập</span>
                </a>
                <div class="sub-item">
                    <ul>
                        <li><a class="{{ Request::is('*admin/setting*') ? 'active': ''}}" href="{{route('setting.list')}}"><i class="icon ion-ios-gear-outline"></i> Quản lý website</a></li>
                        <li><a class="{{ Request::is('*admin/user*') ? 'active': ''}}" href="{{route('user-admin.list')}}"><i class="icon ion-person-stalker"></i> Quản lý người dùng</a></li>
                        <li><a class="{{ Request::is('*agency*') ? 'active': ''}}" href="{{route('agency.list')}}"><i class="icon ion-ios-color-filter-outline"></i> Quản lý chi nhánh</a></li>
                        <li><a class="{{ Request::is('*payment-method*') ? 'active': ''}}"  href="{{route('payment-method.list')}}"><i class="icon ion-cash"></i> Hình thức thanh toán</a></li>
                        <li><a class="{{ Request::is('*admin/card*') ? 'active': ''}}" href="{{route('card.list')}}"><i class="icon ion-card"></i> Quản lý thẻ thành viên</a></li>
                        <!--<li><a href="page-404.html"><i class="icon ion-bag"></i> Kênh bán hàng</a></li>
                        <li><a href="page-404.html"><i class="icon ion-ios-plus-outline"></i> Tích điểm</a></li>
                        <li><a href="page-404.html"><i class="icon ion-ios-paper-outline"></i> Biểu mẫu in hóa đơn</a>-->
                        <li><a class="{{ Request::is('*/term*') ? 'active': ''}}" href="{{route('term.list')}}"><i class="icon ion-ios-list-outline"></i> Nội dung Website</a></li>
                        <li>
                            <a class="{{ Request::is('*list-province*') ? 'active': ''}}" href="{{route('admin.listprovince')}}">
                                <i class="icon ion-ios-list-outline"></i> Quản lý tỉnh / thành phố
                            </a>
                        </li>
                        <li>
                            <a class="{{ Request::is('*list-district*') ? 'active': ''}}" href="{{route('admin.listdistrict')}}">
                                <i class="icon ion-ios-list-outline"></i> Quản lý quận / huyện
                            </a>
                        </li>
                    </ul>
                </div><!-- dropdown-menu -->
            </li>
        </ul>
        @endif
    </div><!-- container -->
</div><!-- slim-navbar -->
