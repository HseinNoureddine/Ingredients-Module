<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Api\Data;

interface IngredientAttributeInterface extends \Magento\Eav\Api\Data\AttributeInterface
{
    const ENTITY_TYPE_CODE = 'custom_ingredient';
    const SCOPE_STORE_TEXT = 'store';
    const SCOPE_GLOBAL_TEXT = 'global';
    const SCOPE_WEBSITE_TEXT = 'website';

    public function isScopeGlobal();
    public function isScopeWebsite();
    public function isScopeStore();
    public function getScope();
    public function setScope($scope);
}
