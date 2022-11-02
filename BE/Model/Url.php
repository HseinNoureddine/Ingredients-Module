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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Url
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlManager;
    /**
     * @var Config
     */
    protected $config;

    /**
     * Url constructor.
     * @param Config $config
     * @param \Magento\Framework\UrlInterface $urlManager
     */
    public function __construct(
        Config $config,
        \Magento\Framework\UrlInterface $urlManager
    ) {
        $this->config = $config;
        $this->urlManager = $urlManager;
    }
    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlManager->getUrl($this->config->getBaseRoute());
    }

    /**
     * @param Ingredient $ingredient
     * @param bool $useSid
     * @return string
     */
    public function getIngredientUrl($ingredient, $useSid = true)
    {
        return $this->getUrl('/' . $ingredient->getUrlKey(), 'ingredient', ['_nosid' => !$useSid]);
    }

    /**
     * @param Category $category
     * @param bool $useSid
     * @return string
     */
    public function getCategoryUrl($category, $useSid = true)
    {
        return $this->getUrl('/category/' . $category->getUrlKey(), 'category', ['_nosid' => !$useSid]);
    }

    /**
     * @param string $route
     * @param string $type
     * @param array  $urlParams
     * @return string
     */
    private function getUrl($route, $type, $urlParams = [])
    {
        $url = $this->urlManager->getUrl($this->config->getBaseRoute() . $route, $urlParams);
        if ($type == 'ingredient' && $this->config->getIngredientUrlSuffix()) {
            $url = $this->addSuffix($url, $this->config->getIngredientUrlSuffix());
        }
        if ($type == 'category' && $this->config->getCategoryUrlSuffix()) {
            $url = $this->addSuffix($url, $this->config->getCategoryUrlSuffix());
        }
        return $url;
    }

    /**
     * @param string $url
     * @param string $suffix
     * @return string
     */
    private function addSuffix($url, $suffix)
    {
        if (strpos($suffix, '.') === false) {
            $suffix = '.' . $suffix;
        }
        $parts    = explode('?', $url, 2);
        $parts[0] = rtrim($parts[0], '/') . $suffix;
        return implode('?', $parts);
    }

    /**
     * Return url without suffix
     * @param string $key
     * @return string
     */
    public function trimSuffix($key)
    {
        $suffix = $this->config->getCategoryUrlSuffix();
        //user can enter .html or html suffix
        if ($suffix != '' && $suffix[0] != '.') {
            $suffix = '.' . $suffix;
        }
        $key = str_replace($suffix, '', $key);
        $suffix = $this->config->getIngredientUrlSuffix();
        //user can enter .html or html suffix
        if ($suffix != '' && $suffix[0] != '.') {
            $suffix = '.' . $suffix;
        }
        $key = str_replace($suffix, '', $key);
        return $key;
    }
}
