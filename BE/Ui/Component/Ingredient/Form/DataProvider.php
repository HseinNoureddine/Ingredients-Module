<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Ui\Component\Ingredient\Form;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Custom\Ingredients\Model\Ingredient;
use Custom\Ingredients\Model\IngredientFactory;
use Custom\Ingredients\Model\ResourceModel\Ingredient\CollectionFactory;
use Custom\Ingredients\Model\Attribute\Backend\Image as ImageBackendModel;
use Custom\Ingredients\Model\FileInfo;
use Custom\Ingredients\Model\ResourceModel\Ingredient\Collection;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Helper\Image as ImageHelper;
use Custom\Ingredients\Model\Ingredient\Attribute\ScopeOverriddenValue;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\EavValidationRules;
use Custom\Ingredients\Api\IngredientAttributeRepositoryInterface;
use Custom\Ingredients\Api\Data\IngredientAttributeInterface;
use Custom\Ingredients\Model\ResourceModel\Eav\Attribute as IngredientAttribute;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Eav\Model\Entity\Type;
use Magento\Eav\Model\Config;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Field;
use Custom\Ingredients\Model\ResourceModel\Ingredient\Attribute\CollectionFactory as AttributeCollectionFactory;

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
     * @var IngredientAttributeRepositoryInterface
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
     * @var IngredientFactory
     */
    protected $ingredientFactory;
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var Config
     */
    protected $eavConfig;
    /**
     * @var ImageHelper
     */
    protected $imageHelper;
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
     * @param IngredientFactory $ingredientFactory
     * @param FilterPool $filterPool
     * @param RequestInterface $request
     * @param Status $status
     * @param ImageHelper $imageHelper
     * @param ScopeOverriddenValue $scopeOverriddenValue
     * @param StoreManagerInterface $storeManagerInterface
     * @param IngredientAttributeRepositoryInterface $attributeRepositoryInterface
     * @param EavValidationRules $eavValidationRules
     * @param Registry $registry
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
        IngredientFactory $ingredientFactory,
        FilterPool $filterPool,
        RequestInterface $request,
        Status $status,
        ImageHelper $imageHelper,
        ScopeOverriddenValue $scopeOverriddenValue,
        StoreManagerInterface $storeManagerInterface,
        IngredientAttributeRepositoryInterface $attributeRepositoryInterface,
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
        $this->ingredientFactory = $ingredientFactory;
        $this->filterPool = $filterPool;
        $this->request = $request;
        $this->status = $status;
        $this->imageHelper = $imageHelper;
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

    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta = $this->prepareMeta($meta);
        $ingredient = $this->getCurrentIngredient();
        if ($ingredient) {
            $meta = $this->addUseDefaultValueCheckbox($ingredient, $meta);
        }
        return $meta;
    }

    private function addUseDefaultValueCheckbox(Ingredient $ingredient, array $meta)
    {
        foreach ($ingredient->getAttributes() as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $canDisplayUseDefault = $attribute->getScope() != IngredientAttribute::SCOPE_GLOBAL_TEXT
                && $ingredient->getId()
                && $ingredient->getStoreId();
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
                        $ingredient,
                        $attributeCode,
                        $this->request->getParam($this->requestScopeFieldName, Store::DEFAULT_STORE_ID)
                    )
                ]
            );
        }

        return $meta;
    }

    public function prepareMeta($meta)
    {
        $meta = array_replace_recursive(
            $meta, 
            $this->prepareFieldsMeta(
                $this->getFieldsMap(),
                $this->getAttributesMeta($this->eavConfig->getEntityType('custom_ingredient'))
            )
        );
        return $meta;
    }

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

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $ingredient = $this->getCurrentIngredient();
        if ($ingredient) {
            $ingredientData = $ingredient->getData();
            $ingredientData = $this->ingredientImagesData($ingredient, $ingredientData);
            $ingredientData = $this->ingredientProductData($ingredient, $ingredientData);

            $this->loadedData[$ingredient->getId()] = $ingredientData;
        }

        return $this->loadedData;
    }

    public function getAttributesMeta(Type $entityType)
    {
        $meta = [];
        $attributes = $entityType->getAttributeCollection();
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
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

    public function getCurrentIngredient()
    {
        $ingredient = $this->registry->registry('custom_ingredient');
        if ($ingredient) {
            return $ingredient;
        }
        $requestId = $this->request->getParam($this->requestFieldName);
        $requestScope = $this->request->getParam($this->requestScopeFieldName, Store::DEFAULT_STORE_ID);
        if ($requestId) {
            $ingredient = $this->ingredientFactory->create();
            $ingredient->setStoreId($requestScope);
            $ingredient->load($requestId);
            if (!$ingredient->getId()) {
                throw NoSuchEntityException::singleField('id', $requestId);
            }
        }
        return $ingredient;
    }

    protected function getFieldsMap()
    {
        return [
            'main_fieldset' => [
                'category_id',
                'is_active',
                'ingredient_name',
                'letter',
                'url_key',
                'ingredient_image',
                'featured',
                'description',
            ],
            'meta_data' => [
                'meta_title',
                'meta_description',
                'meta_keywords',
            ],
            'products' => [],
        ];
    }

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

    private function ingredientImagesData($ingredient, $ingredientData)
    {
        foreach ($ingredient->getAttributes() as $attributeCode => $attribute) {
            if (!isset($ingredientData[$attributeCode])) {
                continue;
            }

            if ($attribute->getBackend() instanceof ImageBackendModel) {
                unset($ingredientData[$attributeCode]);

                $fileName = $ingredient->getData($attributeCode);
                $fileInfo = $this->fileInfo;

                if ($fileInfo->isExist($fileName)) {
                    $stat = $fileInfo->getStat($fileName);
                    $ingredientData[$attributeCode][0]['name'] = $fileName;
                    $ingredientData[$attributeCode][0]['url'] = $ingredient->getImageUrl($attributeCode);
                    $ingredientData[$attributeCode][0]['size'] = isset($stat) ? $stat['size'] : 0;
                    $ingredientData[$attributeCode][0]['type'] = $fileInfo->getMimeType($fileName);
                }
            }
        }
        return $ingredientData;
    }

    private function ingredientProductData($ingredient, $ingredientData)
    {
        $ingredientData['links']['products'] = [];
        foreach ($ingredient->getIngredientProducts() as $product) {
            $ingredientData['links']['products'][] = [
                'id'   => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'status' => $this->status->getOptionText($product->getStatus()),
                'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
            ];
        }
        return $ingredientData;
    }
}
