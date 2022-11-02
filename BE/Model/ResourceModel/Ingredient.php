<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Model\ResourceModel;

use Custom\Ingredients\Api\Data\IngredientInterface;
use Custom\Ingredients\Model\Url;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Custom\Ingredients\Setup\IngredientSetup;
use Magento\Framework\Filter\TranslitUrl;

class Ingredient extends AbstractResource
{
    /**
     * @var Mixed
     */
    protected $_storeId = null;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var Url
     */
    protected $urlModel;
    /**
     * @var TranslitUrl
     */
    protected $translitUrl;
    /**
     *
     * @param Context $context
     * @param Url $urlModel
     * @param TranslitUrl $translitUrl
     * @param StoreManagerInterface $storeManager
     * @param array $data
     *
     * @return $this
     */
    public function __construct(
        Context $context,
        Url $urlModel,
        TranslitUrl $translitUrl,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $storeManager, $data);
        $this->setType(IngredientSetup::ENTITY_TYPE_CODE);
        $this->setConnection(IngredientSetup::ENTITY_TYPE_CODE . '_read', IngredientSetup::ENTITY_TYPE_CODE . '_write');
        $this->urlModel = $urlModel;
        $this->translitUrl = $translitUrl;
        $this->_storeManager = $storeManager;
    }

    protected function _getDefaultAttributes()
    {
        return [
            'created_at',
            'updated_at'
        ];
    }

    protected function _afterLoad(DataObject $object)
    {
        $object->setProductIds($this->getProductIds($object));
        $object->setCategoryId($this->getCategoryId($object));
        return parent::_afterLoad($object);
    }

    protected function _afterSave(DataObject $object)
    {
        $this->saveProductIds($object);
        $this->saveCategoryId($object);
        return parent::_afterSave($object);
    }

    protected function _beforeSave(DataObject $object)
    {
        $urlKey = $object->getUrlKey();
        if ($urlKey == '') {
            $urlKey = $object->getIngredientName();
        }
        $object->setUrlKey($this->translitUrl->filter($urlKey));

        return parent::_beforeSave($object);
    }

  
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    protected function _saveAttribute($object, $attribute, $value)
    {
        $table = $attribute->getBackend()->getTable();
        if (!isset($this->_attributeValuesToSave[$table])) {
            $this->_attributeValuesToSave[$table] = [];
        }

        $entityIdField = $attribute->getBackend()->getEntityIdField();
        $storeId = $object->getStoreId() ?: Store::DEFAULT_STORE_ID;
        $data = [
            $entityIdField => $object->getId(),
            'attribute_id' => $attribute->getId(),
            'value' => $this->_prepareValueForSave($value, $attribute),
            'store_id' => $storeId,
        ];

        if (!$this->getEntityTable() || $this->getEntityTable() == \Magento\Eav\Model\Entity::DEFAULT_ENTITY_TABLE) {
            $data['entity_type_id'] = $object->getEntityTypeId();
        }

        if ($attribute->isScopeStore()) {
            $this->_attributeValuesToSave[$table][] = $data;
        } elseif ($attribute->isScopeWebsite() && $storeId != Store::DEFAULT_STORE_ID) {
            $storeIds = $this->_storeManager->getStore($storeId)->getWebsite()->getStoreIds(true);
            foreach ($storeIds as $storeId) {
                $data['store_id'] = (int)$storeId;
                $this->_attributeValuesToSave[$table][] = $data;
            }
        } else {
            $data['store_id'] = Store::DEFAULT_STORE_ID;
            $this->_attributeValuesToSave[$table][] = $data;
        }

        return $this;
    }

    protected function _getLoadAttributesSelect($object, $table)
    {
        if ($this->_storeManager->hasSingleStore()) {
            $storeId = (int) $this->_storeManager->getStore(true)->getId();
        } else {
            $storeId = (int) $object->getStoreId();
        }

        $storeIds = [Store::DEFAULT_STORE_ID];
        if ($storeId != Store::DEFAULT_STORE_ID) {
            $storeIds[] = $storeId;
        }

        $select = $this->getConnection()
            ->select()
            ->from(['attr_table' => $table], [])
            ->where("attr_table.{$this->getLinkField()} = ?", $object->getData($this->getLinkField()))
            ->where('attr_table.store_id IN (?)', $storeIds);

        return $select;
    }

    public function getProductIds(IngredientInterface $model)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('custom_ingredient_product'),
            'product_id'
        )->where(
            'ingredient_id = ?',
            (int)$model->getId()
        );
        return $connection->fetchCol($select);
    }

    private function saveProductIds(IngredientInterface $model)
    {
        $connection = $this->getConnection();
        $table = $this->getTable('custom_ingredient_product');
        if (!$model->getProductIds()) {
            return $this;
        }
        $productIds = $model->getProductIds();
        $oldProductIds = $this->getProductIds($model);
        $insert = array_diff($productIds, $oldProductIds);
        $delete = array_diff($oldProductIds, $productIds);
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $productId) {
                if (empty($productId)) {
                    continue;
                }
                $data[] = [
                    'product_id' => (int)$productId,
                    'ingredient_id'    => (int)$model->getId(),
                ];
            }
            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }
        if (!empty($delete)) {
            foreach ($delete as $productId) {
                $where = ['ingredient_id = ?' => (int)$model->getId(), 'product_id = ?' => (int)$productId];
                $connection->delete($table, $where);
            }
        }
        return $this;
    }

    public function getCategoryId(IngredientInterface $model)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('custom_ingredient_ingredients_category'),
            'category_id'
        )->where(
            'ingredient_id = ?',
            (int)$model->getId()
        );
        return $connection->fetchCol($select);
    }

    private function saveCategoryId(IngredientInterface $model)
    {
        $connection = $this->getConnection();
        $table = $this->getTable('custom_ingredient_ingredients_category');
        if (!$model->getCategoryId()) {
            return $this;
        }
        $categoryId = [];
        $categoryId[] = $model->getCategoryId();
        $oldCategoryId = $this->getCategoryId($model);
        $insert = array_diff($categoryId, $oldCategoryId);
        $delete = array_diff($oldCategoryId, $categoryId);
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }
                $data[] = [
                    'category_id' => (int)$categoryId,
                    'ingredient_id' => (int)$model->getId(),
                ];
                if ($data) {
                    $connection->insertMultiple($table, $data);
                }
            }
        }
        if (!empty($delete)) {
            foreach ($delete as $categoryId) {
                $where = ['ingredient_id = ?' => (int)$model->getId(), 'category_id = ?' => (int)$categoryId];
                $connection->delete($table, $where);
            }
        }
        return $this;
    }

    public function saveProductIngredientIds($ingredientIds, ProductInterface $product)
    {
        $table = $this->getTable('custom_ingredient_product');
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('custom_ingredient_product'),
            'ingredient_id'
        )->where(
            'product_id = ?',
            (int)$product->getId()
        );
        $oldIngredientIds = $connection->fetchCol($select);

        $insert = array_diff($ingredientIds, $oldIngredientIds);
        $delete = array_diff($oldIngredientIds, $ingredientIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $ingredientId) {
                if (empty($ingredientId)) {
                    continue;
                }
                $data[] = [
                    'ingredient_id' => (int)$ingredientId,
                    'product_id' => (int)$product->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }
        if (!empty($delete)) {
            foreach ($delete as $ingredientId) {
                $where = ['ingredient_id = ?' => (int)$ingredientId, 'product_id = ?' => (int)$product->getId()];
                $connection->delete($table, $where);
            }
        }
        return $this;
    }
}
