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
use Custom\Ingredients\Model\ResourceModel\Category as CategoryResource;
use Magento\Setup\Exception;
use Magento\Store\Model\StoreManager;
use Magento\Framework\Model\AbstractExtensibleModel;
use Custom\Ingredients\Api\Data\CategoryInterface;
use Magento\Framework\DataObject\IdentityInterface;


class Category extends AbstractExtensibleModel implements CategoryInterface, IdentityInterface
{
    /**
     * @var String
     */
    const CACHE_TAG = 'custom_ingredients_ingredient_category';
    /**
     * @var String
     */
    protected $_cacheTag = 'custom_ingredients_ingredient_category';
    /**
     * @var String
     */
    protected $_eventPrefix = 'custom_ingredients_ingredient_category';
    /**
     * @var Url
     */
    protected $urlModel;
    /**
     * @var CategoryResource
     */
    protected $categoryResource;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
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
        $this->_init('Custom\Ingredients\Model\ResourceModel\Category');
    }
    /**
     *
     * @param Url $urlModel
     * @param CategoryResource $categoryResource
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param UrlInterface $urlBuilder
     * @param StoreManager $storeManager
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     *
     * @return $this
     */
    public function __construct(
        Url $urlModel,
        CategoryResource $categoryResource,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        UrlInterface $urlBuilder,
        StoreManager $storeManager,
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
        $this->categoryResource = $categoryResource;
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->fileInfo = $fileInfo;
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

    public function getCategoryName()
    {
        return $this->getData(self::CATEGORY_NAME);
    }

    public function setCategoryName($categoryName)
    {
        return $this->setData(self::CATEGORY_NAME, $categoryName);
    }

    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    public function getCategoryImage()
    {
        return $this->getData(self::CATEGORY_IMAGE);
    }

    public function setCategoryImage($image)
    {
        return $this->setData(self::CATEGORY_IMAGE, $image);
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
        $this->categoryResource->setStoreId($storeId);
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

    public function getCategoryUrl($useSid = true)
    {
        return $this->urlModel->getCategoryUrl($this, $useSid);
    }

    public function getImageUrl($attributeCode = 'category_image')
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
        return $this->categoryResource
            ->loadAllAttributes($this)
            ->getSortedAttributes();
    }
}
