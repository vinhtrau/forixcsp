<?php
/**
 * Created by: Bruce
 * Date: 07/10/20
 * Time: 14:43
 */

namespace Forix\Csp\Console;

use Forix\Csp\Helper\ImportData;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCspCollector extends Command{

    protected $helper;
    protected $logger;

	public function __construct(
        ImportData $helper,
	    LoggerInterface $logger,
	    string $name = null
    ){
        parent::__construct($name);
        $this->helper = $helper;
        $this->logger = $logger;
    }

    protected function configure(){
		$this->setName('forix:csp:importcsp');
		$this->setDescription('Check & Import new CSP blocked URL');
		parent::configure();
	}

    /**
	 * CLI command description
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output): void{
	    try{
            $this->helper->import();
        }catch(\Exception $e){
	        $output->writeln($e->getMessage());
            $this->logger->error("Forix CSP Console: " . $e->getMessage());
        }
	}
}
