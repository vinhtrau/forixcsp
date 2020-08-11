<?php
/**
 * Created by: Bruce
 * Date: 07/10/20
 * Time: 17:07
 */

namespace Forix\Csp\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Phrase;

class ImportData extends AbstractHelper{

    protected $_baseDir;
    protected $_errorDir;
    /**
     * @var $connection \Magento\Framework\DB\Adapter\Pdo\Mysql
     */
    protected $connection;

    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $dirList,
        ResourceConnection $resource_connection,
        Context $context
    ){
        $this->_baseDir = $dirList->getPath("var")."/csp_collector";
        $this->_errorDir = $this->_baseDir."/error";
        if(!is_dir($this->_errorDir)){
            mkdir($this->_errorDir, 0777, true);
        }
        $this->connection = $resource_connection->getConnection('default');
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\DB\Adapter\Pdo\Mysql
     */
    public function getConnection(){
        return $this->connection;
    }

    public function isEnabled(){
        return $this->scopeConfig->getValue('forix_csp/general/enabled');
    }

    public function getReportedList($rows = null){
        if($rows){
            $list = [];
            foreach($rows as $file){
                $_files = explode('/',$file);
                $type = $_files[1];
                $list[$type][] = BP . "/var/" .$file;
            }
            return $list;
        }
        $listFe    = glob($this->_baseDir . "/frontend/csp_*.csv");
        $listAdmin = glob($this->_baseDir . "/admin/csp_*.csv");
        $list      = [
            'frontend' => $listFe,
            'admin'    => $listAdmin
        ];
        if(!count($listFe) && !count($listAdmin)){
            throw new NotFoundException(new Phrase('No CSP report found!'));
        }

        return $list;
    }
    public function import($rows = null){
        if(!$this->isEnabled()){
            throw new \Exception("Module is disabled");
        }

        $list = $this->getReportedList($rows);
        $cspExists = $this->getCspReported();
        $connection = $this->getConnection();
        $col = [
            'host','directive','created_at', 'area'
        ];

        $insertData = [];
        foreach($list as $area => $_file){
            foreach($_file as $file){
                if(!is_readable($file)){
                    continue;
                }
                $f = fopen($file, "r");
                $l = 0;
                $success = true;
                while($data = fgetcsv($f)){
                    $l++;
                    $data[] = $area;

                    $key = join("_", [$data[0], $data[1], $area]);
                    if(!isset($cspExists[$key])){
                        try{
                            $insertData[]    = array_combine($col, $data);
                            $cspExists[$key] = 1;
                        }catch(\Exception $e){
                            $success = false;
                            $dataError = [
                                "file" => $file,
                                "data" => $data,
                                "csvLine" => $l,
                                "message" => $e->getMessage()
                            ];
                            $this->_logger->error(print_r($dataError,1));
                            if(class_exists("ForixDebug")){
                                \ForixDebug::log($dataError, 'csp_import.log');
                            }
                            /* Move error file to folder */
//                            $fileArr = pathinfo($file);
//                            $newFile = $this->_errorDir . "/" . $area . "_" . $fileArr['basename'];
//                            rename($file,$newFile);
                        }
                    }
                }
//                if($success)
                    unlink($file);
            }
        }
        if($insertData){
            $table = $connection->getTableName('forix_csp_collector');
            $connection->insertMultiple($table, $insertData);
        }
    }

    public function getCspReported(){
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $this->connection;
        $select     = $connection->select();
        $select->from($connection->getTableName('forix_csp_collector'), ['directive', 'host', 'area']);
        $data = $connection->query($select)->fetchAll();
        if(!$data){
            return [];
        }
        $cspReport = [];
        foreach($data as $config){
            $key             = strtolower(join("_", [$config['host'], $config['directive'], $config['area']]));
            $cspReport[$key] = 1;
        }

        return $cspReport;
    }
}
