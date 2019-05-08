<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'title' => '支払締処理',
    'list' => [
        'search' => [
            'sales-office' => '営業所',
            'all-office' => '全社',
            'billing-date' => '請求年月',
            'year' => '年',
            'month' => '月',
            'supplier' => '仕入先',
            'code' => 'コード',
            'name' => '名称',
            'closing-date' => '締め日',
            'day' => '日',
            'button' => [
                'execution' => '締め実行'
            ]
        ],
        'table' => [
            'business_office_nm' => '営業所',
            'mst_suppliers_cd' => '仕入先CD',
            'supplier_nm_formal' => '仕入先名',
            'billing_amount' => '請求金額',
            'consumption_tax' => '消費税',
            'total_amount' => '合計金額',
            'button' => [
                'detail' => '詳細'
            ]
        ],
    ],
    'modal' => [
        'title'=>'請求詳細',
        'sub_title'=>'明細',
        'table' => [
            'daily_report_date_formatted' => '日付',
            'departure_point_name' => '発地',
            'landing_name' => '着地',
            'total_fee'=>'請求金額',
            'consumption_tax'=>'消費税',
            'tax_included_amount'=>'合計金額',
        ]
    ]
];