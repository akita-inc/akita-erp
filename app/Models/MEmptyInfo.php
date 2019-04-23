<?php

namespace App\Models;

use App\Helpers\TimeFunction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MEmptyInfo extends Model
{
    use SoftDeletes;

    protected $table = "empty_info";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [

    ];

    public $rules = [

    ];

    public static function updateStatus($id,$status, $mail_fg=null){
        $mEmptyInfo = MEmptyInfo::find( $id );
        DB::beginTransaction();
        try {
            if($mEmptyInfo->status==2 && $mEmptyInfo->regist_office_id == Auth::user()->mst_business_office_id && $status==1 && Carbon::parse($mEmptyInfo->arrive_date) <= Carbon::now()){
                $status = 9;
            }
            $mEmptyInfo->status = $status;

            switch ($status){
                case 1:
                    $mailTo = explode(';',$mEmptyInfo->ask_staff_email_address);
                    $configMail = config('params.empty_info_reservation_reject_mail');
                    $mEmptyInfo->ask_date = null;
                    $mEmptyInfo->ask_office = null;
                    $mEmptyInfo->ask_staff = null;
                    $mEmptyInfo->ask_staff_email_address = null;

                    break;
                case 2:
                    $mailTo = explode(';',$mEmptyInfo->email_address);
                    $configMail = config('params.empty_info_reservation_mail');
                    $empty_mail_add = MEmptyMailTo::where('office_id',Auth::user()->mst_business_office_id)->whereNull('deleted_at')->first();
                    $mEmptyInfo->ask_date = TimeFunction::getTimestamp();
                    $mEmptyInfo->ask_office = Auth::user()->mst_business_office_id ;
                    $mEmptyInfo->ask_staff = Auth::user()->staff_cd;
                    $mEmptyInfo->ask_staff_email_address = $empty_mail_add ?$empty_mail_add->email_address : null;
                    break;
                case 8:
                    $mailTo = explode(';',$mEmptyInfo->ask_staff_email_address);
                    $configMail = config('params.empty_info_reservation_approval_mail');
                    $mEmptyInfo->apr_date = TimeFunction::getTimestamp();
                    $mEmptyInfo->apr_staff = Auth::user()->staff_cd;
                    break;
                case 9:
                    $mailTo = explode(';',$mEmptyInfo->ask_staff_email_address);
                    $configMail = config('params.empty_info_reservation_reject_mail');
                    $mEmptyInfo->ask_date = null;
                    $mEmptyInfo->ask_office = null;
                    $mEmptyInfo->ask_staff = null;
                    $mEmptyInfo->ask_staff_email_address = null;
                    $mEmptyInfo->apr_date = null;
                    $mEmptyInfo->apr_staff = null;
                    break;
            }
            $mEmptyInfo->save();
            DB::commit();
            if($mail_fg){
                $mEmptyInfo->handleMail($id,$configMail,$mailTo);
            }
        }catch (\Exception $e) {
            DB::rollback();
            return false;
        }
        return true;
    }

    public function handleMail($id,$configMail,$mailTo){
        $emptyInfo = $this->getInfoForMail($id);
        $text = str_replace(
            ['[id]', '[start_address]','[arrive_address]','[start_date_time]','[arrive_date]','[business_office_nm]','[staffs_nm]','[vehicle_kb]','[registration_numbers]','[vehicle_size]','[vehicle_body_shape]','[asking_price]','[asking_baggage]'],
            [$emptyInfo->id, $emptyInfo->start_address, $emptyInfo->arrive_address, $emptyInfo->start_date_time, $emptyInfo->arrive_date, $emptyInfo->regist_office, $emptyInfo->regist_staff, $emptyInfo->vehicle_classification, $emptyInfo->registration_numbers, $emptyInfo->vehicle_size, $emptyInfo->vehicle_body_shape, $emptyInfo->asking_price, $emptyInfo->asking_baggage],
            $configMail['template']);
        $subject = str_replace('[id]',$id,$configMail["subject"]);
        Mail::raw($text,
            function ($message) use ($configMail,$subject,$mailTo) {
                $message->from($configMail["from"]);
                $message->to($mailTo)
                    ->subject($subject);
            });
    }
    public function getInfoForMail($id){
        $query = DB::table($this->table);
        $query = $query->select(
            'empty_info.id',
            DB::raw("CONCAT_WS(' ',empty_car_location.date_nm, empty_info.start_address) as start_address"),
            DB::raw("CONCAT_WS(' ',arrive_location.date_nm, empty_info.arrive_address) as arrive_address"),
            DB::raw("CONCAT_WS(' ',DATE_FORMAT(empty_info.start_date, '%Y/%m/%d'),TIME_FORMAT(empty_info.start_time,'%H:%i')) as start_date_time"),
            DB::raw("DATE_FORMAT(empty_info.arrive_date, '%Y/%m/%d') as arrive_date"),
            'mst_business_offices.business_office_nm as regist_office',
            DB::raw("CONCAT_WS(' ',mst_staffs.last_nm,mst_staffs.first_nm) as regist_staff"),
            'vehicle_classification.date_nm as vehicle_classification',
            'empty_info.registration_numbers',
            'empty_info.vehicle_size',
            'empty_info.vehicle_body_shape',
            DB::raw('format(empty_info.asking_price, "#,##0") as asking_price'),
            'pref_asking_baggage.date_nm as asking_baggage'
        );
        $query = $query->leftjoin('mst_general_purposes as vehicle_classification', function ($join) {
            $join->on('vehicle_classification.date_id', '=', 'empty_info.vehicle_kb')
                ->where('vehicle_classification.data_kb', config('params.data_kb.vehicle_classification_for_empty_car_info'));
        })->leftjoin('mst_general_purposes as empty_car_location', function ($join) {
            $join->on('empty_car_location.date_id', '=', 'empty_info.start_pref_cd')
                ->where('empty_car_location.data_kb', config('params.data_kb.prefecture_cd'));
        })->leftjoin('mst_general_purposes as arrive_location', function ($join) {
            $join->on('arrive_location.date_id', '=', 'empty_info.arrive_pref_cd')
                ->where('arrive_location.data_kb',config('params.data_kb.prefecture_cd'));
        })->leftjoin('mst_general_purposes as pref_asking_baggage', function ($join) {
            $join->on('pref_asking_baggage.date_id', '=', 'empty_info.asking_baggage')
                ->where('pref_asking_baggage.data_kb',config('params.data_kb.preferred_package'));
        })->leftjoin('mst_business_offices', function ($join) {
                $join->on('mst_business_offices.id', '=', 'empty_info.regist_office_id')
                    ->whereNull('mst_business_offices.deleted_at');
        })->leftjoin('mst_staffs', function ($join) {
                $join->on('mst_staffs.staff_cd', '=', 'empty_info.regist_staff')
                    ->whereNull('mst_staffs.deleted_at');
        });
        $query = $query->where('empty_info.id', '=', $id)
            ->whereNull('empty_info.deleted_at');
        return $query->first();
    }

}
