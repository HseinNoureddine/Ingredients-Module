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

use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class IngredientProductDataProvider extends ProductDataProvider
{
    public function getCollection()
    {
        $collection = parent::getCollection();
        return $collection->addAttributeToSelect('status');
    }
}
