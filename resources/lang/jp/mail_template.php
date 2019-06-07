<?php
return [
    'empty_info_reservation_mail' => array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【ハコカラ】No.[id] に申請がありました。',
        'template' => "【ハコカラ】\n".
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
            "Akita ERP - ハコカラ\n\n"
    ,
    ),
    'empty_info_reservation_approval_mail' => array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【ハコカラ】No.[id] の申請が承認されました。',
        'template' => "【ハコカラ】\n".
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
            "Akita ERP - ハコカラ\n\n"
    ),
    'empty_info_reservation_reject_mail' => array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【ハコカラ】No.[id] の申請が却下されました。',
        'template' => "【ハコカラ】\n".
            "No.[id]\n".
            "発地：[start_address]\n".
            "着地：[arrive_address]\n".
            "空車予定日時：[start_date_time]\n".
            "到着日：[arrive_date]\n".
            "の申請が却下されました。\n\n".
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
            "Akita ERP - ハコカラ\n\n"
    ),
    'vacation_register_mail' => array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【休暇取得申請】No. [id] [approval_kb] [applicant_id]（[applicant_office_id]）',
        'template' => "【休暇取得申請】\n\n".
            "＜申請ID＞\n".
            "No. [id]\n\n".
            "＜登録者＞\n".
            "[applicant_id]\n\n".
            "＜休暇区分＞\n".
            "[approval_kb]\n\n".
            "＜期間＞\n".
            "[start_date] ～ [end_date]\n".
            "[days] [times]\n\n".
            "＜理由＞\n".
            "[reasons]\n\n\n".
            "確認の後、承認お願いいたします。\n\n".
            "------------------------------------------------------\n\n".
            "Akita ERP - ワークフロー\n\n"
    ),
    'vacation_edit_mail' => array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【休暇取得申請】No. [id] [approval_kb] [applicant_id]（[applicant_office_id]）（修正）',
        'template' => "【休暇取得申請】\n\n".
            "＜申請ID＞\n".
            "No. [id] （No. [id_before] を修正）\n\n".
            "＜登録者＞\n".
            "[applicant_id]\n\n".
            "＜休暇区分＞\n".
            "[approval_kb]\n\n".
            "＜期間＞\n".
            "[start_date] ～ [end_date]\n".
            "[days] [times]\n\n".
            "＜理由＞\n".
            "[reasons]\n\n\n".
            "確認の後、承認お願いいたします。\n\n".
            "------------------------------------------------------\n\n".
            "Akita ERP - ワークフロー\n\n"
    ,
    ),
    'vacation_approval_mail' => array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【休暇取得申請】承認：No. [id] [approval_kb] [applicant_id]（[applicant_office_id]）',
        'template' => "【休暇取得申請】\n".
            "[title] に承認されました。\n\n".
            "＜申請ID＞\n".
            "No. [id]\n\n".
            "＜登録者＞\n".
            "[applicant_id]\n\n".
            "＜休暇区分＞\n".
            "[approval_kb]\n\n".
            "＜期間＞\n".
            "[start_date] ～ [end_date]\n".
            "[days] [times]\n\n".
            "＜理由＞\n".
            "[reasons]\n\n".
            "------------------------------------------------------\n\n".
            "Akita ERP - ワークフロー\n\n"
    ),
    'vacation_reject_mail' => array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【休暇取得申請】却下：No. [id] [approval_kb] [applicant_id]（[applicant_office_id]）',
        'template' => "【休暇取得申請】\n".
            "[title] に却下されました。\n".
            "理由：[send_back_reason]\n\n".
            "＜申請ID＞\n".
            "No. [id]\n\n".
            "＜登録者＞\n".
            "[applicant_id]\n\n".
            "＜休暇区分＞\n".
            "[approval_kb]\n\n".
            "＜期間＞\n".
            "[start_date] ～ [end_date]\n".
            "[days] [times]\n\n".
            "＜理由＞\n".
            "[reasons]\n\n".
            "------------------------------------------------------\n\n".
            "Akita ERP - ワークフロー\n\n"
    ),
    'expense_application_register_mail'=>array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【交際費支出申請】No. [id][applicant_id]（[applicant_office_id]）',
        'template' => "【交際費支出申請】\n\n".
            "＜申請ID＞\n".
            "No. [id]\n\n".
            "＜登録者＞\n".
            "[applicant_id]\n\n".
            "確認の後、承認お願いいたします。\n\n".
            "------------------------------------------------------\n\n".
            "Akita ERP - ワークフロー\n\n"
    ),
    'expense_application_edit_mail'=>array(
        'from' => 'shinwaytest@gmail.com',
        'subject' => '【交際費支出申請】No. [id][applicant_id]（[applicant_office_id]）',
        'template' => "【交際費支出申請】\n\n".
            "＜申請ID＞\n".
            "No. [id]\n\n".
            "＜登録者＞\n".
            "[applicant_id]\n\n".
            "確認の後、承認お願いいたします。\n\n".
            "------------------------------------------------------\n\n".
            "Akita ERP - ワークフロー\n\n"
    )
];