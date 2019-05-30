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
        ],
        'table' => [
            'regist_office' => '営業所',
            'customer_cd' => '得意先CD',
            'customer_nm' => '得意先名',
            'total_fee'=>'請求金額',
            'consumption_tax'=>'消費税',
            'tax_included_amount'=>'合計金額',
        ],
        'field' => [
            'dw_day' => '入金日',
            'invoice_balance_total' => '請求残合計',
            'dw_classification' => '入金区分',
            'payment_amount' => '入金額',
            'fee' => '手数料',
            'discount' => '値引き',
            'total_payment_amount' => '入金額合計',
            'item_payment_total' => '明細入金合計',
            'note' => '備考',
        ]
    ],
];