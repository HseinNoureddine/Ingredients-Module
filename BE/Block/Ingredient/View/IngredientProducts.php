<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Block\Ingredient\View;

use Magento\Framework\Registry;

class IngredientProducts extends \Magento\Catalog\Block\Product\View
{
    /**
     *
     * @var Registry
     */
    protected $registry;

    /**
     *
     * @param Context $context
     * @param EncoderInterface $urlEncoder
     * @param StringUtils $string
     * @param Product $productHelper
     * @param ConfigInterface $string
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     *
     * @return $this
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->registry = $context->getRegistry();
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency
        );
    }

    public function getIngredientProducts()
    {
        return $this->getCurrentIngredient()->getIngredientProducts();
    }

    public function getCurrentIngredient()
    {
        return $this->registry->registry('current_ingredient');
    }
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getIngredientProducts() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        return $identities;
    }
}
