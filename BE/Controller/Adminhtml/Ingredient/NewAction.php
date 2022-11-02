<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Controller\Adminhtml\Ingredient;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Custom\Ingredients\Model\IngredientFactory;

class NewAction extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /** @var IngredientFactory $ingredientFactory */
    protected $ingredientFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param IngredientFactory $ingredientFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        IngredientFactory $ingredientFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->ingredientFactory = $ingredientFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Ingredients::ingredient');
    }

    /**
     * Edit
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $ingredient = $this->ingredientFactory->create();
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $ingredient->addData($data);
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_Ingredients::ingredient');
        $resultPage->getConfig()->getTitle()->prepend(__('New Ingredient'));

        return $resultPage;
    }
}
