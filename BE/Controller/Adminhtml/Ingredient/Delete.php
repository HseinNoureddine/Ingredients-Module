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
use Custom\Ingredients\Model\IngredientFactory;

class Delete extends Action
{
    /** @var IngredientFactory $ingredientFactory */
    protected $ingredientFactory;

    /**
     * @param Context $context
     * @param IngredientFactory $ingredientFactory
     */
    public function __construct(
        Context $context,
        IngredientFactory $ingredientFactory
    ) {
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
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id', null);

        try {
            $ingredient = $this->ingredientFactory->create()->load($id);
            if ($ingredient->getId()) {
                $ingredient->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the record.'));
            } else {
                $this->messageManager->addErrorMessage(__('Record does not exist.'));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        
        return $resultRedirect->setPath('*/*');
    }
}
