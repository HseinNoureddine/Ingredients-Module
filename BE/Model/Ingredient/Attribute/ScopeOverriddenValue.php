<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Model\Ingredient\Attribute;

use Custom\Ingredients\Model\Ingredient\Attribute\Repository as AttributeRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Store\Model\Store;
use Magento\Framework\App\ResourceConnection;

class ScopeOverriddenValue
{
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ScopeOverriddenValue
     */
    private $attributesValues;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     *
     * @param AttributeRepository $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param ResourceConnection $resourceConnection
     *
     * @return $this
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        ResourceConnection $resourceConnection
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->resourceConnection = $resourceConnection->getConnection();
    }

    public function containsValue($entity, $attributeCode, $storeId)
    {
        if ((int)$storeId === Store::DEFAULT_STORE_ID) {
            return false;
        }
        if ($this->attributesValues === null) {
            $this->initAttributeValues($entity, (int)$storeId);
        }

        return isset($this->attributesValues[$storeId])
            && array_key_exists($attributeCode, $this->attributesValues[$storeId]);
    }

    private function initAttributeValues($entity, $storeId)
    {
        $attributeTables = [];
        foreach ($this->getAttributes() as $attribute) {
            if (!$attribute->isStatic()) {
                $attributeTables[$attribute->getBackend()->getTable()][] = $attribute->getAttributeId();
            }
        }
        $storeIds = [Store::DEFAULT_STORE_ID];
        if ($storeId !== Store::DEFAULT_STORE_ID) {
            $storeIds[] = $storeId;
        }
        $selects = [];
        foreach ($attributeTables as $attributeTable => $attributeCodes) {
            $select = $this->resourceConnection->select()
                ->from(['t' => $attributeTable], ['value' => 't.value', 'store_id' => 't.store_id'])
                ->join(
                    ['a' => $this->resourceConnection->getTableName('eav_attribute')],
                    'a.attribute_id = t.attribute_id',
                    ['attribute_code' => 'a.attribute_code']
                )
                ->where('entity_id = ?', $entity->getId())
                ->where('t.attribute_id IN (?)', $attributeCodes)
                ->where('t.store_id IN (?)', $storeIds);
            $selects[] = $select;
        }

        $unionSelect = new \Magento\Framework\DB\Sql\UnionExpression(
            $selects,
            \Magento\Framework\DB\Select::SQL_UNION_ALL
        );
        $attributes = $this->resourceConnection->fetchAll((string)$unionSelect);
        foreach ($attributes as $attribute) {
            $this->attributesValues[$attribute['store_id']][$attribute['attribute_code']] = $attribute['value'];
        }
    }

    private function getAttributes()
    {
        $searchResult = $this->attributeRepository->getList(
            $this->searchCriteriaBuilder->addFilters([])->create()
        );
        return array_filter(
            $searchResult->getItems(), 
            function ($item) {
                return !$item->isScopeGlobal();
            }
        );
    }
}
