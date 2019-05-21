
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

    'title' => '休暇取得申請',
    'list' => [
        'title' => '休暇取得申請',
        'search' => [
            'btn_new_register'=>'新規登録',
            'applicant_id'=>'申請者ID',
            'applicant'=>'申請者',
            'show_approved' =>'承認済も表示',
            'show_deleted'=>'削除済も表示',
            'sales_office'=>'営業所',
            'vacation_class'=>'休暇区分'

        ],
        'table' => [
            'id'=>'ID',
            'applicant_date'=>'申請日',
            'applicant_nm'=>'申請者',
            'sales_office'=>'営業所',
            'vacation_class'=>'休暇区分',
            'period'=>'期間',
            'days'=>'日数',
            'approval_status'=>'承認ステータス',
        ]
    ],
    'create' =>[
        'title' => '休暇取得申請　新規追加',
        'title_edit' => '休暇取得申請　修正',
    ]
];
