<?php
return[
    'title' => '請求書発行履歴',
    'list' => [
        'title'=>'請求書発行履歴',
        'search' => [
            'mst_business_office_id' => '営業所',
            'billing_year' => '請求日',
            'customer' => '得意先',
            'code' => 'コード',
            'name' => '名称',
            'mst_business_office_id_default'=>'全社',
            'date_of_issue' => '発行日',
            'display_remaining_payment' => '入金残があるもののみ表示',
            'button' => [
                'issue' => '再発行',
                'csv'=>'再CSV',
                'export'=>'詳細',
                'choose'=>'選択',
                'amazonCSV'=>'再Amazon',
            ],
            'no_data'=>'データがありません。',
        ],
        'table' => [
            'regist_office' => '営業所',
            'customer_cd' => '得意先CD',
            'customer_nm' => '得意先名',
            'invoice_number' => '請求書No.',
            'publication_date' => '発行日',
            'total_fee'=>'請求金額',
            'consumption_tax'=>'消費税',
            'tax_included_amount'=>'合計金額',
            'payment'=>'入金',
            'payment_amount'=>'入金額',
            'payment_remaining'=>'入金残',
        ],
    ],
    'modal' => [
        'title'=>'請求詳細',
        'sub_title'=>'明細',
        'table' => [
            'daily_report_date' => '日付',
            'departure_point_name' => '発地',
            'landing_name' => '着地',
            'total_fee'=>'請求金額',
            'consumption_tax'=>'消費税',
            'tax_included_amount'=>'合計金額',
        ]
    ]
];