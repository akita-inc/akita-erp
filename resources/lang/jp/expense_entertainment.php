
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

    'title' => '交際費精算',
    'list' => [
        'title' => '交際費精算',
        'search' => [
            'btn_new_register'=>'新規登録',
            'applicant_id'=>'申請者ID',
            'applicant'=>'申請者',
            'show_status' =>'承認済も表示',
            'show_deleted'=>'削除済も表示',
            'applicant_office_id'=>'営業所',
            'client_company_name'=>'相手先'

        ],
        'table' => [
            'id'=>'ID',
            'applicant_date'=>'申請日',
            'applicant_nm'=>'申請者',
            'applicant_office_id'=>'営業所',
            'date'=>'実施日',
            'client_company_name'=>'相手先',
            'participant_amount'=>'参加人数',
            'cost'=>'精算額',
            'approval_status'=>'承認ステータス',
        ]
    ],
    'create' =>[
        'title_register' => '交際費精算 登録',
        'title_edit' => '交際費精算 編集',
        'title_approval' => '交際費精算 承認・却下',
        'title_reference' => '交際費精算 照会 ',
        'field' => [
            'applicant_id' => '申請者',
            'staff_nm' => '',
            'applicant_office_nm' => '所属営業所',
            'approval_kb' => '休暇区分',
            'half_day_kb' => '時間区分',
            'start_date' => '期間',
            'end_date' => '',
            'days' => '日数',
            'times' => '',
            'reasons' => '理由',
            'additional_notice' => '追加通知',
            'send_back_reason' => '却下理由',
        ],
        'search' => [
            'name' => '氏名 / ふりがな',
            'mst_business_office_id' => '営業所',
        ],
        'modal' =>[
            'table'=>[
                'staff_nm' => '氏名',
                'business_office_nm' => '営業所',
                'mail' => 'メールアドレス',
            ]
        ]
    ],
    'modal' => [
        'register'=>[
            'title' => '登録',
            'content' => \Illuminate\Support\Facades\Lang::get('messages.MSG10025')
        ],
        'edit'=>[
            'title' => '登録',
            'content' => \Illuminate\Support\Facades\Lang::get('messages.MSG10025')
        ],
        'approval'=>[
            'title' => '承認',
            'content' => \Illuminate\Support\Facades\Lang::get('messages.MSG10016')
        ],
        'reject'=>[
            'title' => '却下',
            'content' => \Illuminate\Support\Facades\Lang::get('messages.MSG10019')
        ],
    ]
];
