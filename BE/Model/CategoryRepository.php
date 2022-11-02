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
use Custom\Ingredients\Api\Data\CategorySearchResultsInterfaceFactory;
use Custom\Ingredients\Model\ResourceModel\Category as CategoryResource;
use Custom\Ingredients\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class CategoryRepository implements \Custom\Ingredients\Api\CategoryRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $categoryCollection;
    /**
     * @var CategorySearchResultsInterfaceFactory
     */
    private $searchResultFactory;
    /**
     * @var CategoryResource
     */
    private $categoryResource;
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     *
     * @param CategoryResource $categoryResource
     * @param CollectionFactory $categoryCollection
     * @param CategorySearchResultsInterfaceFactory $searchResultFactory
     * @param CategoryFactory $categoryFactory
     * @param StoreManagerInterface $storeManager
     *
     * @return $this
     */
    public function __construct(
        CategoryResource $categoryResource,
        CollectionFactory $categoryCollection,
        CategorySearchResultsInterfaceFactory $searchResultFactory,
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryResource = $categoryResource;
        $this->categoryCollection = $categoryCollection;
        $this->searchResultFactory = $searchResultFactory;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
    }

    public function save(\Custom\Ingredients\Api\Data\CategoryInterface $category)
    {
        if ($category->getStoreId() === null) {
            $storeId = $this->storeManager->getStore()->getId();
        $category->setStoreId($storeId);
        }
        try {
            $this->categoryResource->save($category);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save category: %1', $exception->getMessage()),
                $exception
            );
        }
        return $category;
    }

    public function getById($categoryId, $storeId = null)
    {
        $category = $this->categoryFactory->create();
        if ($storeId !== null) {
            $category->setData('store_id', $storeId);
        }
        $category->load($categoryId);
        if (!$category->getId()) {
            throw NoSuchEntityException::singleField('id', $categoryId);
        }
        return $category;
    }

    public function delete(\Custom\Ingredients\Api\Data\CategoryInterface $category)
    {
        try {
            $this->categoryResource->delete($category);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete category: %1',
                    $category->getId()
                ),
                $exception
            );
        }
        return true;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->categoryCollection->create();

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
