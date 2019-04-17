<?php
return [
    'page_size' => 10,
    'data_kb' => [
        'sex'                           => '01001', // 性別ID
        'prefecture_cd'                 => '01002', // 都道府県CD
        'employment_pattern'            => '01003', // 雇用形態ID
        'belong_company'                => '01004', // 所属会社ID
        'occupation'                    => '01005', // 職種ID
        'department'                    => '01006', // 部門ID
        'position'                      => '01007', // 役職ID
        'relocation_municipal_office_cd'=> '01008', // 市町村役場コード
        'drivers_license_color'         => '01009', // 運転免許証の色ID
        'drivers_license_divisions_kb'  => '01010', // 運転免許証の区分1
        'medical_checkup_interval'      => '01011',//健康診断の間隔ID
        'dependent_kb'                  => '02001', // 扶養区分
        'qualification_kind'            => '03001', // 資格種類ID
        'vehicles_kb'                   => '04001', // 車両区分
        'vehicle_classification'        => '04002', // 自動車種別ID
        'vehicle_size_kb'               => '04003', // 小中大区分
        'vehicle_purpose'               => '04004', // 用途ID
        'land_transport_office_cd'      => '04005', // 陸運支局CD
        'private_commercial'            => '04006', // 自家用・事業用の別ID
        'car_body_shape'                => '04007', // 車体の形状ID
        'vehicle'                       => '04008', // 車名ID
        'kinds_of_fuel'                 => '04009', // 燃料の種類ID
        'suspensions_cd'                => '04010', // サスペンションCD
        'transmissions'                 => '04011', // ミッションID
        'power_gate_cd'                 => '04012', // パワーゲートCD
        'drive_system'                  => '04013', // 駆動ID
        'deposit_month'                 => '05001', // 入金予定月ID
        'deposit_method'                => '05002', // 入金予定方法ID
        'consumption_tax_calc_unit'     => '05003', // 消費税計算単位区分ID
        'rounding_method'               => '05004', // 消費税端数処理区分ID
        'deposit_bank_cd'               => '05005', // 入金銀行コード
        'payment_month'                 => '06001', // 支払予定月ID
        'payment_method'                => '06002', // 支払予定方法ID
        'payment_bank_cd'               => '06003', // 支払銀行コード
        'payment_branch_cd'             => '06004', // 支払銀行支店コード
        'show_division'                 => '07001', // 表示区分
        'credit_debit_division'         => '07002', // 貸借区分
        'debit_tax_division'            => '07003', // 借方税区分
        'credit_tax_division'           => '07004', // 貸方税区分
        'holiday_kb'                    => '08001', // 休日区分
        'accessible_kb'                 => '09001', // アクセス許可区分
        // 'screen_disp_auth_kb'           => '09002', // データ項目表示許可区分
        'screen_category'               => '09003', // システム画面カテゴリID
        'payment_account_type'          => '09004', // 支払口座種別
        'vehicle_classification_for_empty_car_info'          => '11001', // 空車情報用 自車区分
        'empty_car_info_status'         => '11002', // 空車情報ステータス
        'preferred_package'             => '11003', // 希望荷物
        'loaded_item'                   => '11004', // 搭載物
    ],
    'adhibition_end_dt_default' => '2999/12/31',
    'vehicles_path' => storage_path('vehicles/'),
    'staff_path'=>storage_path('staffs/'),
    'max_file_size' => 1,
    'import_file_path'=>[
        'mst_vehicles' => [
                'main' => storage_path('import/dbo_M_車両.xlsx'),
                'extra' => [
                    storage_path('import/償却 新 ６ (カラフル) 新.xlsx'),
                    storage_path('import/仕様別車両一覧~300620.xls'),
                    storage_path('import/車両償却 ５ （青白）.xls'),
                ]
        ],
        'mst_staffs'=>storage_path('import/dbo_M_社員.xlsx'),
        'data_convert'=>storage_path('logs/DataConvert.log'),
    ]
];
