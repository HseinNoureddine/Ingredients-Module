<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Model\ResourceModel\Ingredient;

use Custom\Ingredients\Model\Ingredient;
use Custom\Ingredients\Api\Data\IngredientAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute as EntityAttribute;

class Attribute extends EntityAttribute implements IngredientAttributeInterface, ScopedAttributeInterface
{

    private $globalAttributes = [
        Ingredient::ID,
        Ingredient::URL_KEY,
        Ingredient::IS_ACTIVE,
        Ingredient::INGREDIENT_IMAGE,
        Ingredient::FEATURED
    ];

    const KEY_IS_GLOBAL = 'is_global';

    public function getIsGlobal()
    {
        return $this->_getData(self::KEY_IS_GLOBAL);
    }

    public function isScopeGlobal()
    {
        return in_array($this->getAttributeCode(), $this->globalAttributes);
    }

    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    public function __sleep()
    {
        $this->unsetData('entity_type');
        return parent::__sleep();
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
}
