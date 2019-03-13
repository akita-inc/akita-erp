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

    'title' => '社員　検索リスト',
    'list' => [
        'search' => [
            'radio-all' => 'すべて',
            'radio-reference-date' => '基準日',
            'staff'=>'社員',
            'code' => 'コード',
            'staff_nm' => '氏名',
            'belong_company_id'=>'所属会社',
            'mst_business_office_id'=>'営業所',
            'employment_pattern_id'=>'雇用形態',
            'position_id'=>'役職',
        ],
        'table' => [
            'staff_cd'=>'社員コード',
            'employment_pattern_nm'=>'雇用形態',
            'position_nm'=>'役職',
            'staff_nm' => '氏名',
            'belong_company_nm'=>'所属会社',
            'business_office_nm'=>'営業所',
            'adhibition_start_dt'=>'適用開始日',
            'adhibition_end_dt'=>'適用終了日',
            'modified_at'=>'更新日時',
            'delete'=>'',
        ]
    ],
    'create' => [
        'title' => '社員　新規追加',
        'field' => [
            "staff_cd" => "社員コード",
            "adhibition_start_dt" => "適用開始日",
            "adhibition_end_dt" => "適用終了日",
            "password"=>"パスワード",
            "employment_pattern_id"=>"雇用形態",
            "position_id"=>"役職",
            "last_nm"=>"社員名（姓）",
            "first_nm"=>"社員カナ名（姓）",
            "last_nm_kana"=>"社員名（名）",
            "first_nm_kana"=>"社員カナ名（名）",
            "zip_cd"=>"郵便番号",
            "prefectures_cd"=>"都道府県",
            "address1"=>"市区町村",
            "address2"=>"町名番地",
            "address3"=>"建物等",
            "landline_phone_number"=>"固定電話番号",
            "cellular_phone_number"=>"携帯電話番号",
            "corp_cellular_phone_number"=>"支給携帯電話番号",
            "notes"=>"備考",
            "sex_id"=>"性別",
            "birthday"=>"生年月日",
            "enter_date"=>"入社年月日",
            "retire_date"=>"退社年月日",
            "insurer_number"=>"保険番号",
            "basic_pension_number"=>"基礎年金番号",
            "person_insured_number"=>"被保険者番号",
            "health_insurance_class"=>"健康保険等級",
            "welfare_annuity_class"=>"厚生年金等級",
            "relocation_municipal_office_cd"=>"市町村役場コード",
            "educational_background"=>"最終学歴",
            "educational_background_dt"=>"最終学歴日付",
            "job_duties"=>"職務内容",
            "staff_tenure_start_dt"=>"在職期間（開始日）",
            "staff_tenure_end_dt"=>"在職期間（終了日）",
            "disp_number"=>"'表示順",
            "qualification_kind_id"=>"資格種類",
            "acquisition_dt"=>"資格取得日",
            "period_validity_start_dt"=>"有効期限（開始日）",
            "period_validity_end_dt"=>"有効期限（終了日）",
            "qualification_notes"=>"資格に対する備考",
            "amounts"=>"資格取得補助金の金額",
            "payday"=>"支払日",

            "dependent_kb"=>"扶養区分",
            "mst_staff_dependents"=> [
                "last_nm"=>"扶養者名（姓）",
                "last_nm_kana"=>"扶養者カナ名（姓）",
                "first_nm"=>"扶養者名（名）",
                "first_nm_kana"=>"扶養者カナ名（名）",
                "birthday"=>"生年月日",
                "sex_id"=>"性別",
                "social_security_number"=>"年金番号",
            ],
            "mst_staff"=>[

            ]
        ]
    ]
];
