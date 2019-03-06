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

trait ListTrait
{
    public $query = null;

    protected function getQuery(){
        $this->query = DB::table($this->table);
    }

    protected function search($data){}

    public function getItems(Request $request)
    {
        $data = $request->all();
        $this->getQuery();
        $this->search( $data );
        $items = $this->query->paginate(config('params.page_size'));
        $response = [
            'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem()
            ],
            'data' => $items
        ];
        return response()->json($response);
    }
}
