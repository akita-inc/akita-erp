<?php

namespace App\Console\Commands;

use App\Console\ImportExcel\MstStaffDependents;
use App\Console\Commands\ImportExcel\MstStaffs;
use App\Models\MBusinessOffices;
use App\Models\MCustomers;
use App\Models\MEmptyInfo;
use App\Models\MGeneralPurposes;
use App\Models\MStaffDependents;
use App\Models\MStaffQualifications;
use App\Models\MStaffs;
use App\Models\MSupplier;
use App\Models\MVehicles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\WriteLogs;

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
        $class = "\\APP\\Console\\Commands\\ImportExcel\\".str_replace(" ","",(ucwords(str_replace("_"," ",$type))));
        $class = new $class();
        $class->table = $type;
        $class->run();
    }
}
