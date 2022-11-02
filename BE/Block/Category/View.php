<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Block\Category;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\DataObject\IdentityInterface;
use Custom\Ingredients\Model\Config;
use Custom\Ingredients\Model\Url;
use Magento\Cms\Model\Template\FilterProvider;
use Custom\Ingredients\Model\ResourceModel\Ingredient\CollectionFactory as IngredientCollectionFactory;

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
     * @var FilterProvider
     */
    protected $filterProvider;
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
     * @param Url $urlModel
     * @param IngredientCollectionFactory $ingredientCollection
     * @param Config $config
     * @param FilterProvider $filterProvider
     * @param Registry $registry
     * @param Context $context
     *
     * @return $this
     */
    public function __construct(
        Url $urlModel,
        IngredientCollectionFactory $ingredientCollection,
        Config $config,
        FilterProvider $filterProvider,
        Registry $registry,
        Context $context
    ) {
        $this->urlModel = $urlModel;
        $this->ingredientCollection = $ingredientCollection;
        $this->config = $config;
        $this->filterProvider = $filterProvider;
        $this->registry = $registry;
        parent::__construct($context);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $category = $this->getCategory();
        $title = $category->getCategoryName();

        $this->pageConfig->getTitle()->set(__($title));

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
            $breadcrumbs->addCrumb($title, ['label' => $title ]);
        }
    }

    public function getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setData('category', $this->registry->registry('current_ingredient_category'));
        }
        return $this->getData('category');
    }

    public function getIdentities()
    {
        return $this->getCategory()->getIdentities();
    }
}
