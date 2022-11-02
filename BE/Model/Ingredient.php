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

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Custom\Ingredients\Model\ResourceModel\Ingredient as IngredientResource;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManager;
use Custom\Ingredients\Model\ResourceModel\Ingredient\CollectionFactory as IngredientCollectionFactory;
use Magento\Framework\Model\AbstractExtensibleModel;
use Custom\Ingredients\Api\Data\IngredientInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Ingredient extends AbstractExtensibleModel implements IngredientInterface, IdentityInterface
{
     /**
     * @var String
     */
    const CACHE_TAG = 'custom_ingredients_ingredient';
     /**
     * @var String
     */
    protected $_cacheTag = 'custom_ingredients_ingredient';
     /**
     * @var String
     */
    protected $_eventPrefix = 'custom_ingredients_ingredient';
     /**
     * @var Url
     */
    protected $urlModel;
     /**
     * @var IngredientResource
     */
    protected $ingredientResource;
     /**
     * @var UrlInterface
     */
    protected $urlBuilder;
     /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
     /**
     * @var IngredientCollectionFactory
     */
    protected $ingredientCollectionFactory;
     /**
     * @var StoreManager
     */
    protected $storeManager;
     /**
     * @var FileInfo
     */
    protected $fileInfo;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Custom\Ingredients\Model\ResourceModel\Ingredient');
    }
    /**
     *
     * @param Url $urlModel
     * @param IngredientResource $ingredientResource
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param UrlInterface $urlBuilder
     * @param ProductCollectionFactory $productCollectionFactory
     * @param StoreManager $storeManager
     * @param IngredientCollectionFactory $ingredientCollectionFactory
     * @param FileInfo $fileInfo
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     *
     * @return $this
     */
    public function __construct(
        Url $urlModel,
        IngredientResource $ingredientResource,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        UrlInterface $urlBuilder,
        ProductCollectionFactory $productCollectionFactory,
        StoreManager $storeManager,
        IngredientCollectionFactory $ingredientCollectionFactory,
        FileInfo $fileInfo,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->urlModel = $urlModel;
        $this->ingredientResource = $ingredientResource;
        $this->urlBuilder = $urlBuilder;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->ingredientCollectionFactory = $ingredientCollectionFactory;
        $this->fileInfo = $fileInfo;
        $this->storeManager = $storeManager;
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return parent::getData(self::ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function getIngredientName()
    {
        return $this->getData(self::INGREDIENT_NAME);
    }

    public function setIngredientName($ingredientName)
    {
        return $this->setData(self::INGREDIENT_NAME, $ingredientName);
    }

    public function getLetter()
    {
        return $this->getData(self::LETTER);
    }

    public function setLetter($letter)
    {
        return $this->setData(self::LETTER, $letter);
    }

    public function setIsActive($status)
    {
        return $this->setData(self::IS_ACTIVE, $status);
    }

    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    public function getIngredientImage()
    {
        return $this->getData(self::INGREDIENT_IMAGE);
    }

    public function setIngredientImage($image)
    {
        return $this->setData(self::INGREDIENT_IMAGE, $image);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function isFeatured()
    {
        return (bool)$this->getData(self::FEATURED);
    }

    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    public function getProductIds()
    {
        return $this->getData(self::PRODUCT_IDS);
    }

    public function setProductIds(array $productIds)
    {
        return $this->setData(self::PRODUCT_IDS, $productIds);
    }

    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    public function getIngredientIds()
    {
        return $this->getData(self::INGREDIENT_IDS);
    }

    public function setIngredientIds(array $ingredientIds)
    {
        return $this->setData(self::INGREDIENT_IDS, $ingredientIds);
    }

    public function getIngredientProducts()
    {
        $ids = $this->getProductIds();
        $ids[] = 0;

        $productCollection = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', $ids);

        return $productCollection;
    }

    public function getProductIngredients($product)
    {
        $ingredientCollection = $this->ingredientCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addRelatedProductFilter($product);

        return $ingredientCollection;
    }

    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return (int)$this->_getData('store_id');
        }
        return (int)$this->storeManager->getStore()->getId();
    }

    public function setStoreId($storeId)
    {
        if (!is_numeric($storeId)) {
            $storeId = $this->storeManager->getStore($storeId)->getId();
        }
        $this->setData('store_id', $storeId);
        $this->ingredientResource->setStoreId($storeId);
        return $this;
    }

    public function saveCollection(array $data)
    {
        if (isset($data[$this->getId()])) {
            $this->addData($data[$this->getId()]);
            $this->getResource()->save($this);
        }
        return $this;
    }

    public function getIngredientUrl($useSid = true)
    {
        return $this->urlModel->getIngredientUrl($this, $useSid);
    }

    public function getImageUrl($attributeCode = 'ingredient_image')
    {
        $url = false;
        $image = $this->getData($attributeCode);
        if ($image) {
            if (is_string($image)) {
                $store = $this->storeManager->getStore();
                $isRelativeUrl = substr($image, 0, 1) === '/';
                $mediaBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                if ($isRelativeUrl) {
                    $url = $image;
                } else {
                    $url = $mediaBaseUrl
                        . ltrim(FileInfo::MEDIA_PATH, '/')
                        . '/'
                        . $image;
                }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    public function getAttributes()
    {
        return $this->ingredientResource
            ->loadAllAttributes($this)
            ->getSortedAttributes();
    }
}
