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

    CONST ROOT_SCRIPT = 'CatchCsp.php';
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
    /**
     * @var $connection ResourceConnection
     */
    protected $connection;

    public function __construct(
        \Magento\Framework\Config\CacheInterface $cache,
        ResourceConnection $resource_connection,
        State $state,
        Serialize $serialize
    ){
        $this->cache      = $cache;
        $this->state      = $state;
        $this->serialize  = $serialize;
        $this->connection = $resource_connection->getConnection('default');
    }


    /**
     * @param $subject \Magento\Csp\Model\Collector\CspWhitelistXml\Data
     * @param $config
     */
    public function afterGet($subject, $config){
        if($data = $this->_loadCsp()){
            foreach($data as $cspAllow){
                $type = $cspAllow['directive'];
                $host = $cspAllow['host'];
                if(!isset($config[$type]) || !in_array($host, $config[$type]['hosts'])){
                    $config[$type]['hosts'][] = $host;
                }
                if(!isset($config[$type]['hashes'])){
                    $config[$type]['hashes'] = [];
                }
            }
        }
        return $config;
    }

    protected function _loadCsp(){
        $areaCode = $this->state->getAreaCode();
        $area = $areaCode == "adminhtml" ? 'admin': $areaCode;
        $cacheId = "forix_csp_collector_".$area;
        if($data = $this->cache->load($cacheId)){
            return $this->serialize->unserialize($data);
        }
        /** fetch from database */
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $select = $this->connection->select();
        $select->from($this->connection->getTableName('forix_csp_collector'))
               ->where("is_allowed = ?", 1)
               ->where('area = ?', $area);
        $data = $this->connection->query($select)->fetchAll();
        if($data){
            $this->cache->save($this->serialize->serialize($data),$cacheId,["CONFIG"]);
        }
        return $data;
    }
}
