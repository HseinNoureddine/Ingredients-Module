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
use Custom\Ingredients\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Custom\Ingredients\Block\Ingredient\IngredientList;

class CategoriesResolver implements ResolverInterface
{

    /**
     * get ingredient categories
     *
     * @var CategoryCollectionFactory
     */
    protected $categoryCollection;

    /**
     * @param CategoryCollectionFactory $categoryCollection
     *
     * @return $this
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollection
    ) {
        $this->categoryCollection = $categoryCollection;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {

        $data = [];
        $data["categories"] = [];
        $categories = $this->categoryCollection->create()
            ->addAttributeToSelect(['category_name', 'category_image', 'url_key', 'category_id'])
            ->addAttributeToSort('position', 'DESC');

        foreach ($categories as $category) {
            $c = [];
            $c["name"] = $category->getCategoryName() . "";
            $c["image"] = $category->getImageUrl() . "";
            $c["url"] = $category->getCategoryUrl() . "";
            $c["id"] = $category->getId() . "";
            array_push($data["categories"], $c);
        }
        return $data;
    }
}
