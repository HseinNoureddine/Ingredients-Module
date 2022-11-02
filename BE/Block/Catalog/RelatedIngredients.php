<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Block\Catalog;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Custom\Ingredients\Model\ResourceModel\Ingredient\CollectionFactory as IngredientCollectionFactory;

class RelatedIngredients extends Template
{
    /**
     *
     * @var IngredientCollectionFactory
     */
    protected $ingredientCollectionFactory;
    /**
     *
     * @var Registry
     */
    protected $registry;
    /**
     *
     * @param IngredientCollectionFactory $ingredientCollectionFactory
     * @param Registry $registry
     * @param Context $context
     *
     * @return $this
     */
    public function __construct(
        IngredientCollectionFactory $ingredientCollectionFactory,
        Registry $registry,
        Context $context
    ) {
        $this->ingredientCollectionFactory = $ingredientCollectionFactory;
        $this->registry = $registry;
        parent::__construct($context);
        $this->setTabTitle();
    }

    public function setTabTitle()
    {
        $size = $this->getCollection()->getSize();
        $title = $size
            ? __('Key Ingredients %1', '<span class="counter">' . $size . '</span>')
            : '';
        $this->setTitle($title);
    }
 
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getCollection()
    {
        $collection = $this->ingredientCollectionFactory->create()
            ->addAttributeToSelect(['ingredient_name', 'ingredient_image', 'url_key'])
            ->setStore($this->_storeManager->getStore()->getId())
            ->addFieldToFilter('is_active', 1)
            ->addAttributeToSort('date', 'DESC')
            ->addAttributeToSort('entity_id', 'DESC');
        if ($product = $this->getProduct()) {
            $collection->addRelatedProductFilter($product);
        }
        return $collection;
    }
}
