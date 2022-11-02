<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Custom\Ingredients\Model\CategoryFactory;

class Edit extends Action
{
    /**
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
    /**
     *
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     *
     * @var CategoryFactory
     */
    protected $categoryFactory;
    /**
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     *
     * @return $this
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        CategoryFactory $categoryFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Ingredients::category');
    }

    public function execute()
    {
        // 1. Get ID
        $id = $this->getRequest()->getParam('entity_id');
        $storeId = $this->getRequest()->getParam('store');
        $category = $this->categoryFactory->create();

        // 2. Initial checking
        if ($id) {
            $category->setStoreId($storeId);
            $category->load($id);
            if (!$category->getId()) {
                $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        //  3. Set entered data if was error when we do save
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $category->addData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('entity_id', $id);

        // 5. Build edit form
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_Ingredients::category');
        $resultPage->getConfig()->getTitle()
            ->prepend(__($category->getCategoryName()));

        return $resultPage;
    }
}
