
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

    'title' => '入金履歴',
    'list' => [
        'title' => '入金履歴',
        'search' => [
            'customer'=>'得意先',
            'code'=>'コード',
            'name'=>'名称',
            'customer_nm'=>'名称',
            'payment_date'=>'入金日',
            'button' => [
                'detail'=>'詳細',
                'close'=>'閉じる',
                'delete'=>'削除'
            ],
            'no_data'=>'データがありません',
            'deleted'=>'削除しました'
        ],
        'table' => [
          'dw_day'=>'入金日',
          'mst_customers_cd'=>'得意先CD',
          'customer_nm'=>'得意先名',
          'dw_classification'=>'入金区分',
          'actual_dw'=>'入金額',
          'fee'=>'手数料',
          'discount'=>'値引き',
          'total_dw_amount'=>'合計入金額',
          'note'=>'備考'
        ]
    ],
    'modal' => [
        'title'=>'入金明細',
        'table' => [
            'invoice_number' => '請求書No.',
            'publication_date' => '発行日',
            'total_fee'=>'請求金額',
            'consumption_tax'=>'消費税',
            'tax_included_amount'=>'合計金額',
            'last_payment_amount'=>'前回入金額',
            'total_dw_amount'=>'入金額',
            'fee'=>'手数料',
            'discount'=>'値引き',
            'deposit_balance'=>'入金残'
        ],
    ]

];
