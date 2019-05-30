<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TPaymentHistories extends Model {
    use SoftDeletes;

    protected $table = "t_payment_histories";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];
    public function getListByCustomerCd($dw_number,$dataSearch){
        $query = DB::table('t_payment_histories as payment')
                    ->select(
                        'payment.dw_number',
                        'payment.invoice_number',
                        DB::raw("DATE_FORMAT(bill_headers.publication_date, '%Y/%m/%d') as publication_date"),
                        DB::raw('IFNULL(bill_headers.total_fee,0) as total_fee'),
                        DB::raw('IFNULL(bill_headers.consumption_tax,0) as consumption_tax'),
                        DB::raw('IFNULL(bill_headers.tax_included_amount,0) as tax_included_amount'),
                        DB::raw('0 as last_payment_amount'),
                        DB::raw('IFNULL(payment.total_dw_amount,0) as total_dw_amount'),
                        DB::raw('IFNULL(payment.fee,0) as fee'),
                        DB::raw('IFNULL(payment.discount,0) as discount'),
                        'bill_headers.tax_included_amount',
                        DB::raw('0 as deposit_balance')
                    )->join('t_billing_history_headers as bill_headers', function ($join) {
                        $join->on('bill_headers.invoice_number', '=',  'payment.invoice_number')
                            ->whereRaw('bill_headers.deleted_at IS NULL');
                    })
                    ->where('payment.dw_number',$dw_number);
        if ($dataSearch['from_date'] != '' && $dataSearch['to_date'] != '' ) {
            $query=$query->where('payment.dw_day', '>=',$dataSearch['from_date'])
                ->where('payment.dw_day','<=',$dataSearch['to_date']);
        }
        if ($dataSearch['mst_customers_cd'] != '' ) {
            $query=$query->where('payment.mst_customers_cd', '=',  $dataSearch['mst_customers_cd']);
        }
        $query=$query->whereNull('payment.deleted_at')
            ->orderBy('payment.dw_number','desc')
            ->get();
        if(count($query)>0)
        {
            foreach($query as $key=>$value)
            {
                $price=DB::select('SELECT
                        sum( total_dw_amount ) AS last_payment_amount 
                    FROM
                        t_payment_histories 
                    WHERE
                        deleted_at IS NULL 
                        AND invoice_number = :invoice_number 
                    GROUP BY
                        invoice_number',['invoice_number'=>$value->invoice_number]);
                $value->last_payment_amount=isset($price[0]->last_payment_amount)?$price[0]->last_payment_amount:0;
                $value->deposit_balance=$value->tax_included_amount-$value->last_payment_amount;
            }
        }

        return $query;
    }
}