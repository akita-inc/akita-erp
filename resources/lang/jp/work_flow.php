
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
        'title_step_1' => 'ワークフロー登録',
        'title_step_2' => 'ワークフロー-基本承認者設定',
        'title_step_3' => 'ワークフロー-承認者設定',
        'field' => [
            "name" => "名称",
            "steps" => "承認段階数 ",
            "approval_levels" => "段階",
            "approval_kb" => "承認区分",
        ],
        'button' => [
            "next" => '次へ',
            "clear" => 'クリア',
        ]
    ]
];
