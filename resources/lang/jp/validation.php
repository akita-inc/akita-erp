<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attributeを承認してください。',
    'active_url'           => ':attributeは正しいURLではありません。',
    'after'                => ':attributeは:date以降の日付にしてください。',
    'after_or_equal'       => 'The :attribute must be a date after or equal to :date.',
    'alpha'                => ':attributeは英字のみにしてください。',
    'alpha_dash'           => ':attributeは英数字とハイフンのみにしてください。',
    'alpha_num'            => ':attributeは英数字のみにしてください。',
    'array'                => ':attributeは配列にしてください。',
    'before'               => ':attributeは:date以前の日付にしてください。',
    'before_or_equal'      => 'The :attribute must be a date before or equal to :date.',
    'between'              => [
        'numeric' => ':attributeは:min〜:maxまでにしてください。',
        'file'    => ':attributeは:min〜:max KBまでのファイルにしてください。',
        'string'  => ':attributeは:min〜:max文字にしてください。',
        'array'   => ':attributeは:min〜:max個までにしてください。',
    ],
    'boolean'              => ':attributeはtrueかfalseにしてください。',
    'confirmed'            => ':attributeは確認用項目と一致していません。',
    'date'                 => ':attributeは正しい日付ではありません。',
    'date_format'          => ':attributeは":format"書式と一致していません。',
    'different'            => ':attributeは:otherと違うものにしてください。',
    'digits'               => ':attributeは:digits桁にしてください',
    'digits_between'       => ':attributeは:min〜:max桁にしてください。',
    'dimensions'           => ':attributeは正しい画像サイズにしてください。',
    'distinct'             => ':attributeに重複値があります。',
    'email'                => ':attributeを正しいメールアドレスにしてください。',
    'exists'               => '選択された:attributeは正しくありません。',
    'file'                 => ':attributeはファイルにしてください。',
    'filled'               => ':attributeは必須です。',
    'image'                => ':attributeは画像にしてください。',
    'in'                   => '選択された:attributeは正しくありません。',
    'in_array'             => ':attributeは:otherに存在しません。',
    'integer'              => ':attributeは整数にしてください。',
    'ip'                   => ':attributeを正しいIPアドレスにしてください。',
    'json'                 => ':attributeを正しいJSON文字列にしてください。',
    'max'                  => [
        'numeric' => ':attributeは:max以下にしてください。',
        'file'    => ':attributeは:max KB以下のファイルにしてください。.',
        'string'  => ':attributeは:max文字以下にしてください。',
        'array'   => ':attributeは:max個以下にしてください。'
    ],
    'mimes'                => ':attributeは:valuesタイプのファイルにしてください。',
    'mimetypes'            => ':attributeは:valuesタイプのファイルにしてください。',
    'min'                  => [
        'numeric' => ':attributeは:min以上にしてください。',
        'file'    => ':attributeは:min KB以上のファイルにしてください。.',
        'string'  => ':attributeは:min文字以上で入力して下さい。',
        'array'   => ':attributeは:min個以上にしてください。'
    ],
    'not_in'               => '選択された:attributeは正しくありません。',
    'numeric'              => ':attributeは数字にしてください。',
    'present'              => 'The :attribute field must be present.',
    'regex'                => ':attributeの書式が正しくありません。',
    'required'             => ':attributeを入力してください。',
    'required_if'          => ':otherが:valueの時、:attributeは必須です。',
    'required_unless'      => ':otherが:valueでない限り、:attributeは必須です。',
    'required_with'        => ':valuesが存在する時、:attributeは必須です。',
    'required_with_all'    => ':valuesが存在する時、:attributeは必須です。',
    'required_without'     => ':valuesが存在しない時、:attributeは必須です。',
    'required_without_all' => ':valuesが存在しない時、:attributeは必須です。',
    'same'                 => ':attributeと:otherは一致していません。',
    'size'                 => [
        'numeric' => ':attributeは:sizeにしてください。',
        'file'    => ':attributeは:size KBにしてください。.',
        'string'  => ':attribute:size文字にしてください。',
        'array'   => ':attributeは:size個にしてください。',
    ],
    'string'                    => ':attributeは文字列にしてください。',
    'timezone'                  => ':attributeは正しいタイムゾーンをしていしてください。',
    'unique'                    => ':attributeは既に存在します。',
    'uploaded'                  => ':attributeはアップロードできませんでした。',
    'url'                       => ':attributeを正しい書式にしてください。',
    'before_date'               => '日指定を開始日 ≦ 導入予定日の形で入力して下さい',
    'flag_adopt_simu_check_one' => '複数のシミュレーションは採用できません。',
    'flag_adopt_simu_exist_check_one' => 'シミュレーションが採用されていません。どれかシミュレーションを採用して下さい。',
    'one_bytes_string' => ':attributeは半角文字で入力してください。',
    'number_dashes' => ':attributeは半角数字，半角ハイフンのみを入力してください。',
    'email_character' => '半角英数字と'.config("params.email_character").'以外の文字を入力しないでください。',
    'email_format' => '正しいE-mailの書式で入力してください。',
    'select_required' => ':attributeを選択してください。',
    'one_byte_number' => ':attributeは半角数字で入力して下さい。',
    'length' => ':attributeは:length文字で入力して下さい。',
    'check_two_bytes_api' => ':attributeに登録できない文字が含まれています。入力した文字を確認してください。',
    'check_address_api' => ':attributeに登録できない文字が含まれています。入力した文字を確認してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'login_id' => array(
            'required' => 'IDを入力してください。',
        ),
        'login_pw' => array(
            'required' => 'PWを入力してください。',
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
    	"nameMember" => "お名前",
    	"emailMember" => "Eメール",
        "zip_cd" => "郵便番号",
        "address_1" => "住所",
        "address_2" => "建物など",
        "tel_no" => "電話番号",
        "cel_no" => "携帯電話",
        "id_verify_doc" => "確認書類",
        "id_verify_img" => "確認画像",
        "id_verify_img_2" => "確認画像2",
        //
        "login_id" => "ログインID",
        "login_pw" => "パスワード",
        "user_name" => "利用者名",
        //
        "name" => "お名前",
        "status" => "ステータス",
        //
        "ptn_width" => "フィールド幅",
        "ptn_height" => "フィールド高さ",
        "ptn_default" => "初期値",
        "ptn_value" => "選択値",
        //
        "mng_name" => "選択値種別名",
        "mng_order" => "表示順",
        "info_1" => "名称"
    ],

];
