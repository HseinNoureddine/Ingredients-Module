<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);

namespace Custom\Ingredients\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Custom\Ingredients\Model\ResourceModel\Ingredient\CollectionFactory as IngredientCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\UrlInterface;
use Magento\Framework\Api\Filter;
class IngredientResolver implements ResolverInterface
{
    /**
     * get ingredient
     *
     * @var IngredientCollectionFactory
     */
    protected $ingredientCollectionFactory;
    /**
     * get connection
     *
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * get related products
     *
     * @var ProductRepositoryInterface
     */
    protected $productRepositoryInterface;
    /**
     * filter specific products
     *
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * get baseUrl
     *
     * @var UrlInterface
     */
    protected $urlInterface;


    /**
     * @param IngredientCollectionFactory
     * @param ResourceConnection
     * @param ProductRepositoryInterface
     * @param SearchCriteriaBuilder
     * @param UrlInterface
     */
    public function __construct(
        IngredientCollectionFactory $ingredientCollectionFactory,
        ResourceConnection $resourceConnection,
        ProductRepositoryInterface $productRepositoryInterface,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        UrlInterface $urlInterface
    ) {
        $this->ingredientCollectionFactory =  $ingredientCollectionFactory;
        $this->resourceConnection =  $resourceConnection;
        $this->productRepositoryInterface =  $productRepositoryInterface;
        $this->searchCriteriaBuilder =  $searchCriteriaBuilder;
        $this->urlInterface =  $urlInterface;
    }

    /**
     * get ingredient products in a specific range according to page size and number using ingredient id
     *
     * @param String $ingredient
     * @param Int $pageSize
     * @param Int $pageNumber
     *
     * @return $products
     */
    public function getProducts($ingredient, $pageSize, $pageNumber)
    {
        $productIds = $this->getProductIds($ingredient, $pageSize, $pageNumber);
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', $productIds, 'in')->create();
        $IngredientProducts = $this->productRepositoryInterface->getList($searchCriteria)->getItems();
        $products = [];
        foreach ($IngredientProducts as $product) {
            $name = $product->getName();
            $img = $product->getImage();
            $url = $product->getProductUrl();
            array_push($products, ["id" => $product->getId(), "name" => $name, "img" => $img, "url" => $url]);
        }
        return $products;
    }
    /**
     * get products ids related to specific ingredient using ingredient id
     *
     * @param String $ingredient
     * @param Int $pageSize
     * @param Int $pageNumber
     *
     * @return $products
     */
    private function getProductIds($id, $pageSize, $pageNumber)
    {
        $upperLimit = $pageSize * $pageNumber;
        $lowerLimit = $upperLimit - $pageSize;
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('custom_ingredient_product');
        $query = "SELECT product_id FROM `" . $table . "` WHERE ingredient_id = $id limit $lowerLimit,$upperLimit ";
        $result = $connection->fetchAll($query);
        $products = [];
        foreach ($result as $p) {
            array_push($products, ["id" => implode("", $p)]);
        }
        return $products;
    }
    /**
     * gets the number of pages needed for products related to an ingredient
     *
     * @param String $id
     * @param Int $pageSize
     *
     * @return $ceil(sizeof($result) / $pageSize) // number of pages as int
     */
    private function numberOfProductPages($id, $pageSize)
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('custom_ingredient_product');
        $query = "SELECT product_id FROM `" . $table . "` WHERE ingredient_id = $id";
        $result = $connection->fetchAll($query);
        return ceil(sizeof($result) / $pageSize);
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {

        $ingredient = [];
        $id = $args["id"];
        $pageNumber = $args["pageNumber"];
        $pageSize = $args["pageSize"];
        if (!is_numeric($id)) {
            $connection = $this->resourceConnection->getConnection();
            $table = $connection->getTableName('custom_ingredient_entity_varchar');
            $query = "SELECT entity_id FROM `" . $table . "` WHERE value = '" . $id . "' ";
            $result = $connection->fetchAll($query);
            $id = implode("", $result[0]);
        }
        $collection = $this->ingredientCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', $id);

        foreach ($collection as $product) {
            $ingredient["id"] = $product->getId();
            $ingredient["name"] = $product->getIngredientName();
            $ingredient["img"] =  $product->getIngredientImage();
            $ingredient["url"] = $product->getIngredientUrl();
            $ingredient["description"] = $product->getIngredientDescription();
            $ingredient["baseUrl"] = $this->urlInterface->getBaseUrl();
            $ingredient["letter"] = $product->getLetter();

            $metaData = [];
            $metaData["metaTitle"] = $product->getMetaTitle();
            $metaData["metaKeyWords"] = $product->getMetaKeywords();
            $metaData["metaDescription"] = $product->getMetaDescription();
            $ingredient["metaData"] = $metaData;

            $ingredient["categoryID"] = $product->getCategoryId();
            $ingredient["numberOfProductPages"] = $this->numberOfProductPages($id, $pageSize);
            $ingredient["products"] = $this->getProducts($id, $pageSize, $pageNumber);
            break;
        }
        return $ingredient;
    }
}
