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
                'main' => [
                    'path' => storage_path('import/dbo_M_車両.xlsx'),
                    'fileName' => 'dbo_M_車両.xlsx',
                ],
                'extra1' => [
                    'path' => storage_path('import/償却 新 ６ (カラフル) 新.xlsx'),
                    'fileName' => '償却 新 ６ (カラフル) 新.xlsx',
                ],
                'extra2' => [
                    'path' => storage_path('import/仕様別車両一覧~300620.xls'),
                    'fileName' => '仕様別車両一覧~300620.xls',
                ],
                'extra3' => [
                    'path' => storage_path('import/車両償却 ５ （青白）.xls'),
                    'fileName' => '車両償却 ５ （青白）.xls',
                ]
        ],
        'mst_staffs'=>[
            'main'=>storage_path('import/dbo_M_社員.xlsx'),
            'main_file_name'=>'dbo_M_社員.xlsx',
            'health_insurance_card_information'=>storage_path('import/20190404_保険証情報.xlsx'),
            'health_insurance_card_information_nm'=>'20190404_保険証情報.xlsx',
            'staff_background'=>storage_path('import/dbo_T_社員DETA.xlsx'),
            'staff_background_nm'=>'dbo_T_社員DETA.xlsx',
            'drivers_license'=>storage_path('import/20190404_免許情報.xlsx'),
            'drivers_license_nm'=>'20190404_免許情報.xlsx',
            'export_password_file_nm'=>storage_path("import/dbo_M_社員_yyyymmddhh24miss.xlsx"),
        ],
        'mst_bill_issue_destinations'=>[
            'main'=>storage_path('import/dbo_M_得意先.xlsx'),
            'main_file_name'=>'dbo_M_得意先.xlsx',
        ],
        'mst_staff_dependents' => storage_path('import/扶養者名_生年月日.xlsx'),
        'mst_suppliers' => [
            'main' => [
                'path' => storage_path('import/dbo_M_社員.xlsx'),
                'fileName' => 'dbo_M_社員.xlsx',
            ],
            'extra1' => [
                'path' => storage_path('import/経費_得意先_Ｔ.xlsx'),
                'fileName' => '経費_得意先_Ｔ.xlsx',
            ],
        ],
        'mst_staff_qualifications'=>[
            'main' => [
                'path' => storage_path('import/dbo_T_社員DETA.xlsx'),
                'fileName' => 'dbo_T_社員DETA.xlsx',
            ],
        ],
        'data_convert'=>storage_path('logs/DataConvert.log'),
    ],
    "log_import_path" => [
        'data_convert'=>storage_path('logs/DataConvert.log')
    ],
    'import_mst_staffs_data_kb' => [
        'employment_pattern_kb'=>[
            '1'=>'正社員',
            '2'=>'契約社員',
            '3'=>'パート・アルバイト',
            '4'=>'顧問',
            '5'=>'役員'
        ],
        'sex_kb'=>[
            '1'=>'男性',
            '2'=>'女性'
        ],
        'relocation_municipal_office_cd_kb'=>[
            '131032'=>'港区',
            '131041'=>'新宿区',
            '131059'=>'文京区',
            '131067'=>'台東区',
            '131075'=>'墨田区',
            '131083'=>'江東区'
        ]
    ],
    'import_mst_vehicles_data_kb' => [
        'vehicles_kb' => [
            '1' => '一般車',
            '2' => '運行車',
        ],
        'vehicle_size_kb' => [
            '3' => '小型',
            '5' => '中型',
            '8' => '大型',
        ],
        'vehicle_purpose_id' => [
            '1' => '貨物',
            '2' => 'トレーラー',
        ],
        'land_transport_office_cd' => [
            '21' => '愛知県',
            '22' => '岐阜県',
            '23' => '三重県',
            '24' => '静岡県',
            '25' => '新潟県',
            '26' => '長野県',
            '27' => '滋賀県',
            '28' => '富山県',
            '29' => '奈良県',
            '30' => '兵庫県',
            '31' => '福島県',
            '32' => '熊本県',
            '33' => '石川県',
            '34' => '香川県',
            '35' => '埼玉県',
            '36' => '佐賀県',
            '37' => '京都府',
            '38' => '福井県',
            '39' => '宮城県',
            '40' => '広島県',
        ],
        'vehicle_classification_id' => [
            '1' => '普通',
            '2' => '特殊',
        ],
        'private_commercial_id' => [
            '1' => '事業用',
            '2' => '自家用',
        ],
        'car_body_shape_id' => [
            '1' => 'キャブオーバー',
            '2' => 'バン',
        ],
        'vehicle_id' =>[
            '1' => 'イスズ',
            '2' => '日野',
            '3' => 'トヨタ',
            '4' => '日産',
            '5' => '三菱',
            '6' => 'マツダ',
            '7' => 'ダイハツ',
            '9' => 'その他',
        ],
        'kinds_of_fuel_id'=>[
            '1' => '軽油',
            '2' =>'ガソリン',
        ]
    ],

    'empty_info_reservation_mail' => array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【KARAマッチ】No.[id] に申請がありました。',
        'template' => "【KARAマッチ】\n".
                "No.[id]\n".
                "発地：[start_address]\n".
                "着地：[arrive_address]\n".
                "空車予定日時：[start_date_time]\n".
                "到着日：[arrive_date]\n".
                "に、仮押さえ申請がありました。\n\n".
                "【申請者】\n".
                "[business_office_nm]\n".
                "[staffs_nm]\n\n".
                "【車輌情報】\n".
                "＜車両区分＞\n".
                "[vehicle_kb]\n\n".
                "＜車番＞\n".
                "[registration_numbers]\n\n".
                "＜車格＞\n".
                "[vehicle_size]\n\n".
                "＜形状＞\n".
                "[vehicle_body_shape]\n\n".
                "＜希望運賃＞\n".
                "[asking_price]\n\n".
                "＜希望荷物＞\n".
                "[asking_baggage]\n\n".
                "確認の後、承認お願いいたします。\n\n".
                "------------------------------------------------------\n".
                "Akita ERP - KARAマッチ\n\n"
        ,
    ),
    'empty_info_reservation_approval_mail' => array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【KARAマッチ】No.[id] の申請が承認されました。',
        'template' => "【KARAマッチ】\n".
            "No.[id]\n".
            "発地：[start_address]\n".
            "着地：[arrive_address]\n".
            "空車予定日時：[start_date_time]\n".
            "到着日：[arrive_date]\n".
            "の申請が承認されました。\n\n".
            "【承認者】\n".
            "[business_office_nm]\n".
            "[staffs_nm]\n\n".
            "【車輌情報】\n".
            "＜車両区分＞\n".
            "[vehicle_kb]\n\n".
            "＜車番＞\n".
            "[registration_numbers]\n\n".
            "＜車格＞\n".
            "[vehicle_size]\n\n".
            "＜形状＞\n".
            "[vehicle_body_shape]\n\n".
            "＜希望運賃＞\n".
            "[asking_price]\n\n".
            "＜希望荷物＞\n".
            "[asking_baggage]\n\n".
            "------------------------------------------------------\n".
            "Akita ERP - KARAマッチ\n\n"
    ),
    'empty_info_reservation_reject_mail' => array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【KARAマッチ】No.[id] の申請が却下されました。',
        'template' => "【KARAマッチ】\n".
            "No.[id]\n".
            "発地：[start_address]\n".
            "着地：[arrive_address]\n".
            "空車予定日時：[start_date_time]\n".
            "到着日：[arrive_date]\n".
            "の申請が却下されました。。\n\n".
            "【却下者】\n".
            "[business_office_nm]\n".
            "[staffs_nm]\n\n".
            "【車輌情報】\n".
            "＜車両区分＞\n".
            "[vehicle_kb]\n\n".
            "＜車番＞\n".
            "[registration_numbers]\n\n".
            "＜車格＞\n".
            "[vehicle_size]\n\n".
            "＜形状＞\n".
            "[vehicle_body_shape]\n\n".
            "＜希望運賃＞\n".
            "[asking_price]\n\n".
            "＜希望荷物＞\n".
            "[asking_baggage]\n\n".
            "------------------------------------------------------\n".
            "Akita ERP - KARAマッチ\n\n"
    ),
];
