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

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getIngredientBaseMetaTitle()
    {
        return $this->scopeConfig->getValue(
            'ingredients/ingredient/ingredient_base_meta_title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getIngredientBaseMetaDescription()
    {
        return $this->scopeConfig->getValue(
            'ingredients/ingredient/ingredient_base_meta_description',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getIngredientBaseMetaKeywords()
    {
        return $this->scopeConfig->getValue(
            'ingredients/ingredient/ingredient_base_meta_keywords',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getBaseRoute()
    {
        return $this->scopeConfig->getValue(
            'ingredients/ingredient/base_route',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ?: 'ingredients';
    }

    /**
     * @return string
     */
    public function getIngredientUrlSuffix()
    {
        return $this->scopeConfig->getValue(
            'ingredients/ingredient/ingredient_url_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getCategoryUrlSuffix()
    {
        return $this->scopeConfig->getValue(
            'ingredients/ingredient/category_url_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
