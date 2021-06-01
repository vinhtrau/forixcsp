<?php
/**
 * Created by: Bruce
 * Date: 27/05/2021
 * Time: 12:39
 */
namespace Forix\Csp\Controller\Adminhtml\Report;
use Magento\Backend\App;
use Magento\Framework\Controller\ResultFactory;
use Forix\Csp\Model\ResourceModel\Report;
class MassApprove extends \Magento\Backend\App\Action{

    const ADMIN_RESOURCE = 'Forix_Csp::report';

    protected $resourceModel;

    public function __construct(App\Action\Context $context, Report $resourceModel){
        parent::__construct($context);

        $this->resourceModel = $resourceModel;
    }

    public function execute(){
        $ids = $this->getRequest()->getParam('selected', []);
        $excluded = $this->getRequest()->getParam('excluded');
        if(empty($ids) && $excluded !== 'false'){
            $this->messageManager->addErrorMessage(__('Please select at least one entry'));
        } else{
            try{
                $count = empty($ids) ? 'all':count($ids);
                $this->resourceModel->massStatus($ids,1);
                $this->messageManager->addSuccessMessage(__('Approved %1 items', $count));
            }catch(\Exception $e){
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
