<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Block\Ingredient;

use Custom\Ingredients\Model\Config;
use Custom\Ingredients\Model\ResourceModel\Ingredient\CollectionFactory as IngredientCollectionFactory;
use Custom\Ingredients\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class IngredientList extends Template implements IdentityInterface
{
    /**
     *
     * @var IngredientCollectionFactory
     */
    protected $ingredientCollection;
    /**
     *
     * @var CategoryCollectionFactory
     */
    protected $categoryCollection;
    /**
     *
     * @var Config
     */
    protected $config;
    /**
     *
     * @var Context
     */
    protected $context;
    /**
     *
     * @var Registry
     */
    protected $registry;
    /**
     *
     * @param IngredientCollectionFactory $ingredientCollection
     * @param CategoryCollectionFactory $categoryCollection
     * @param Context $context
     * @param Config $config
     * @param Registry $registry
     *
     * @return $this
     */
    public function __construct(
        IngredientCollectionFactory $ingredientCollection,
        CategoryCollectionFactory $categoryCollection,
        Context $context,
        Config $config,
        Registry $registry,
        array $data = []
    ) {
        $this->ingredientCollection = $ingredientCollection;
        $this->categoryCollection = $categoryCollection;
        $this->config = $config;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig
            ->getTitle()
            ->set($this->config->getIngredientBaseMetaTitle());
        $this->pageConfig
            ->setDescription($this->config->getIngredientBaseMetaDescription());
        $this->pageConfig
            ->setKeywords($this->config->getIngredientBaseMetaKeywords());
    }

    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getIngredientCollection() as $ingredient) {
            $identities = array_merge($identities, $ingredient->getIdentities());
        }
        return $identities;
    }


    public function getIngredientCollection()
    {
        $collection = $this->ingredientCollection->create()
            ->addAttributeToSelect(['ingredient_name', 'url_key', 'ingredient_image'])
            ->addAttributeToSort('ingredient_name', 'ASC')
            ->addAttributeToSort('entity_id', 'DESC')
            ->addAttributeToFilter('is_active', 1)
            ->setStore($this->_storeManager->getStore()->getId());

        $category = $this->getCategory();
        $letter = $this->getLetterKey();

        if ($category && $category->getId()) {
            $collection->addAttributeToFilter('category_id', $category->getId());
        } elseif ($letter) {
            $collection->addAttributeToFilter('letter', $letter);
        }

        return $collection;
    }

    public function getCategories()
    {
        $categories = $this->categoryCollection->create()
            ->addAttributeToSelect(['category_name', 'category_image', 'url_key'])
            ->addAttributeToSort('position', 'DESC')
            ->setStore($this->_storeManager->getStore()->getId());
        return $categories;
    }

    public function getLetters()
    {
        $attributeCode = 'letter';
        $ingredients = $this->ingredientCollection->create()
            ->addAttributeToFilter($attributeCode, ['notnull' => true])
            ->addAttributeToFilter($attributeCode, ['neq' => ''])
            ->addAttributeToSelect($attributeCode);
        $letters = array_unique($ingredients->getColumnValues($attributeCode));
        sort($letters);

        $url = $this->getBaseIngredientsUrl();
        $items = [];
        foreach ($letters as $letter) {
            $items[$letter] = $url.'?letter='.$letter;
        }

        return $items;
    }

    public function getCategory()
    {
        return $this->registry->registry('current_ingredient_category');
    }

    public function getLetterKey()
    {
        $letter = $this->getData('letter');
        if ($letter == null) {
            $letter = $this->getRequest()->getParam('letter');
        }
        return strtoupper($letter);
    }

    public function getCurrentCategory()
    {
        return $this->getRequest()->getParam('entity_id');
    }

    public function getBaseIngredientsUrl()
    {
        return $this->getBaseUrl() . 'ingredients/';
    }
}
