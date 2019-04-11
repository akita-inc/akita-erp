<?php

namespace App\Console\Commands;

use App\Models\MEmptyInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class ConvertDataByExcels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ConvertDataByExcels {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
マスタデータマッピング.xlsx 車両シートを参考に、table_params にデータコンバートするバッチを作成してください。
登録時にエラーが発生した場合はすべてのデータをロールバックしてください。
エラーが発生しても次のデータを読み込んで、すべてのデータを処理してください。
';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $type = $this->option("type");
        $path = public_path('dbo_M_車両.xlsx');
        $this->getDataFromExcel($path);
    }

    protected function getDataFromExcel($path){
        $data = Excel::load($path)->get();
        if($data->count()){
            foreach ($data as $key => $value) {
                print_r($value->No);
            }

        }
    }
}
