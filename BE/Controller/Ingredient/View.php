<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Controller\Ingredient;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Custom\Ingredients\Api\IngredientRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\ForwardFactory;

class View extends Action
{
    /**
     * @var IngredientRepositoryInterface
     */
    private $ingredientRepository;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Context
     */
    private $context;
    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     *
     * @param StoreManagerInterface $storeManager
     * @param IngredientRepositoryInterface $ingredientRepository
     * @param ForwardFactory $resultForwardFactory
     * @param Registry $registry
     * @param Context $context
     *
     * @return $this
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        IngredientRepositoryInterface $ingredientRepository,
        ForwardFactory $resultForwardFactory,
        Registry $registry,
        Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->ingredientRepository = $ingredientRepository;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    private function initIngredient()
    {
        $id = $this->getRequest()->getParam('entity_id');
        if (!$id) {
            return false;
        }
        try {
            $ingredient = $this->ingredientRepository->getById($id, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        $this->registry->register('current_ingredient', $ingredient);

        return $ingredient;
    }

    public function execute()
    {
        if ($this->initIngredient()) {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}
