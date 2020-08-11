<?php
/**
 * Created by: Bruce
 * Date: 07/10/20
 * Time: 17:06
 */

namespace Forix\Csp\Cron;


use Forix\Csp\Helper\ImportData;
use Psr\Log\LoggerInterface;

class ImportCspData{

    /** @var ImportData */
    protected $helper;
    protected $logger;

    public function __construct(ImportData $helper, LoggerInterface $logger){
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute(): void{
        try{
            $this->helper->import();
        }catch(\Exception $e){
            $this->logger->error("Forix CSP Cron: " . $e->getMessage());
        }
    }
}
