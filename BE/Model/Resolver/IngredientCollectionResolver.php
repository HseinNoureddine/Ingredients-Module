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


class IngredientCollectionResolver implements ResolverInterface
{
    /**
     * get ingredients
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
        $this->resourceConnection = $resourceConnection;
        $this->ingredientCollectionFactory =  $ingredientCollectionFactory;
        $this->productRepositoryInterface =  $productRepositoryInterface;
        $this->searchCriteriaBuilder =  $searchCriteriaBuilder;
        $this->urlInterface = $urlInterface;
    }

    /**
     * get products using an array of product ids
     *
     * @param Array $ids
     *
     * @return $products
     * @throws FileSystemException
     */
    public function getProducts($ids)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', $ids, 'in')->create();
        $IngredientProducts = $this->productRepositoryInterface->getList($searchCriteria)->getItems();
        $products = [];
        foreach ($IngredientProducts as $product) {
            $name = $product->getName();
            $img = $product->getImage();
            $url = $product->getProductUrl();
            array_push($products, ["id" =>$product->getId(), "name" => $name, "img" => $img, "url" => $url]);
        }
        return $products;
    }
    /**
     * get product ids using ingredient id
     *
     * @param String $id
     * @param String $ids
     * @param ResourceConnection $ids
     *
     * @return $products // array of product ids
     */
    private function getProductIds($id, $table, $connection)
    {
        $query = "SELECT product_id FROM `" . $table . "` WHERE ingredient_id = $id ";
        $result = $connection->fetchAll($query);
        $products = [];
        foreach ($result as $p) {
            array_push($products, ["id" => implode("", $p)]);
        }
        return $products;
    }
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {


        if ($args["pageSize"] == 0 || $args["pageNumber"] == 0) {
            return
                [
                    "ingredients" => [],
                    "numberOfPages" => 0,
                    "baseUrl" => $this->urlInterface->getBaseUrl()
                ];
        }
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('custom_ingredient_product');

        $collection = $this->ingredientCollectionFactory->create()
            ->addAttributeToSelect('*');
        $numberOfPages = ceil(sizeof($collection) / $args["pageSize"]);

        $collection = $this->ingredientCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->setPageSize($args["pageSize"])
            ->setCurPage($args["pageNumber"])
            ->load();



        $data = [];


        foreach ($collection as $product) {
            $ingredient = [];
            $ingredient["id"] = $product->getId();
            $ingredient["name"] = $product->getIngredientName();
            $ingredient["img"] =  $product->getIngredientImage();
            $ingredient["url"] = $product->getIngredientUrl();
            $ingredient["description"] = $product->getIngredientDescription();
            $ingredient["letter"] = $product->getLetter();

            $metaData = [];
            $metaData["metaTitle"] = $product->getMetaTitle();
            $metaData["metaKeyWords"] = $product->getMetaKeywords();
            $metaData["metaDescription"] = $product->getMetaDescription();
            $ingredient["metaData"] = $metaData;

            $ingredient["categoryID"] = $product->getCategoryId();

            $productIds = [];
            $productIds = $this->getProductIds($ingredient["id"], $table, $connection);
            $ingredient["products"] = $this->getProducts($productIds);

            array_push($data, $ingredient);
        }
        $result = [
            "ingredients" => $data,
            "numberOfPages" => (int)$numberOfPages,
            "baseUrl" => $this->urlInterface->getBaseUrl()
        ];
        return $result;
    }
}
