<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Ui\Component\Ingredient\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Custom\Ingredients\Model\ResourceModel\Category\CollectionFactory as CategoryCollection;

class Categories extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $categoryCollection;

    /**
     * Categories constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CategoryCollection $categoryCollection
     * @param array $components
     * @param array $data
     */

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CategoryCollection $categoryCollection,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->categoryCollection = $categoryCollection;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');

        if (isset($dataSource['data']['items'])) {
            $categoryIds = [];
            foreach ($dataSource['data']['items'] as &$item) {
                if(array_key_exists('category_id', $item)){
                    $categoryIds[] = $item['category_id'];
                }
            }
            if (!empty($categoryIds)) {
                $collection = $this->categoryCollection->create()
                    ->addFieldToSelect('category_name')
                    ->addFieldToFilter('entity_id', ['in' => $categoryIds])
                    ->setStore($this->context->getRequestParam('store', 0));
                $categories = [];
                foreach ($collection as $category) {
                    $categories[$category->getId()] = $category->getCategoryName();
                }
                foreach ($dataSource['data']['items'] as &$item) {
                    if(!array_key_exists('category_id', $item))$item['category_id'] = 1;
                    $item[$fieldName] = $categories[$item['category_id']];
                }
            }
        }

        return $dataSource;
    }
}
