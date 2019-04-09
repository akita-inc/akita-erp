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

    'title' => '得意先　検索リスト',
    'list' => [
        'title'=>'空車照会',
        'search' => [
            'regist_office_id' => '営業所',
            'vehicle_size' => '車格',
            'vehicle_body_shape' => '形状',
            'asking_baggage' => '希望荷物',
            'equipment' => '搭載物',
            'start_pref_cd'=>'空車場所',
            'arrive_pref_cd'=>'到着予定地',
            'pref'=>'▼都道府県',
            'status'=>'マッチング済も表示',
            'arrive_date'=>'過去データも表示',
            'edit'=>'編集',
            'choose'=>'選択',
            'button'=>[
                'inquiry'=>'照会',//「照会・仮押さえ」
                'select'=>'選択',//「照会・仮押さえ」
                'edit'=>'編集',//空車登録（編集）
                'approve'=>'承認' //「予約承認」
            ]
        ],
        'table' => [
            'regist_office' => '営業所',
            'vehicle_classification' => '車両区分',
            'registration_numbers'=>'車番',
            'vehicle_size'=>'車格',
            'vehicle_body_shape'=>'形状',
            'max_load_capacity'=>'最大積載量',
            'equipment'=>'搭載物',
            'schedule_date'=>'空車予定日時',
            'start_pref_cd'=>'空車場所',
            'asking_price'=>'希望運賃',
            'asking_baggage'=>'希望荷物',
            'arrive_location'=>'到着予定地',
            'arrive_date'=>'到着日'
        ]
    ],
    'create' => [
        'title_register' => '空車登録',
        'title_edit' => '空車登録（編集）',
        'title_reservation' => '空車登録（照会）',
        'title_reservation_approval' => '空車登録（ 承認・却下）',
        'field' => [
            "status" => "ステータス",
            "regist_office_id" => "営業所",
            "email_address" => "ナンバー検索",
            "vehicle_kb" => "車両区分",
            "registration_numbers" => "車番",
            "vehicle_size" => "車格",
            "vehicle_body_shape" => "形状",
            "max_load_capacity" => "最大積載量",
            "equipment" => "搭載物",
            "start_date" => "空車予定日時",
            "start_time" => "",
            "start_pref_cd" => "空車場所",
            "start_address" => "",
            "asking_price" => "希望運賃",
            "asking_baggage" => "希望荷物",
            "arrive_pref_cd" => "到着予定地",
            "arrive_address" => "",
            "arrive_date" => "到着日",
            "ask_date" => "予約日時",
            "ask_office" => "予約営業所",
            "ask_staff" => "予約担当者",
            "ask_staff_email_address" => "予約担当者メールアドレス",
            "apr_date" => "承認日時",
            "search_vehicle" => "ナンバー検索",
            "application_office_id" => "申請営業所",
            "reservation_person" => "予約担当者",
        ]
    ],
    'modal' => [
        'reservation'=>[
            'title' => '仮押さえ',
            'content' => \Illuminate\Support\Facades\Lang::get('messages.MSG10013')
        ],
        'reservation_approval'=>[
            'title' => '承認',
            'content' => \Illuminate\Support\Facades\Lang::get('messages.MSG10016')
        ],
        'reservation_reject'=>[
            'title' => '却下',
            'content' => \Illuminate\Support\Facades\Lang::get('messages.MSG10019')
        ],
    ]

];
