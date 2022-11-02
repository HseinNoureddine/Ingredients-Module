<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Model\ResourceModel\Eav;

use Custom\Ingredients\Api\Data\IngredientAttributeInterface;
use Magento\Eav\Model\Entity\Attribute as EavAttribute;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class Attribute extends EavAttribute implements IngredientAttributeInterface, ScopedAttributeInterface
{
    const MODULE_NAME = 'Custom_Ingredients';
    const KEY_IS_GLOBAL = 'is_global';
    const KEY_IS_STATIC = 'static';

    protected $_eventObject = 'attribute';
    protected static $_labels = null;
    protected $_eventPrefix = 'custom_ingredient_entity_attribute';

    protected function _construct()
    {
        $this->_init('Custom\Ingredients\Model\ResourceModel\Attribute');
    }

    public function beforeSave()
    {
        $this->setData('modulePrefix', self::MODULE_NAME);
        if (isset($this->_origData[self::KEY_IS_GLOBAL])) {
            if (!isset($this->_data[self::KEY_IS_GLOBAL])) {
                $this->_data[self::KEY_IS_GLOBAL] = self::SCOPE_GLOBAL;
            }
        }
        return parent::beforeSave();
    }

    public function afterSave() 
    {
        $this->_eavConfig->clear();
        return parent::afterSave();
    }

    public function getIsGlobal()
    {
        return $this->_getData(self::KEY_IS_GLOBAL);
    }

    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    public function getStoreId()
    {
        $dataObject = $this->getDataObject();
        if ($dataObject) {
            return $dataObject->getStoreId();
        }
        return $this->getData('store_id');
    }

    public function getScope()
    {
        if ($this->isScopeGlobal()) {
            return self::SCOPE_GLOBAL_TEXT;
        } elseif ($this->isScopeWebsite()) {
            return self::SCOPE_WEBSITE_TEXT;
        } else {
            return self::SCOPE_STORE_TEXT;
        }
    }

    public function setScope($scope)
    {
        if ($scope == self::SCOPE_GLOBAL_TEXT) {
            return $this->setData(self::KEY_IS_GLOBAL, self::SCOPE_GLOBAL);
        } elseif ($scope == self::SCOPE_WEBSITE_TEXT) {
            return $this->setData(self::KEY_IS_GLOBAL, self::SCOPE_WEBSITE);
        } elseif ($scope == self::SCOPE_STORE_TEXT) {
            return $this->setData(self::KEY_IS_GLOBAL, self::SCOPE_STORE);
        } else {
            return $this;
        }
    }

    public function getSourceModel()
    {
        $model = $this->getData('source_model');
        if (empty($model)) {
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
                return $this->_getDefaultSourceModel();
            }
        }
        return $model;
    }

    public function _getDefaultSourceModel()
    {
        return 'Magento\Eav\Model\Entity\Attribute\Source\Table';
    }

    public function afterDelete()
    {
        $this->_eavConfig->clear();
        return parent::afterDelete();
    }

    public function __sleep()
    {
        $this->unsetData('entity_type');
        return parent::__sleep();
    }
}
