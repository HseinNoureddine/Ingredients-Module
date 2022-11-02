<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Observer;

use Custom\Ingredients\Model\ResourceModel\Ingredient as IngredientResource;

class IngredientProductSave implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var IngredientResource
     */
    private $ingredientResource;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     *
     * @param IngredientResource $ingredientResource
     * @param RequestInterface $request
     *
     * @return $this
     */
    public function __construct(
        IngredientResource $ingredientResource,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->ingredientResource = $ingredientResource;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $ingredientIds = [];
        if ($data = $this->request->getParams()) {
            if (isset($data['links']['ingredients'])) {
                foreach ($data['links']['ingredients'] as $item) {
                    $ingredientIds[] = $item['id'];
                }
                $this->ingredientResource->saveProductIngredientIds($ingredientIds, $product);
            }
        }
    }
}
