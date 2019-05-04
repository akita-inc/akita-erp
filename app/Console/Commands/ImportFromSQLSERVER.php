<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/24/2019
 * Time: 11:13 AM
 */
namespace App\Console\Commands;
use App\Models\MStaffs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ImportFromSQLSERVER extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ImportFromSQLSERVER';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $serverName = "172.30.30.193";
        $connectionInfo = [
            "Database" => "AKITA",
            "Uid" => "sa",
            "PWD" => "Shinway@123"
        ];
        $conn = sqlsrv_connect( $serverName, $connectionInfo);
        if( $conn )
        {
            $sql = "SELECT TOP 10 * FROM ".mb_convert_encoding('M_運転日報', 'sjis-win', 'UTF-8');
            $stmt = sqlsrv_query( $conn, $sql );
            if( $stmt === false) {
                die( print_r( sqlsrv_errors(), true) );
            }

            while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
                echo $row['block_name'];
            }

            sqlsrv_free_stmt( $stmt);

            echo "Connection established.\n";
        }
        else
        {
            echo "Connection could not be established.\n";
            die( print_r( sqlsrv_errors(), true));
        }

        //-----------------------------------------------
        // Perform operations with connection.
        //-----------------------------------------------

        /* Close the connection. */
        sqlsrv_close( $conn);
    }
}
