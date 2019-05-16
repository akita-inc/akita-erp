
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

    'title' => 'ワークフロー一覧',
    'list' => [
        'title' => 'ワークフロー一覧',
        'search' => [
            'name'=>'名称',
            'btn_clear'=>'クリア',
            'btn_new_register'=>'新規登録'
        ],
        'table' => [
            'id'=>'ID',
            'name'=>'名称',
            'steps'=>'承認段階数',
        ]
    ],
    'create' => [
        'title_register' => 'ワークフロー登録',
        'title_edit' => 'ワークフロー-基本承認者設定',
        'title_advance_setting' => 'ワークフロー-承認者設定',
        'field' => [
            "name" => "基本",
            "steps" => "承認段階数 ",
            "approval_levels" => "段階",
            "approval_kb" => "承認区分",
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
    ]
];
