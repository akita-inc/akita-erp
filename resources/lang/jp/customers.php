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
        'search' => [
            'customer' => '得意先',
            'code' => 'コード',
            'name' => '名称',
            'name_kana' => '名称カナ',
            'radio-all' => 'すべて',
            'radio-reference-date' => '基準日',
        ],
        'table' => [
            'mst_customers_cd' => '得意先CD',
            'customer_nm' => '得意先名',
            'street_address' => '住所',
            'explanations_bill' => '請求に関する説明',
            'adhibition_start_dt' => '適用開始日',
            'adhibition_end_dt' => '適用終了日',
            'modified_at' => '更新日時',
        ]
    ],
    'create' => [
        'title' => '得意先　新規追加',
        'title_edit' => '得意先　修正',
        'mst_customers_cd_description' => '※編集中データをもとに、新しい適用期間のデータを作成したい場合は、適用開始日（新規用）を入力し、新規登録（履歴残し）ボタンを押してください。',
        'field' => [
            "mst_customers_cd" => "得意先コード",
            "adhibition_start_dt" => "適用開始日",
            "adhibition_end_dt" => "適用終了日",
            "adhibition_start_dt_edit" => "適用開始日（更新用）",
            "adhibition_end_dt_edit" => "適用終了日（更新用）",
            "adhibition_start_dt_history" => "適用開始日（新規用）",
            "adhibition_end_dt_history" => "適用終了日（新規用）",
            "customer_nm" => "得意先名略称",
            "customer_nm_kana" => "得意先略称カナ名",
            "customer_nm_formal" => "得意先正式名",
            "customer_nm_kana_formal" => "得意先正式カナ名",
            "person_in_charge_last_nm" => "担当者名(姓）",
            "person_in_charge_first_nm" => "担当者名(名）",
            "person_in_charge_last_nm_kana" => "担当者名カナ（姓）",
            "person_in_charge_first_nm_kana" => "担当者名カナ（名）",
            "zip_cd" => "郵便番号",
            "prefectures_cd" => "都道府県",
            "address1" => "市区町村",
            "address2" => "町名番地",
            "address3" => "建物等",
            "phone_number" => "電話番号",
            "fax_number" => "FAX番号",
            "hp_url" => "WEBサイトアドレス",
            "customer_category_id" => "得意先カテゴリ小区分",
            "prime_business_office_id" => "取得営業所",
            "explanations_bill" => "請求に関する説明",
            "bundle_dt" => "請求締日",
            "deposit_month_id" => "入金予定月",
            "deposit_day" => "入金予定日",
            "deposit_method_id" => "入金予定方法",
            "deposit_method_notes" => "入金予定補足説明",
            "business_start_dt" => "取引開始日",
            "consumption_tax_calc_unit_id" => "消費税計算単位区分",
            "rounding_method_id" => "消費税端数処理区分",
            "discount_rate" => "値引率（％）",
            "except_g_drive_bill_fg" => "請求書の発行有無",
            "deposit_bank_cd" => "入金銀行コード",
            "mst_account_titles_id" => "売上勘定科目（代表）",
            "mst_account_titles_id_2" => "売上勘定科目（第２）",
            "mst_account_titles_id_3" => "売上勘定科目（第３）",
            "notes" => "備考",
        ]
    ]

];
