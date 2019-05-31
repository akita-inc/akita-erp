
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
    ]

];
