<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Api;

use Custom\Ingredients\Api\Data\IngredientInterface;

interface IngredientRepositoryInterface
{
    /**
     * @param \Custom\Ingredients\Api\Data\IngredientInterface $ingredient
     * @return \Custom\Ingredients\Api\Data\IngredientInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(IngredientInterface $ingredient);

    /**
     * @param int $ingredientId
     * @param int|null $storeId
     * @return \Custom\Ingredients\Api\Data\IngredientInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($ingredientId, $storeId = null);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Custom\Ingredients\Api\Data\IngredientSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param \Custom\Ingredients\Api\Data\IngredientInterface $ingredient
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool Will returned True if deleted
     */
    public function delete(IngredientInterface $ingredient);
}
