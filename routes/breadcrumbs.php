<?php
Breadcrumbs::for('home', function ($trail) {
    $trail->push('システムタイトル', '/');
});

Breadcrumbs::for('sale', function ($trail) {
    $trail->parent('home');
    $trail->push('販売', '');
});

Breadcrumbs::for('sales_lists', function ($trail) {
    $trail->parent('sale');
    $trail->push('売上一覧', route('sales_lists.list'));
});

Breadcrumbs::for('invoices', function ($trail) {
    $trail->parent('sale');
    $trail->push('請求書発行', route('invoices.list'));
});

Breadcrumbs::for('invoice_history', function ($trail) {
    $trail->parent('sale');
    $trail->push('請求書発行履歴', route('invoice_history.list'));
});

Breadcrumbs::for('payment_processing', function ($trail) {
    $trail->parent('sale');
    $trail->push('入金処理', route('payment_processing.list'));
});

Breadcrumbs::for('payment_histories', function ($trail) {
    $trail->parent('sale');
    $trail->push('入金履歴', route('payment_histories.list'));
});

Breadcrumbs::for('buy', function ($trail) {
    $trail->parent('home');
    $trail->push('購買', '');
});

Breadcrumbs::for('payments', function ($trail) {
    $trail->parent('buy');
    $trail->push('支払締処理', route('payments.list'));
});

Breadcrumbs::for('purchases_lists', function ($trail) {
    $trail->parent('buy');
    $trail->push('仕入一覧', route('purchases_lists.list'));
});

Breadcrumbs::for('accounts_payable_data_output', function ($trail) {
    $trail->parent('buy');
    $trail->push('買掛データ出力', route('accounts_payable_data_output.list'));
});

Breadcrumbs::for('master', function ($trail) {
    $trail->parent('home');
    $trail->push('マスタ', '');
});

Breadcrumbs::for('staffs_list', function ($trail) {
    $trail->parent('master');
    $trail->push('社員', route('staffs.list'));
});

Breadcrumbs::for('suppliers_list', function ($trail) {
    $trail->parent('master');
    $trail->push('仕入先', route('suppliers.list'));
});

Breadcrumbs::for('suppliers_create', function ($trail) {
    $trail->parent('master');
    $trail->push('仕入先', route('suppliers.create'));
});


Breadcrumbs::for('customers_list', function ($trail) {
    $trail->parent('master');
    $trail->push('得意先', route('customers.list'));
});

Breadcrumbs::for('vehicles_list', function ($trail) {
    $trail->parent('master');
    $trail->push('車両', route('vehicles.list'));
});

Breadcrumbs::for('empty_info', function ($trail) {
    $trail->parent('home');
    $trail->push('ハコカラ', '');
});

Breadcrumbs::for('work_flow_list', function ($trail) {
    $trail->parent('home');
    $trail->push('ワークフロー', '');
});

Breadcrumbs::for('take_vacation_list', function ($trail) {
    $trail->parent('work_flow');
    $trail->push('休暇取得申請', route('take_vacation.list'));
});

Breadcrumbs::for('basic_setting_list', function ($trail) {
    $trail->parent('work_flow');
    $trail->push('得意先', route('work_flow.list'));
});

Breadcrumbs::for('expense_application_list', function ($trail) {
    $trail->parent('work_flow');
    $trail->push('交際費申請', route('expense_application.list'));
});

Breadcrumbs::for('expense_entertainment_list', function ($trail) {
    $trail->parent('work_flow');
    $trail->push('交際費精算', route('expense_entertainment.list'));
});