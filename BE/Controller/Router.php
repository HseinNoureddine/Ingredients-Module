<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Custom\Ingredients\Model\Url;
use Custom\Ingredients\Model\Config;
use Custom\Ingredients\Model\ResourceModel\Ingredient\CollectionFactory as IngredientCollection;
use Custom\Ingredients\Model\ResourceModel\Category\CollectionFactory as CategoryCollection;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;
    /**
     * @var EventManagerInterface
     */
    private $eventManager;
    /**
     * @var Url
     */
    private $url;

    private $config;

    /**
     * @var IngredientCollection
     */
    protected $ingredientCollection;
    /**
     * @var CategoryCollection
     */
    protected $categoryCollection;

    /**
     *
     * @param Config $config
     * @param Url $url
     * @param IngredientCollection $ingredientCollection
     * @param CategoryCollection $categoryCollection
     * @param ActionFactory $actionFactory
     * @param EventManagerInterface $eventManager
     *
     * @return $this
     */
    public function __construct(
        Config $config,
        Url $url,
        IngredientCollection $ingredientCollection,
        CategoryCollection $categoryCollection,
        ActionFactory $actionFactory,
        EventManagerInterface $eventManager
    ) {
        $this->config = $config;
        $this->url = $url;
        $this->ingredientCollection = $ingredientCollection;
        $this->categoryCollection = $categoryCollection;
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
    }
    /**
     * @param RequestInterface $request
     * @return null
     */
    public function match(RequestInterface $request)
    {
        $urlKey = trim($request->getPathInfo(), '/');
        $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
        $this->eventManager->dispatch('custom_ingredients_controller_router_match_before', [
            'router' => $this,
            'condition' => $condition
        ]);

        $pathInfo = $request->getPathInfo();
        $identifier = trim($pathInfo, '/');
        $parts      = explode('/', $identifier);
        if (count($parts) >= 1) {
            $parts[count($parts) - 1] = $this->url->trimSuffix($parts[count($parts) - 1]);
        }
        if ($parts[0] != $this->config->getBaseRoute()) {
            return false;
        }
        if (count($parts) > 1) {
            unset($parts[0]);
            $parts  = array_values($parts);
            $urlKey = implode('/', $parts);
            $urlKey = urldecode($urlKey);
            $urlKey = $this->url->trimSuffix($urlKey);
        } else {
            $urlKey = '';
        }

        $success = false;

        if ($urlKey == '') {
            $request
                ->setModuleName('ingredients')
                ->setControllerName('ingredient')
                ->setActionName('index');
            $success = true;
        }

        if ($parts[0] == 'category' && isset($parts[1])) {
            $category = $this->categoryCollection->create()
                ->addFieldToFilter('url_key', $parts[1])
                ->setPageSize(1)
                ->getFirstItem();

            if ($category->getId()) {
                $request
                    ->setModuleName('ingredients')
                    ->setControllerName('category')
                    ->setActionName('view')
                    ->setParam('entity_id', $category->getId());
                $success = true;
            } else {
                return false;
            }
        }

        $ingredient = $this->ingredientCollection->create()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('url_key', $urlKey)
            ->setPageSize(1)
            ->getFirstItem();

        if ($ingredient->getId()) {
            $request
                ->setModuleName('ingredients')
                ->setControllerName('ingredient')
                ->setActionName('view')
                ->setParam('entity_id', $ingredient->getId());
            $success = true;
        }

        if (!$success) {
            return false;
        }

        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
