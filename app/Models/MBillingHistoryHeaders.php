<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MBillingHistoryHeaders extends Model {
    use SoftDeletes;

    protected $table = "t_billing_history_headers";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];

    public function getInvoicePDFHeader($id){
        $params = ['id' => $id];
        $query = "
                SELECT
                bill.invoice_number,
                bill.mst_customers_cd as customer_cd,
                bill.mst_business_office_id,
                CONCAT(cus.customer_nm_formal,' ', '御中') AS customer_nm,
                ( SELECT CONCAT_WS('-', SUBSTR(bill_zip_cd, 1, 3), SUBSTR(bill_zip_cd, 4)) AS bill_zip_cd FROM mst_bill_issue_destinations WHERE mst_customer_id = cus.id AND deleted_at IS NULL LIMIT 1 ) AS bill_zip_cd,
                (
                SELECT
                    CONCAT(
                        IFNULL( bill_pref.date_nm, '' ),
                        IFNULL( bill_address2, '' ), 
                        IFNULL( bill_address3, '' ),
                        IFNULL( bill_address4, '' ) 
                    ) 
                FROM
                    mst_bill_issue_destinations
                LEFT JOIN mst_general_purposes AS bill_pref ON bill_address1 = bill_pref.date_id  AND bill_pref.data_kb = '01002' 
                WHERE
                    mst_customer_id = cus.id 
                    AND mst_bill_issue_destinations.deleted_at IS NULL 
                    LIMIT 1 
                ) AS bill_address,
                bill.publication_date,
                CONCAT_WS( ' ', 'アキタ株式会', office.business_office_nm ) AS business_office_nm,
                IF( office.zip_cd IS NULL, '', CONCAT_WS('-', SUBSTR(office.zip_cd, 1, 3), SUBSTR(office.zip_cd, 4))) AS zip_cd,
                CONCAT(
                    IFNULL( prefectures.date_nm, '' ),
                    IFNULL( office.address1, '' ),
                    IFNULL( office.address2, '' ),
                    IFNULL( office.address3, '' ) 
                ) AS address,
                office.phone_number,
                office.fax_number, 
                format(bill.consumption_tax, '#,##0') as consumption_tax,
                format(bill.tax_included_amount, '#,##0') as tax_included_amount,
                format(bill.total_fee, '#,##0') as total_fee,
                format(details.sales_amount, '#,##0') as sales_amount,
                format(details.incidental_other, '#,##0') as incidental_other,
                format(details.surcharge_fee, '#,##0') as surcharge_fee,
                format(details.toll_fee, '#,##0') as toll_fee
            FROM
                t_billing_history_headers bill
                LEFT JOIN mst_customers AS cus ON cus.mst_customers_cd = bill.mst_customers_cd 
                AND cus.deleted_at
                IS NULL LEFT JOIN mst_business_offices AS office ON office.id = bill.mst_business_office_id 
                AND office.deleted_at
                IS NULL LEFT JOIN mst_general_purposes AS prefectures ON office.prefectures_cd = prefectures.date_id 
                AND prefectures.data_kb = '01002'
                LEFT JOIN (
                SELECT
                    invoice_number,
                    sum( IFNULL( quantity, 0 ) * IFNULL( unit_price, 0 ) - IFNULL( discount_amount, 0 ) ) AS sales_amount,
                    sum(
                        IFNULL( insurance_fee, 0 ) + IFNULL( loading_fee, 0 ) + IFNULL( wholesale_fee, 0 ) + IFNULL( waiting_fee, 0 ) + IFNULL( incidental_fee, 0 ) 
                    ) AS incidental_other,
                    sum( IFNULL( surcharge_fee, 0 ) ) AS surcharge_fee,
                    sum( IFNULL( billing_fast_charge, 0 ) ) AS toll_fee 
                FROM
                    t_billing_history_header_details 
                WHERE
                    deleted_at IS NULL 
                GROUP BY
                    invoice_number 
                ) details ON details.invoice_number = bill.invoice_number 
            WHERE
                bill.id = :id 
                AND bill.deleted_at IS NULL
        ";

        return DB::select($query,$params);
    }
}