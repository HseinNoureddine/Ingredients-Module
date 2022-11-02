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

use Custom\Ingredients\Model\Url;
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Custom\Ingredients\Setup\CategorySetup;
use Magento\Framework\Filter\TranslitUrl;


class Category extends AbstractResource
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
        $this->setType(CategorySetup::ENTITY_TYPE_CODE);
        $this->setConnection(CategorySetup::ENTITY_TYPE_CODE . '_read', CategorySetup::ENTITY_TYPE_CODE . '_write');
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

    protected function _beforeSave(DataObject $object)
    {
        $urlKey = $object->getUrlKey();
        if ($urlKey == '') {
            $urlKey = $object->getCategoryName();
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
            $storeId = (int)$this->_storeManager->getStore(true)->getId();
        } else {
            $storeId = (int)$object->getStoreId();
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
}
