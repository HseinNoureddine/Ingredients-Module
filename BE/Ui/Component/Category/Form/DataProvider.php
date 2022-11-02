<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Ui\Component\Category\Form;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Custom\Ingredients\Model\Category;
use Custom\Ingredients\Model\CategoryFactory;
use Custom\Ingredients\Model\ResourceModel\Category\CollectionFactory;
use Custom\Ingredients\Model\Attribute\Backend\Image as ImageBackendModel;
use Custom\Ingredients\Model\FileInfo;
use Custom\Ingredients\Model\ResourceModel\Category\Collection;
use Custom\Ingredients\Model\Category\Attribute\ScopeOverriddenValue;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\EavValidationRules;
use Custom\Ingredients\Api\CategoryAttributeRepositoryInterface;
use Custom\Ingredients\Api\Data\IngredientAttributeInterface;
use Custom\Ingredients\Model\ResourceModel\Eav\Attribute as IngredientAttribute;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Eav\Model\Entity\Type;
use Magento\Eav\Model\Config;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Field;
use Custom\Ingredients\Model\ResourceModel\Category\Attribute\CollectionFactory as AttributeCollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var String
     */
    protected $requestScopeFieldName = 'store';
    /**
     * @var ScopeOverriddenValue
     */
    private $scopeOverriddenValue;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CategoryAttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var CollectionFactory
     */
    protected $collection;
    /**
     * @var Mixed
     */
    protected $loadedData;
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var Config
     */
    protected $eavConfig;
    /**
     * @var ArrayManager
     */
    private $arrayManager;
    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollection;
    /**
     * @var EavValidationRules
     */
    private $eavValidationRules;
    /**
     * @var FileInfo
     */
    private $fileInfo;
    /**
     * @var Array
     */
    protected $metaProperties = [
        'dataType' => 'frontend_input',
        'visible' => 'is_visible',
        'required' => 'is_required',
        'label' => 'frontend_label',
        'sortOrder' => 'sort_order',
        'notice' => 'note',
        'default' => 'default_value',
        'size' => 'multiline_count',
    ];
    /**
     * @var Array
     */
    protected $formElement = [
        'text' => 'input',
        'boolean' => 'checkbox',
    ];
    /**
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param CategoryFactory $categoryFactory
     * @param FilterPool $filterPool
     * @param RequestInterface $request
     * @param ScopeOverriddenValue $scopeOverriddenValue
     * @param StoreManagerInterface $storeManagerInterface
     * @param CategoryAttributeRepositoryInterface $attributeRepositoryInterface
     * @param EavValidationRules $eavValidationRules
     * @param Config $eavConfig
     * @param ArrayManager $arrayManager
     * @param AttributeCollectionFactory $attributeCollection
     * @param FileInfo $fileInfo
     * @param Array $meta
     * @param Array $data
     *
     * @return $this
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        CategoryFactory $categoryFactory,
        FilterPool $filterPool,
        RequestInterface $request,
        ScopeOverriddenValue $scopeOverriddenValue,
        StoreManagerInterface $storeManagerInterface,
        CategoryAttributeRepositoryInterface $attributeRepositoryInterface,
        EavValidationRules $eavValidationRules,
        Registry $registry,
        Config $eavConfig,
        ArrayManager $arrayManager,
        AttributeCollectionFactory $attributeCollection,
        FileInfo $fileInfo,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->collection->addAttributeToSelect('*');
        $this->categoryFactory = $categoryFactory;
        $this->filterPool = $filterPool;
        $this->request = $request;
        $this->scopeOverriddenValue = $scopeOverriddenValue;
        $this->storeManager = $storeManagerInterface;
        $this->attributeRepository = $attributeRepositoryInterface;
        $this->eavValidationRules = $eavValidationRules;
        $this->registry = $registry;
        $this->eavConfig = $eavConfig;
        $this->arrayManager = $arrayManager;
        $this->attributeCollection = $attributeCollection;
        $this->fileInfo = $fileInfo;
    }

    /**
     * @inheritdoc
     * @since 101.1.0
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta = $this->prepareMeta($meta);

        $category = $this->getCurrentCategory();

        if ($category) {
            $meta = $this->addUseDefaultValueCheckbox($category, $meta);
        }

        return $meta;
    }

    /**
     * @param Category $category
     * @param array $meta
     * @return array
     */
    private function addUseDefaultValueCheckbox(Category $category, array $meta)
    {
        /** @var IngredientAttributeInterface $attribute */
        foreach ($category->getAttributes() as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $canDisplayUseDefault = $attribute->getScope() != IngredientAttribute::SCOPE_GLOBAL_TEXT
                && $category->getId()
                && $category->getStoreId();
            $attributePath = $this->arrayManager->findPath($attributeCode, $meta);

            if (!$attributePath || !$canDisplayUseDefault) {
                continue;
            }

            $meta = $this->arrayManager->merge(
                [$attributePath, 'arguments/data/config'],
                $meta,
                [
                    'service' => [
                        'template' => 'ui/form/element/helper/service',
                    ],
                    'disabled' => !$this->scopeOverriddenValue->containsValue(
                        $category,
                        $attributeCode,
                        $this->request->getParam($this->requestScopeFieldName, Store::DEFAULT_STORE_ID)
                    )
                ]
            );
        }

        return $meta;
    }

    /**
     * Prepare meta data
     *
     * @param array $meta
     * @return array
     * @since 101.0.0
     */
    public function prepareMeta($meta)
    {
        $meta = array_replace_recursive($meta, $this->prepareFieldsMeta(
            $this->getFieldsMap(),
            $this->getAttributesMeta($this->eavConfig->getEntityType('custom_ingredient_category'))
        ));

        return $meta;
    }

    /**
     * Prepare fields meta based on xml declaration of form and fields metadata
     *
     * @param array $fieldsMap
     * @param array $fieldsMeta
     * @return array
     */
    private function prepareFieldsMeta($fieldsMap, $fieldsMeta)
    {
        $result = [];
        foreach ($fieldsMap as $fieldSet => $fields) {
            foreach ($fields as $field) {
                if (isset($fieldsMeta[$field])) {
                    $result[$fieldSet]['children'][$field]['arguments']['data']['config'] = $fieldsMeta[$field];
                }
            }
        }
        return $result;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $category = $this->getCurrentCategory();
        if ($category) {
            $categoryData = $category->getData();
            $categoryData = $this->categoryImagesData($category, $categoryData);

            $this->loadedData[$category->getId()] = $categoryData;
        }

        return $this->loadedData;
    }

    /**
     * Get attributes meta
     *
     * @param Type $entityType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @since 101.0.0
     */
    public function getAttributesMeta(Type $entityType)
    {
        $meta = [];
        $attributes = $entityType->getAttributeCollection();
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            // use getDataUsingMethod, since some getters are defined and apply additional processing of returning value
            foreach ($this->metaProperties as $metaName => $origName) {
                $value = $attribute->getDataUsingMethod($origName);
                $meta[$code][$metaName] = $value;
                if ('frontend_input' === $origName) {
                    $meta[$code]['formElement'] = isset($this->formElement[$value])
                        ? $this->formElement[$value]
                        : $value;
                }
                if ($attribute->usesSource()) {
                    $meta[$code]['options'] = $attribute->getSource()->getAllOptions();
                }
            }

            $rules = $this->eavValidationRules->build($attribute, $meta[$code]);
            if (!empty($rules)) {
                $meta[$code]['validation'] = $rules;
            }

            $meta[$code]['scopeLabel'] = $this->getScopeLabel($attribute);
            $meta[$code]['componentType'] = Field::NAME;
        }

        $result = [];
        foreach ($meta as $key => $item) {
            $result[$key] = $item;
            $result[$key]['sortOrder'] = 0;
        }

        return $result;
    }

    public function getCurrentCategory()
    {
        $category = $this->registry->registry('custom_ingredient_category');
        if ($category) {
            return $category;
        }
        $requestId = $this->request->getParam($this->requestFieldName);
        $requestScope = $this->request->getParam($this->requestScopeFieldName, Store::DEFAULT_STORE_ID);
        if ($requestId) {
            $category = $this->categoryFactory->create();
            $category->setStoreId($requestScope);
            $category->load($requestId);
            if (!$category->getId()) {
                throw NoSuchEntityException::singleField('id', $requestId);
            }
        }
        return $category;
    }

    /**
     * @return array
     * @since 101.0.0
     */
    protected function getFieldsMap()
    {
        return [
            'main_fieldset' => [
                'category_name', 'url_key', 'category_image', 'position',
            ],
        ];
    }

    /**
     * Retrieve label of attribute scope
     *
     * GLOBAL | WEBSITE | STORE
     *
     * @param $attribute
     * @return string
     * @since 101.0.0
     */
    public function getScopeLabel(IngredientAttributeInterface $attribute)
    {
        $html = '';
        if (!$attribute || $this->storeManager->isSingleStoreMode()
            || $attribute->getFrontendInput() === AttributeInterface::FRONTEND_INPUT
        ) {
            return $html;
        }
        if ($attribute->isScopeGlobal()) {
            $html .= __('[GLOBAL]');
        } elseif ($attribute->isScopeWebsite()) {
            $html .= __('[WEBSITE]');
        } elseif ($attribute->isScopeStore()) {
            $html .= __('[STORE VIEW]');
        }

        return $html;
    }

    /**
     * @param Category $category
     * @return array
     * @throws LocalizedException
     */
    private function categoryImagesData($category, $categoryData)
    {
        foreach ($category->getAttributes() as $attributeCode => $attribute) {
            if (!isset($categoryData[$attributeCode])) {
                continue;
            }

            if ($attribute->getBackend() instanceof ImageBackendModel) {
                unset($categoryData[$attributeCode]);

                $fileName = $category->getData($attributeCode);
                $fileInfo = $this->fileInfo;

                if ($fileInfo->isExist($fileName)) {
                    $stat = $fileInfo->getStat($fileName);
                    $categoryData[$attributeCode][0]['name'] = $fileName;
                    $categoryData[$attributeCode][0]['url'] = $category->getImageUrl($attributeCode);
                    $categoryData[$attributeCode][0]['size'] = isset($stat) ? $stat['size'] : 0;
                    $categoryData[$attributeCode][0]['type'] = $fileInfo->getMimeType($fileName);
                }
            }
        }

        return $categoryData;
    }
}
