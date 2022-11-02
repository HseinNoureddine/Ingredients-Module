<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Custom\Ingredients\Api\Data\IngredientSearchResultsInterfaceFactory;
use Custom\Ingredients\Model\ResourceModel\Ingredient as IngredientResource;
use Custom\Ingredients\Model\ResourceModel\Ingredient\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class IngredientRepository implements \Custom\Ingredients\Api\IngredientRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $ingredientCollection;
    /**
     * @var IngredientSearchResultsInterfaceFactory
     */
    private $searchResultFactory;
    /**
     * @var IngredientResource
     */
    private $ingredientResource;
    /**
     * @var IngredientFactory
     */
    private $ingredientFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     *
     * @param IngredientResource $ingredientResource
     * @param CollectionFactory $ingredientCollection
     * @param IngredientSearchResultsInterfaceFactory $searchResultFactory
     * @param IngredientFactory $ingredientFactory
     * @param StoreManagerInterface $storeManager
     *
     * @return $this
     */
    public function __construct(
        IngredientResource $ingredientResource,
        CollectionFactory $ingredientCollection,
        IngredientSearchResultsInterfaceFactory $searchResultFactory,
        IngredientFactory $ingredientFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->ingredientResource = $ingredientResource;
        $this->ingredientCollection = $ingredientCollection;
        $this->searchResultFactory = $searchResultFactory;
        $this->ingredientFactory = $ingredientFactory;
        $this->storeManager = $storeManager;
    }

    public function save(\Custom\Ingredients\Api\Data\IngredientInterface $ingredient)
    {
        if ($ingredient->getStoreId() === null) {
            $storeId = $this->storeManager->getStore()->getId();
            $ingredient->setStoreId($storeId);
        }
        try {
            $this->ingredientResource->save($ingredient);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save ingredient: %1', $exception->getMessage()),
                $exception
            );
        }
        return $ingredient;
    }

    public function getById($ingredientId, $storeId = null)
    {
        $ingredient = $this->ingredientFactory->create();
        if ($storeId !== null) {
            $ingredient->setData('store_id', $storeId);
        }
        $ingredient->load($ingredientId);
        if (!$ingredient->getId()) {
            throw NoSuchEntityException::singleField('id', $ingredientId);
        }
        return $ingredient;
    }

    public function delete(\Custom\Ingredients\Api\Data\IngredientInterface $ingredient)
    {
        try {
            $this->ingredientResource->delete($ingredient);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete ingredient: %1',
                    $ingredient->getId()
                ),
                $exception
            );
        }
        return true;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->ingredientCollection->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, CollectionFactory $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, CollectionFactory $collection)
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, CollectionFactory $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, CollectionFactory $collection)
    {
        $searchResults = $this->searchResultFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
