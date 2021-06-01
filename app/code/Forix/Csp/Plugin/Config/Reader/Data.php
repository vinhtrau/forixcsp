<?php
/**
 * Created by: Bruce
 * Date: 07/10/20
 * Time: 11:22
 */

namespace Forix\Csp\Plugin\Config\Reader;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Magento\Framework\Serialize\Serializer\Serialize;

class Data{

    /**
     * @var \Magento\Framework\Config\CacheInterface
     */
    protected $cache;
    /** @var State */
    protected $state;
    /**
     * @var $serialize Serialize
     */
    protected $serialize;
    protected $configHelper;
    /**
     * @var $connection ResourceConnection
     */
    protected $connection;

    public function __construct(
        \Magento\Framework\Config\CacheInterface $cache,
        ResourceConnection $resource_connection,
        State $state,
        Serialize $serialize,
        \Forix\Csp\Helper\Config $configHelper
    ){
        $this->cache        = $cache;
        $this->state        = $state;
        $this->serialize    = $serialize;
        $this->configHelper = $configHelper;
        $this->connection   = $resource_connection->getConnection('default');
    }


    /**
     * @param $subject \Magento\Csp\Model\Collector\CspWhitelistXml\Data
     * @param $config
     */
    public function afterGet($subject, $config){
        if(!$this->configHelper->isEnableRule()){
            return $config;
        }
        if($data = $this->_loadCsp()){
            foreach($data as $directive => $hosts){
                $hostArr = array_unique(explode(",",$hosts));
                if(!isset($config[$directive])){
                    $config[$directive]['hosts'] = $hostArr;
                }else{
                    if(isset($config[$directive]['hosts'])){
                        $host = array_unique(array_merge($hostArr, $config[$directive]['hosts']));
                        $config[$directive]['hosts'] = $host;
                    }
                }
                $config[$directive]['hosts'] = $this->configHelper->mergeHost($config[$directive]['hosts']);
                if(!isset($config[$directive]['hashes'])){
                    $config[$directive]['hashes'] = [];
                }
            }
        }
        return $config;
    }

    protected function _loadCsp(){
        try{
            $areaCode = $this->state->getAreaCode();
            $area     = $areaCode == "adminhtml" ? 'admin' : $areaCode;
            $cacheId  = "forix_csp_collector_" . $area;
            if($data = $this->cache->load($cacheId)){
                return $this->serialize->unserialize($data);
            }
            /** Fetch from database */
            $query = "SET SESSION group_concat_max_len = 100000;";
            $this->connection->query($query);
            /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
            $select = $this->connection->select();
            $table  = $this->connection->getTableName('forix_csp_collector');
            $select->from(["csp" => $table])
                   ->reset('columns')
                   ->where("area = ?", $area)
                   ->where("is_allowed = ?", 1)
                   ->group("directive")
                   ->columns(new \Zend_Db_Expr("csp.directive, GROUP_CONCAT(csp.host) as hosts"));
            $data = $this->connection->query($select)->fetchAll(\PDO::FETCH_KEY_PAIR);
            if($data){
                $this->cache->save($this->serialize->serialize($data), $cacheId, ["CONFIG"]);
            }
            return $data;
        }catch(\Exception $e){
            return [];
        }
        return [];
    }
}
