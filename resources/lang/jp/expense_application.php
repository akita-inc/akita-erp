
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

    'title' => '交際費支出申請',
    'list' => [
        'title' => '交際費支出申請',
        'search' => [
            'applicant_id'=>'申請者ID',
            'applicant_nm'=>'申請者',
            'applicant_office_id'=>'営業所',
            'client_company_name'=>'相手先',
            'show_status'=>'承認済も表示',
            'show_deleted'=>'削除済も表示',
            'no_data'=>'データがありません',
            'deleted'=>'削除しました'
        ],
        'table' => [
            'id'=>'ID',
            'regist_date'=>'申請日',
            'applicant_nm'=>'申請者',
            'applicant_office_id'=>'営業所',
            'date'=>'実施日',
            'client_company_name'=>'相手先',
            'participant_amount'=>'参加人数（得意先/自社）',
            'cost'=>'概算費用',
            'approval_status'=>'承認ステータス'
        ]
    ],
    'create' =>[
        'title_register' => '交際費支出申請 登録',
        'title_edit' => '交際費支出申請 編集',
        'title_approval' => '交際費支出申請 承認・却下',
        'title_reference' => '交際費支出申請 照会 ',
        'field' => [
            'applicant_id' => '申請者',
            'applicant_office_nm' => '所属営業所',
            'staff_nm' => '',
            'date' => '実施日',
            'cost'=>'概算費用',
            'client_company_name'=>'相手先会社名',
            'client_members_count'=>'相手先参加者',
            'client_members'=>'',
            'own_members_count'=>'当社参加者',
            'own_members'=>'',
            'place'=>'場所',
            'conditions'=>'取引状況',
            'purpose'=>'目的',
            'deposit_flg'=>'仮払い',
            'deposit_amount'=>'仮払い金額',
            'email_address'=>'追加通知',

        ],
        'search' => [

        ],
        'modal' =>[
            'table'=>[

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
