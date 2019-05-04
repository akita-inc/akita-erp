<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 5/4/2019
 * Time: 2:31 PM
 */
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
