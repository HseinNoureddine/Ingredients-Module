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

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\DataObject\IdentityInterface;
use Custom\Ingredients\Model\Config;
use Custom\Ingredients\Model\Url;
use Custom\Ingredients\Model\ResourceModel\Ingredient\CollectionFactory as IngredientCollectionFactory;
use Custom\Ingredients\Api\CategoryRepositoryInterface;

class View extends Template implements IdentityInterface
{
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
     * @var Config
     */
    protected $config;
    /**
     *
     * @var Url
     */
    protected $urlModel;
    /**
     *
     * @var IngredientCollectionFactory
     */
    protected $ingredientCollection;
    /**
     *
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    /**
     *
     * @var RedirectInterface
     */
    protected $redirect;
    /**
     *
     * @param IngredientCollectionFactory $ingredientCollection
     * @param CategoryRepositoryInterface $categoryRepository
     * @param RedirectInterface $redirect
     * @param Config $config
     * @param Registry $registry
     * @param Context $context
     *
     * @return $this
     */
    public function __construct(
        Url $urlModel,
        IngredientCollectionFactory $ingredientCollection,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        Config $config,
        Registry $registry,
        Context $context
    ) {
        $this->urlModel = $urlModel;
        $this->ingredientCollection = $ingredientCollection;
        $this->categoryRepository = $categoryRepository;
        $this->redirect = $redirect;
        $this->config = $config;
        $this->registry = $registry;
        parent::__construct($context);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $ingredient = $this->getIngredient();
        $metaTitle = $ingredient->getMetaTitle() ? $ingredient->getMetaTitle() : $ingredient->getIngredientName();
        $this->pageConfig->getTitle()->set($metaTitle);
        if ($ingredient->getMetaDescription()) {
            $this->pageConfig->setDescription($ingredient->getMetaDescription());
        }
        if ($ingredient->getMetaKeywords()) {
            $this->pageConfig->setKeywords($ingredient->getMetaKeywords());
        }
        $this->pageConfig->addRemotePageAsset(
            $ingredient->getIngredientUrl(),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb(
                'home', [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_urlBuilder->getBaseUrl()
                ]
            );
            $breadcrumbs->addCrumb(
                'ingredients', [
                    'label' => __('Key Ingredients'),
                    'link' => $this->urlModel->getBaseUrl()
                ]
            );
            if ($this->checkForCategoryReferer() == true) {
                $categoryTitle = $this->getCategory()->getCategoryName();
                $breadcrumbs->addCrumb(
                    $categoryTitle, [
                        'label' => $categoryTitle,
                        'link' => $this->getCategory()->getCategoryUrl()
                    ]
                );
            }
            $breadcrumbs->addCrumb(
                $ingredient->getIngredientName(), 
                ['label' => $ingredient->getIngredientName()]
            );
        }
    }

    public function getIngredient()
    {
        if (!$this->hasData('ingredient')) {
            $this->setData('ingredient', $this->registry->registry('current_ingredient'));
        }
        return $this->getData('ingredient');
    }
    
    public function getIdentities()
    {
        return $this->getIngredient()->getIdentities();
    }

    private function checkForCategoryReferer()
    {
        $refererUrl = $this->redirect->getRefererUrl();
        $categoryPart = preg_split('|(/)|', $refererUrl);

        if (isset($categoryPart[4]) && $categoryPart[4] == 'category') {
            return true;
        }
        return false;
    }

    public function getCategory()
    {
        $categoryId = $this->getIngredientCategory();
        if ($categoryId) {
            return $this->categoryRepository->getById($categoryId);
        }
        return false;
    }

    private function getIngredientCategory()
    {
        $category = $this->getIngredient()->getCategoryId();
        return $category[0];
    }

    public function getCategoryIngredients($limit = 10)
    {
        $categoryId = $this->getIngredientCategory();
        if ($categoryId) {
            $collection = $this->ingredientCollection->create()
                ->addAttributeToSelect(['ingredient_name', 'url_key', 'ingredient_image', 'is_active', 'category_id'])
                ->addAttributeToFilter('is_active', 1)
                ->addAttributeToSort('ingredient_name', 'ASC')
                ->addAttributeToSort('entity_id', 'DESC')
                ->addAttributeToFilter('category_id', $categoryId)
                ->setPageSize((int)($limit))
                ->setCurPage(1);
            return $collection;
        }
        return false;
    }
}
