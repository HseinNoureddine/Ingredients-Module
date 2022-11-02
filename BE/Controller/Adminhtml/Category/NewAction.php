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
use Magento\Framework\View\Result\PageFactory;
use Custom\Ingredients\Model\CategoryFactory;

class NewAction extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /** @var CategoryFactory $categoryFactory */
    protected $categoryFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Ingredients::category');
    }

    /**
     * Edit
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $category = $this->categoryFactory->create();
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $category->addData($data);
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_Ingredients::category');
        $resultPage->getConfig()->getTitle()->prepend(__('New Category'));

        return $resultPage;
    }
}
