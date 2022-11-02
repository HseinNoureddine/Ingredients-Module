<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

/**
 * @codeCoverageIgnore
 */
class IngredientSetup extends EavSetup
{
    /**
     * Entity type for Ingredient EAV attributes
     */
    const ENTITY_TYPE_CODE = 'custom_ingredient';

    /**
     * Retrieve Entity Attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getAttributes()
    {
        $attributes = [];

        $attributes['category_id'] = [
            'type' => 'int',
            'label' => 'Category',
            'input' => 'select',
            'required' => false,
            'source' => 'Custom\Ingredients\Model\Ingredient\Attribute\Source\CategorySelection',
            'sort_order' => 1,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        ];

        $attributes['ingredient_name'] = [
            'type' => 'varchar',
            'label' => 'Ingredient name',
            'input' => 'text',
            'required' => true,
            'sort_order' => 10,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
        ];

        $attributes['letter'] = [
            'type' => 'varchar',
            'label' => 'Letter',
            'input' => 'text',
            'required' => true,
            'sort_order' => 11,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
        ];

        $attributes['is_active'] = [
            'type' => 'int',
            'label' => 'Is Active',
            'input' => 'select',
            'required' => false,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'sort_order' => 12,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        ];

        $attributes['url_key'] = [
            'label' => 'URL key',
            'required' => false,
            'sort_order' => 13,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        ];

        $attributes['ingredient_image'] = [
            'type' => 'varchar',
            'label' => 'Image',
            'input' => 'image',
            'backend' => 'Custom\Ingredients\Model\Attribute\Backend\Image',
            'required' => true,
            'sort_order' => 14,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        ];

        $attributes['description'] = [
            'type' => 'varchar',
            'label' => 'Description',
            'input' => 'text',
            'required' => false,
            'sort_order' => 15,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'General',
        ];

        $attributes['featured'] = [
            'type' => 'int',
            'label' => 'Featured',
            'input' => 'select',
            'required' => false,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'sort_order' => 16,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        ];

        $attributes['meta_title'] = [
            'type' => 'varchar',
            'label' => 'Meta Title',
            'input' => 'text',
            'required' => false,
            'sort_order' => 17,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
        ];

        $attributes['meta_keywords'] = [
            'type' => 'varchar',
            'label' => 'Meta Keywords',
            'input' => 'text',
            'required' => false,
            'sort_order' => 18,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
        ];

        $attributes['meta_description'] = [
            'type' => 'text',
            'label' => 'Meta description',
            'input' => 'multiline',
            'required' => false,
            'multiline_count' => 5,
            'sort_order' => 19,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
        ];

        return $attributes;
    }
    /**
     * Retrieve default entities: ingredient
     *
     * @return array
     */

    public function getDefaultEntities()
    {
        $entities = [
            self::ENTITY_TYPE_CODE => [
                'entity_model' => 'Custom\Ingredients\Model\ResourceModel\Ingredient',
                'attribute_model' => 'Custom\Ingredients\Model\ResourceModel\Eav\Attribute',
                'table' => self::ENTITY_TYPE_CODE . '_entity',
                'increment_model' => null,
                'additional_attribute_table' => self::ENTITY_TYPE_CODE . '_eav_attribute',
                'entity_attribute_collection' => 'Custom\Ingredients\Model\ResourceModel\Ingredient\Attribute\Collection',
                'attributes' => $this->getAttributes()
            ]
        ];

        return $entities;
    }
}
