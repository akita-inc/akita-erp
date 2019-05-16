<?php
return[
    'begin_start' => "データコンバート開始（:table）",
    "end_read" => "データコンバート終了（:table）"."　"
        ."データ件数："
        ." :numRead件"
        ."　"."成功件数："
        ." :numNormal件 "."　"
        ."失敗件数："
        ." :numErr件",
    'required' =>  "▼ 必須エラー："
        ." :fileName"."　"
        ." :fieldName"."　"
        ." :row行目"."　",
    'add_general_purposes_number' => "▼ 汎用マスタエラー：データ元" ." :fileName"."　"
        ." :fieldName"."　"
        ." :row行目"."　",
    'unique_cd'=>"▼ コード重複エラー："." :fileName"."　"
        ." :fieldName"."　"
        ." :row行目"."　",
    'check_kana'=>"▼ カナ変換エラー："." :fileName"."　"
        ." :fieldName"."　"
        ." :row行目"."　",
    'add_general_purposes_string' => "▼ 汎用マスタ追加：データ元" ." :fileName"."　"
        ." :fieldName"."　"
        ." :row行目"."　\r\n"
        ."▼ 登録内容："
        ."データ区分："."　".":data_kb"."　"
        ."データ区分名："."　".":data_kb_nm"."　"
        ."データID："."　" .":date_id"."　"
        ."データ名称："."　".":date_nm"."　"
        ."データカナ名称：フメイ",
    'check_length_and_trim' =>  "▼ 桁溢れ：データ元  :fileName　:excelFieldName　:row行目　:excelValue　\r\n"."▼ 登録先テーブル： :tableName　:DBFieldName　:DBvalue",
    'insert_error' => "▼ SQL実行エラー：　:fileName　:row行目　\r\n". "エラー内容：:errorDetail　",
    'no_record_in_extra_file' =>  "▼ IDマッチングエラー：　:mainFileName　:fieldName　:row行目　マッチング対象：　:extraFileName",
    'existed_record_in_db' => "▼ コード重複エラー：　:fileName　:fieldName　:row行目　",
    'registration_numbers_err' => "▼ 自動車登録番号エラー：　:fileName　:fieldName　:row行目　",
    'registration_numbers_required' => "▼ 自動車登録番号必須エラー：　:mainFileName　:fieldName　:row行目　が:extraFileNameにありません。",

];
