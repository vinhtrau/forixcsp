<?php
/**
 * Created by: Bruce
 * Date: 07/10/20
 * Time: 14:09
 */

$rootPath = '';
if(is_file(realpath(__DIR__ . '/../app/bootstrap.php'))){//root is pub
    $rootPath = realpath(__DIR__ . '/../');
}elseif(is_file(realpath(__DIR__ . '/app/bootstrap.php'))){
    $rootPath = __DIR__;
}
$jsonData = file_get_contents("php://input");
if(substr($jsonData, 0, 14) === '{"csp-report":'){
    $jsonData      = json_decode($jsonData, true);
    $cspReportData = $jsonData['csp-report'];
    if($cspReportData['blocked-uri'] == "data")
        $host = "data:";
    else{
        $blockUrl = parse_url($cspReportData['blocked-uri']);
        $host     = $blockUrl['host'];
    }
    $directive     = $cspReportData['violated-directive'];
    if(strpos($directive, " ") !== false){
        $tmp = explode(" ", $directive);
        $directive = $tmp[0];
    }
    $date          = date("Y-m-d H:i:s");
    $csvDirPath    = $rootPath . "/var/csp_collector/admin";
    if(!is_dir($csvDirPath)){
        mkdir($csvDirPath, 0777,true);
    }
    if(is_dir($csvDirPath) && is_writable($csvDirPath)){
        $csvName = "csp_" . date("Y_m_d") . ".csv";
        $csvFile = fopen($csvDirPath . "/" . $csvName, 'a+');
        fputcsv($csvFile, [$host, $directive, $date]);
    }else{

    }
}
