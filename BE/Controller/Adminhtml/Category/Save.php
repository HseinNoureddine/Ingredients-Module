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
use Custom\Ingredients\Api\Data\CategoryAttributeInterface;
use Custom\Ingredients\Model\CategoryFactory;
use Custom\Ingredients\Model\ResourceModel\Category\CollectionFactory;

class Save extends Action
{
    /** @var CategoryFactory $categoryFactory */
    protected $categoryFactory;

    protected $categoryCollection;

    protected $eavConfig;
    /**
     * @param Context $context
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        CollectionFactory $categoryCollection,
        \Magento\Eav\Model\Config $eavConfig = null
    ) {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollection = $categoryCollection;
        $this->eavConfig = $eavConfig
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Eav\Model\Config::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Ingredients::category');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        $data = $this->getRequest()->getParams();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id');
        if ($data) {
            $categoriesCollection = $this->categoryCollection->create()
                ->addFieldToFilter('url_key', $data['url_key']);
            $categoryCheck = $categoriesCollection->setPageSize(1)->getFirstItem();
            //temp check if url_key already exists
            if ($categoryCheck->getId() && $id != $categoryCheck->getId()) {
                $this->messageManager->addErrorMessage(__('Ingredient category with the same url_key already exists'));
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
            } else {
                $params = [];
                $category = $this->categoryFactory->create();
                $category->setStoreId($storeId);
                $params['store'] = $storeId;
                if (empty($data['entity_id'])) {
                    $data['entity_id'] = null;
                } else {
                    $category->load($data['entity_id']);
                    $params['entity_id'] = $data['entity_id'];
                }

                $imageData = $this->imagePreprocessing($data);
                $data = array_merge($data, $imageData);
                $category->addData($data);

                $this->_eventManager->dispatch(
                    'custom_ingredients_category_prepare_save',
                    ['object' => $this->categoryFactory, 'request' => $this->getRequest()]
                );

                if (isset($data['use_default']) && !empty($data['use_default'])) {
                    foreach ($data['use_default'] as $attributeCode => $attributeValue) {
                        if ($attributeValue) {
                            $category->setData($attributeCode, null);
                        }
                    }
                }

                try {
                    $category->save();
                    $this->messageManager->addSuccessMessage(__('You saved this record.'));
                    $this->_getSession()->setFormData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $params['entity_id'] = $category->getId();
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
        $entityType = $this->eavConfig->getEntityType(CategoryAttributeInterface::ENTITY_TYPE_CODE);

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
}
