<?php
Breadcrumbs::for('home', function ($trail) {
    $trail->push('システムタイトル', '/');
});

Breadcrumbs::for('sale', function ($trail) {
    $trail->parent('home');
    $trail->push('販売', '#');
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
    $trail->push('購買', '#');
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
    $trail->push('マスタ', '#');
});

Breadcrumbs::for('staffs_list', function ($trail) {
    $trail->parent('master');
    $trail->push('社員', route('staffs.list'));
});

Breadcrumbs::for('staffs_create', function ($trail) {
    $trail->parent('staffs_list');
    $trail->push('新規追加', route('staffs.create'));
});

Breadcrumbs::for('staffs_edit', function ($trail,$id) {
    $trail->parent('staffs_list');
    $trail->push('修正画面', route('staffs.edit',['id'=>$id]));
});

Breadcrumbs::for('suppliers_list', function ($trail) {
    $trail->parent('master');
    $trail->push('仕入先', route('suppliers.list'));
});

Breadcrumbs::for('suppliers_create', function ($trail) {
    $trail->parent('suppliers_list');
    $trail->push('新規追加', route('suppliers.create'));
});

Breadcrumbs::for('suppliers_edit', function ($trail,$id) {
    $trail->parent('suppliers_list');
    $trail->push('修正画面', route('suppliers.edit',['id' => $id]));
});

Breadcrumbs::for('customers_list', function ($trail) {
    $trail->parent('master');
    $trail->push('得意先', route('customers.list'));
});

Breadcrumbs::for('customers_create', function ($trail) {
    $trail->parent('customers_list');
    $trail->push('新規追加', route('customers.create'));
});

Breadcrumbs::for('customers_edit', function ($trail,$id) {
    $trail->parent('customers_list');
    $trail->push('修正画面', route('customers.edit',['id' => $id]));
});

Breadcrumbs::for('vehicles_list', function ($trail) {
    $trail->parent('master');
    $trail->push('車両', route('vehicles.list'));
});

Breadcrumbs::for('vehicles_create', function ($trail) {
    $trail->parent('vehicles_list');
    $trail->push('新規追加', route('vehicles.create'));
});

Breadcrumbs::for('vehicles_edit', function ($trail,$id) {
    $trail->parent('vehicles_list');
    $trail->push('修正画面', route('vehicles.edit',['id' => $id]));
});

Breadcrumbs::for('empty_info', function ($trail) {
    $trail->parent('home');
    $trail->push('ハコカラ', '#');
});

Breadcrumbs::for('empty_info_register', function ($trail) {
    $trail->parent('empty_info');
    $trail->push('新規登録', route('empty_info.create'));
});

Breadcrumbs::for('empty_info_edit', function ($trail,$id) {
    $trail->parent('empty_info');
    $trail->push('編集', route('empty_info.edit',['id' => $id]));
});

Breadcrumbs::for('empty_info_reservation', function ($trail,$id) {
    $trail->parent('empty_info');
    $trail->push('照会', route('empty_info.reservation',['id' => $id]));
});

Breadcrumbs::for('empty_info_reservation_approval', function ($trail,$id) {
    $trail->parent('empty_info');
    $trail->push('予約承認', route('empty_info.reservation_approval',['id' => $id]));
});

Breadcrumbs::for('work_flow', function ($trail) {
    $trail->parent('home');
    $trail->push('ワークフロー', '#');
});

Breadcrumbs::for('take_vacation_list', function ($trail) {
    $trail->parent('work_flow');
    $trail->push('休暇取得申請', route('take_vacation.list'));
});

Breadcrumbs::for('take_vacation_create', function ($trail) {
    $trail->parent('take_vacation_list');
    $trail->push('新規登録', route('take_vacation.create'));
});

Breadcrumbs::for('take_vacation_edit', function ($trail,$id) {
    $trail->parent('take_vacation_list');
    $trail->push('編集', route('take_vacation.edit',['id' => $id]));
});

Breadcrumbs::for('take_vacation_approval', function ($trail,$id) {
    $trail->parent('take_vacation_list');
    $trail->push('承認・却下', route('take_vacation.approval',['id' => $id]));
});

Breadcrumbs::for('take_vacation_reference', function ($trail,$id) {
    $trail->parent('take_vacation_list');
    $trail->push('照会', route('take_vacation.reference',['id' => $id]));
});

Breadcrumbs::for('basic_setting_list', function ($trail) {
    $trail->parent('work_flow');
    $trail->push('基本設定', route('work_flow.list'));
});

Breadcrumbs::for('basic_setting_create', function ($trail) {
    $trail->parent('basic_setting_list');
    $trail->push('新規登録', route('work_flow.create'));
});

Breadcrumbs::for('basic_setting_edit', function ($trail,$id) {
    $trail->parent('basic_setting_list');
    $trail->push('編集', route('work_flow.edit',['id' => $id]));
});

Breadcrumbs::for('expense_application_list', function ($trail) {
    $trail->parent('work_flow');
    $trail->push('交際費申請', route('expense_application.list'));
});

Breadcrumbs::for('expense_application_create', function ($trail) {
    $trail->parent('expense_application_list');
    $trail->push('新規登録', route('expense_application.create'));
});

Breadcrumbs::for('expense_application_edit', function ($trail,$id) {
    $trail->parent('expense_application_list');
    $trail->push('編集', route('expense_application.edit',['id' => $id]));
});

Breadcrumbs::for('expense_application_approval', function ($trail,$id) {
    $trail->parent('expense_application_list');
    $trail->push('承認・却下', route('expense_application.approval',['id' => $id]));
});

Breadcrumbs::for('expense_application_reference', function ($trail,$id) {
    $trail->parent('expense_application_list');
    $trail->push('照会', route('expense_application.reference',['id' => $id]));
});

Breadcrumbs::for('expense_entertainment_list', function ($trail) {
    $trail->parent('work_flow');
    $trail->push('交際費精算', route('expense_entertainment.list'));
});

Breadcrumbs::for('expense_entertainment_create', function ($trail) {
    $trail->parent('expense_entertainment_list');
    $trail->push('新規登録', route('expense_entertainment.create'));
});

Breadcrumbs::for('expense_entertainment_edit', function ($trail,$id) {
    $trail->parent('expense_entertainment_list');
    $trail->push('編集', route('expense_entertainment.edit',['id' => $id]));
});

Breadcrumbs::for('expense_entertainment_approval', function ($trail,$id) {
    $trail->parent('expense_entertainment_list');
    $trail->push('承認・却下', route('expense_entertainment.approval',['id' => $id]));
});

Breadcrumbs::for('expense_entertainment_reference', function ($trail,$id) {
    $trail->parent('expense_entertainment_list');
    $trail->push('照会', route('expense_entertainment.reference',['id' => $id]));
});