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
use Custom\Ingredients\Api\Data\IngredientAttributeInterface;
use Custom\Ingredients\Model\IngredientFactory;
use Custom\Ingredients\Model\ResourceModel\Ingredient\CollectionFactory;

class Save extends Action
{
    /**
     * @var IngredientFactory
     */
    protected $ingredientFactory;
    /**
     * @var CollectionFactory
     */
    protected $ingredientCollection;
    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @param Context $context
     * @param IngredientFactory $ingredientFactory
     * @param CollectionFactory $ingredientCollection
     * @param Config $eavConfig
     */
    public function __construct(
        Context $context,
        IngredientFactory $ingredientFactory,
        CollectionFactory $ingredientCollection,
        \Magento\Eav\Model\Config $eavConfig = null
    ) {
        parent::__construct($context);
        $this->ingredientFactory = $ingredientFactory;
        $this->ingredientCollection = $ingredientCollection;
        $this->eavConfig = $eavConfig ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Eav\Model\Config::class);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Ingredients::ingredient');
    }

    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        $data = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id');
        if ($data) {
            $ingredientsCollection = $this->ingredientCollection->create()->addFieldToFilter('url_key', $data['url_key']);
            $ingredientCheck = $ingredientsCollection->setPageSize(1)->getFirstItem();
            if ($ingredientCheck->getId() && $id != $ingredientCheck->getId()) {
                $this->messageManager->addErrorMessage(__('Ingredient with the same url_key already exists'));
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
            } else {
                $params = [];
                $ingredient = $this->ingredientFactory->create();
                $ingredient->setStoreId($storeId);
                $params['store'] = $storeId;
                if (empty($data['entity_id'])) {
                    $data['entity_id'] = null;
                } else {
                    $ingredient->load($data['entity_id']);
                    $params['entity_id'] = $data['entity_id'];
                }

                $imageData = $this->imagePreprocessing($data);
                $productData = $this->productData($data);
                $data = array_merge($data, $imageData, $productData);
                $ingredient->addData($data);

                $this->_eventManager->dispatch(
                    'custom_ingredients_ingredient_prepare_save',
                    ['object' => $this->ingredientFactory, 'request' => $this->getRequest()]
                );

                if (isset($data['use_default']) && !empty($data['use_default'])) {
                    foreach ($data['use_default'] as $attributeCode => $attributeValue) {
                        if ($attributeValue) {
                            $ingredient->setData($attributeCode, null);
                        }
                    }
                }

                try {
                    $ingredient->save();
                    $this->messageManager->addSuccessMessage(__('You saved this record.'));
                    $this->_getSession()->setFormData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $params['entity_id'] = $ingredient->getId();
                        $params['_current'] = true;
                        return $resultRedirect->setPath('*/*/edit', $params);
                    }
                    return $resultRedirect->setPath('*/*/');
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
                }
                $this->_getSession()->setFormData($this->getRequest()->getPostValue());
                return $resultRedirect->setPath('*/*/edit', $params);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function imagePreprocessing($data)
    {
        $entityType = $this->eavConfig->getEntityType(IngredientAttributeInterface::ENTITY_TYPE_CODE);

        foreach ($entityType->getAttributeCollection() as $attributeModel) {
            $attributeCode = $attributeModel->getAttributeCode();
            $backendModel = $attributeModel->getBackend();
            if (isset($data[$attributeCode])) {
                continue;
            }
            if (!$backendModel instanceof \Custom\Ingredients\Model\Attribute\Backend\Image) {
                continue;
            }
            $data[$attributeCode] = '';
        }
        return $data;
    }

    protected function productData(array $data)
    {
        if (isset($data['ingredients_ingredient_form_product_listing'])) {
            $productIds = [];
            foreach ($data['ingredients_ingredient_form_product_listing'] as $product) {
                $productIds[] = $product['entity_id'];
            }
            $data['product_ids'] = $productIds;
        }
        return $data;
    }
}
