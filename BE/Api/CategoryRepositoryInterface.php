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

use Custom\Ingredients\Api\Data\CategoryInterface;

interface CategoryRepositoryInterface
{
    /**
     * @param \Custom\Ingredients\Api\Data\CategoryInterface $category
     * @return \Custom\Ingredients\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CategoryInterface $category);

    /**
     * @param int $categoryId
     * @param int|null $storeId
     * @return \Custom\Ingredients\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($categoryId, $storeId = null);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Custom\Ingredients\Api\Data\IngredientSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param \Custom\Ingredients\Api\Data\CategoryInterface $category
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool Will returned True if deleted
     */
    public function delete(CategoryInterface $category);
}
