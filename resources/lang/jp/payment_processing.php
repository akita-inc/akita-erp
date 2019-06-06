<?php
return[
    'title' => '入金処理',
    'list' => [
        'title'=>'入金処理',
        'search' => [
            'customer' => '得意先',
            'code' => 'コード',
            'name' => '名称',
            'no_data'=>'データがありません。',
            'register_success'=>'入金登録しました。',
            'register_error'=>'入金登録処理に失敗しました。',
        ],
        'table' => [
            'invoice_number' => '請求書No.',
            'publication_date'=> '発行日',
            'office_nm'=> '営業所',
            'total_fee'=> '請求金額',
            'consumption_tax'=> '消費税',
            'tax_included_amount'=> '合計金額',
            'last_payment_amount'=> '前回入金額',
            'total_dw_amount'=> '入金額',
            'fee'=> '手数料',
            'discount'=> '値引き',
            'payment_remaining'=> '入金残',
        ],
        'field' => [
            'dw_day' => '入金日',
            'invoice_balance_total' => '請求残合計',
            'dw_classification' => '入金区分',
            'payment_amount' => '入金額',
            'fee' => '手数料',
            'total_discount' => '値引き',
            'total_payment_amount' => '入金額合計',
            'item_payment_total' => '明細入金合計',
            'note' => '備考',
        ]
    ],
];