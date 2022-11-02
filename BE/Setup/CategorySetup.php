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
class CategorySetup extends EavSetup
{
    /**
     * Entity type for Ingredient Category EAV attributes
     */
    const ENTITY_TYPE_CODE = 'custom_ingredient_category';

    /**
     * Retrieve Entity Attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getAttributes()
    {
        $attributes = [];

        $attributes['category_name'] = [
            'type' => 'varchar',
            'label' => 'Category name',
            'input' => 'text',
            'required' => true,
            'sort_order' => 10,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
        ];

        $attributes['url_key'] = [
            'label' => 'URL key',
            'required' => false,
            'sort_order' => 11,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        ];

        $attributes['position'] = [
            'type' => 'int',
            'label' => 'Position',
            'input' => 'text',
            'required' => true,
            'sort_order' => 12,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        ];

        $attributes['category_image'] = [
            'type' => 'varchar',
            'label' => 'Category Image',
            'input' => 'image',
            'backend' => 'Custom\Ingredients\Model\Attribute\Backend\Image',
            'required' => true,
            'sort_order' => 13,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        ];

        return $attributes;
    }

    /**
     * Retrieve default entities: ingredient_category
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = [
            self::ENTITY_TYPE_CODE => [
                'entity_model' => 'Custom\Ingredients\Model\ResourceModel\Category',
                'attribute_model' => 'Custom\Ingredients\Model\ResourceModel\Eav\Attribute',
                'table' => self::ENTITY_TYPE_CODE . '_entity',
                'increment_model' => null,
                'additional_attribute_table' => self::ENTITY_TYPE_CODE . '_eav_attribute',
                'entity_attribute_collection' => 'Custom\Ingredients\Model\ResourceModel\Category\Attribute\Collection',
                'attributes' => $this->getAttributes()
            ]
        ];

        return $entities;
    }
}
