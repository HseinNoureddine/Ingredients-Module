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

use Custom\Ingredients\Api\IngredientAttributeRepositoryInterface;
use Custom\Ingredients\Api\Data\IngredientInterface;

class Repository implements IngredientAttributeRepositoryInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var AttributeRepositoryInterface
     */
    private $eavAttributeRepository;

     /**
     *
     * @param AttributeRepositoryInterface $eavAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     *
     * @return $this
     */
    public function __construct(
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->eavAttributeRepository = $eavAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        return $this->getList($this->searchCriteriaBuilder->create())->getItems();
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->eavAttributeRepository->getList(
            IngredientInterface::ENTITY,
            $searchCriteria
        );
    }

    public function get($attributeCode)
    {
        return $this->eavAttributeRepository->get(
            IngredientInterface::ENTITY,
            $attributeCode
        );
    }
}
