<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Model\ResourceModel\Category\Attribute;

use Custom\Ingredients\Api\Data\CategoryInterface;

class Collection extends \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection
{
    /**
     * Main select object initialization.
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName) The method is inherited
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getResource()->getMainTable()])
            ->where(
                'main_table.entity_type_id=?',
                $this->eavConfig->getEntityType(CategoryInterface::ENTITY)->getId()
            );

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(
            'Custom\Ingredients\Model\ResourceModel\Category\Attribute',
            'Magento\Eav\Model\ResourceModel\Entity\Attribute'
        );
    }
}
