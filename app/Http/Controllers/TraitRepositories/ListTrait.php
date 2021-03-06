<?php
/**
 * Created by PhpStorm.
 * User: TINHNV
 * Date: 7/3/2018
 * Time: 10:13 AM
 */

namespace App\Http\Controllers\TraitRepositories;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

trait ListTrait
{
    public $query = null;

    protected function getPaging(){
        return config('params.page_size');
    }

    protected function getQuery(){
        $this->query = DB::table($this->table);
    }

    protected function search($data){}

    protected function queryDataKb($kb,$val)
    {
        if($val)
        {
            $this->query->where($kb,$val);
        }
        else
        {
            return null;
        }
    }
    public function getItems(Request $request)
    {

        if(Session::exists('backQueryFlag') && Session::get('backQueryFlag')){
            if(Session::exists('backQueryFlag') ){
                $data = Session::get('requestHistory');
            }
            Session::put('backQueryFlag', false);
        }else{
            $data = $request->all();
            Session::put('requestHistory', $data);
        }
        $this->getQuery();
        $this->search( $data );
        $items = $this->query->paginate($this->getPaging(), ['*'], 'page', $data['page']);
        if(count($items->items())==0){
            if($data['page'] > 1){
                $items = $this->query->paginate($this->getPaging(), ['*'], 'page', $data['page']-1);
            }
        }
        $response = [
            'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem()
            ],
            'data' => $items,
            'fieldSearch' => $data['fieldSearch'],
            'order' => $data['order'],
        ];
        return response()->json($response);
    }
}
