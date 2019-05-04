<?php
return[
    'title' => '請求書発行',
    'list' => [
        'title'=>'請求書発行',
        'search' => [
            'mst_business_office_id' => '営業所',
            'billing_year' => '請求年月',
            'customer' => '得意先',
            'code' => 'コード',
            'name' => '名称',
            'closed_date' => '締め日',
            'special_closing_date' => '特例締め日',
            'mst_business_office_id_default'=>'全社',
            'date_of_issue' => '発行日',
            'button' => [
                'issue' => '発行',
                'csv'=>'CSV',
                'export'=>'詳',
                'choose'=>'選択',
            ],
        ],
        'table' => [
            'regist_office' => '営業所',
            'customer_cd' => '得意先CD',
            'customer_nm' => '得意先名',
            'total_fee'=>'請求金額',
            'sale_tax'=>'消費税',
            'total'=>'合計金額',
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