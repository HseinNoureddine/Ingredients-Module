<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Controller\Category;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Custom\Ingredients\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\ForwardFactory;

class View extends Action
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
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
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ForwardFactory $resultForwardFactory
     * @param Registry $registry
     * @param Context $context
     *
     * @return $this
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        ForwardFactory $resultForwardFactory,
        Registry $registry,
        Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    private function initCategory()
    {
        $id = $this->getRequest()->getParam('entity_id');
        if (!$id) {
            return false;
        }

        try {
            $category = $this->categoryRepository->getById($id, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        $this->registry->register('current_ingredient_category', $category);

        return $category;
    }

    public function execute()
    {
        if ($this->initCategory()) {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}
